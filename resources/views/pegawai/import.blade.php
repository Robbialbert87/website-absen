@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Import Pegawai (Excel)</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Instruksi:</h6>
                        <ol class="small text-muted">
                            <li>Download template Excel <a href="{{ route('pegawai.template.download') }}">di sini</a>.</li>
                            <li>Isi data pegawai sesuai format kolom (nip, nama, ruangan, jabatan, status_aktif).</li>
                            <li>Simpan file dalam format .xlsx atau .xls.</li>
                            <li>Jika nama ruangan baru ditemukan, sistem akan otomatis membuatnya.</li>
                            <li>Jika NIP sudah ada, data pegawai akan di-update (Upsert).</li>
                        </ol>
                    </div>

                    <form action="{{ route('pegawai.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Pilih File Excel</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pegawai.index') }}" class="btn btn-light border">Batal</a>
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
