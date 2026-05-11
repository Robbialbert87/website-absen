@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Import Ruangan (Excel)</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Instruksi:</h6>
                        <ol class="small text-muted">
                            <li>Download template Excel/CSV <a href="{{ route('ruangan.template.download') }}">di sini</a>.</li>
                            <li>Isi data ruangan sesuai format kolom (kode_ruangan, nama_ruangan, keterangan, kepala_nip).</li>
                            <li>Simpan file dalam format .xlsx, .xls, atau .csv.</li>
                            <li>Jika `kode_ruangan` sudah ada, data ruangan akan diperbarui.</li>
                            <li>`kepala_nip` adalah NIP pegawai yang ditunjuk sebagai kepala ruangan (opsional).</li>
                        </ol>
                    </div>

                    <form action="{{ route('ruangan.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Pilih File</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('ruangan.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-import me-1"></i> Mulai Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
