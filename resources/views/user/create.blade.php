@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah User</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hubungkan dengan Pegawai</label>
                            <select name="pegawai_id" id="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawai as $p)
                                    <option value="{{ $p->id }}" data-nip="{{ $p->nip }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="nip-display" class="form-text text-muted d-none">NIP: <strong id="nip-value"></strong></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password-input" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="password-hint" class="form-text text-muted d-none"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-block">Roles</label>
                            @foreach($roles as $role)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                                <label class="form-check-label" for="role_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                            </div>
                            @endforeach
                            @error('roles')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Pegawai --',
            allowClear: true
        });

        // Update NIP display when pegawai is selected
        $('#pegawai_id').on('change', function() {
            const nip = $(this).find('option:selected').data('nip');
            if (nip) {
                $('#nip-display').removeClass('d-none');
                $('#nip-value').text(nip);
            } else {
                $('#nip-display').addClass('d-none');
            }
            updatePasswordHint();
        });

        // Update password hint when roles are changed
        $('input[name="roles[]"]').on('change', function() {
            updatePasswordHint();
        });

        function updatePasswordHint() {
            const isKepalaRuangan = $('input[name="roles[]"][value="kepala_ruangan"]').is(':checked');
            const nip = $('#pegawai_id').find('option:selected').data('nip');
            
            if (isKepalaRuangan && nip) {
                $('#password-hint').removeClass('d-none').html(`<span class="text-info">💡 Password untuk Kepala Ruangan akan otomatis diisi dengan NIP: <strong>${nip}</strong></span>`);
                $('#password-input').prop('disabled', true).val('(NIP akan digunakan)');
            } else {
                $('#password-hint').addClass('d-none');
                $('#password-input').prop('disabled', false).val('');
            }
        }

        // Check initial state
        updatePasswordHint();
    });
</script>
@endpush
