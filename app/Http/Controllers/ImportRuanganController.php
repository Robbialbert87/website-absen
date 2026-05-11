<?php

namespace App\Http\Controllers;

use App\Imports\RuanganImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportRuanganController extends Controller
{
    public function index()
    {
        return view('ruangan.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new RuanganImport, $request->file('file'));
            return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Gagal import pada baris: ';
            foreach ($failures as $failure) {
                $errorMsg .= $failure->row() . ' (' . implode(', ', $failure->errors()) . '). ';
            }
            return back()->with('error', $errorMsg);
        } catch (\Exception $e) {
            \Log::error('Import Ruangan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_ruangan.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['kode_ruangan', 'nama_ruangan', 'keterangan', 'kepala_nip'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['UGD', 'Unit Gawat Darurat', 'Ruangan emergency', '197001']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
