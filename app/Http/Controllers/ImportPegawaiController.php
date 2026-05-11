<?php

namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportPegawaiController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        return view('pegawai.import');
    }

    public function import(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new EmployeesImport, $request->file('file'));
            return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Simple CSV template generation for now, or just return a static file path if it exists
        // For this task, I'll return a downloadable response with headers for a basic Excel structure
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_pegawai.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['nip', 'nama', 'ruangan', 'jabatan', 'kategori_kerja', 'status_aktif'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['197001', 'Robbi Albert', 'UGD', 'Perawat', 'non_shift', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
