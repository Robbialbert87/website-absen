<?php

namespace App\Http\Controllers;

use App\Models\JadwalPegawai;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CutiExport;

class DataCutiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $ruangan_id = $request->get('ruangan_id');
        $shift_id = $request->get('shift_id');
        $search = $request->get('search');

        $user = auth()->user();
        
        $query = JadwalPegawai::with(['pegawai.ruangan', 'shift'])
            ->whereHas('shift', function($q) {
                $q->where('kategori_jadwal', 'cuti')
                  ->orWhere('nama_shift', 'like', '%cuti%')
                  ->orWhere('kode_shift', 'like', '%cuti%');
            });

        // Date Filter
        if ($bulan && $tahun) {
            $query->whereMonth('tanggal_masuk', $bulan)
                  ->whereYear('tanggal_masuk', $tahun);
        }

        // Ruangan Filter
        if ($ruangan_id && $ruangan_id !== 'all') {
            $query->where('ruangan_id', $ruangan_id);
        } elseif (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            // Limited access for non-admin
            $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                ->orWhere('id', $user->ruangan_id)
                ->pluck('id')
                ->toArray();
            $query->whereIn('ruangan_id', $allowedRoomIds);
        }

        // Shift Filter
        if ($shift_id && $shift_id !== 'all') {
            $query->where('shift_id', $shift_id);
        }

        // Search Filter
        if ($search) {
            $query->whereHas('pegawai', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $allRecords = $query->orderBy('pegawai_id')
                            ->orderBy('tanggal_masuk')
                            ->get();

        $groupedData = $this->groupConsecutiveLeave($allRecords);

        // Pagination manually for grouped data
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        
        $items = array_slice($groupedData, $offset, $perPage);
        $total = count($groupedData);
        
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, 
            $total, 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $ruangans = Ruangan::all();
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            $ruangans = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                ->orWhere('id', $user->ruangan_id)
                ->get();
        }

        $cutiShifts = Shift::where('kategori_jadwal', 'cuti')
            ->orWhere('nama_shift', 'like', '%cuti%')
            ->orWhere('kode_shift', 'like', '%cuti%')
            ->get();

        return view('cuti.index', compact('paginatedItems', 'ruangans', 'cutiShifts', 'bulan', 'tahun', 'ruangan_id', 'shift_id', 'search'));
    }

    private function groupConsecutiveLeave($records)
    {
        $grouped = [];
        if ($records->isEmpty()) return $grouped;

        $currentGroup = null;

        foreach ($records as $record) {
            $date = Carbon::parse($record->tanggal_masuk);
            
            if ($currentGroup && 
                $currentGroup['pegawai_id'] == $record->pegawai_id && 
                $currentGroup['shift_id'] == $record->shift_id &&
                Carbon::parse($currentGroup['end_date'])->addDay()->format('Y-m-d') == $date->format('Y-m-d')
            ) {
                // Continue current group
                $currentGroup['end_date'] = $date->format('Y-m-d');
                $currentGroup['total_days']++;
            } else {
                // Save previous group if exists
                if ($currentGroup) {
                    $grouped[] = $currentGroup;
                }
                
                // Start new group
                $currentGroup = [
                    'pegawai_id' => $record->pegawai_id,
                    'nip' => $record->pegawai->nip,
                    'nama_pegawai' => $record->pegawai->nama,
                    'ruangan' => $record->pegawai->ruangan->nama_ruangan ?? '-',
                    'shift_id' => $record->shift_id,
                    'jenis_cuti' => $record->shift->nama_shift,
                    'start_date' => $date->format('Y-m-d'),
                    'end_date' => $date->format('Y-m-d'),
                    'total_days' => 1,
                    'keterangan' => $record->keterangan ?? '-',
                    'warna' => $record->shift->warna
                ];
            }
        }

        if ($currentGroup) {
            $grouped[] = $currentGroup;
        }

        return $grouped;
    }

    public function export(Request $request)
    {
        return Excel::download(new CutiExport($request), 'data_pegawai_cuti_' . date('Ymd_His') . '.xlsx');
    }
}
