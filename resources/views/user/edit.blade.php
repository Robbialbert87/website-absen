@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hubungkan dengan Pegawai</label>
                            <select name="pegawai_id" id="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawai as $p)
                                    <option value="{{ $p->id }}" data-nip="{{ $p->nip }}" {{ old('pegawai_id', $user->pegawai_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="nip-display" class="form-text text-muted">NIP: <strong id="nip-value">{{ $user->pegawai->nip ?? '-' }}</strong></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Kosongkan jika tidak diganti)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password-input">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="mb-3" id="reset-nip-section" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reset_password_to_nip" id="reset_password_to_nip" value="1">
                                <label class="form-check-label" for="reset_password_to_nip">
                                    Reset Password ke NIP (<span id="nip-for-reset">{{ $user->pegawai->nip ?? '-' }}</span>)
                                </label>
                            </div>
                            <small class="form-text text-muted d-block mt-1">Centang ini untuk mengganti password dengan NIP Pegawai</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-block">Roles</label>
                            @foreach($roles as $role)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                            </div>
                            @endforeach
                            @error('roles')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Perbarui User</button>
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
                $('#nip-value').text(nip);
                $('#nip-for-reset').text(nip);
            }
            updateResetNipSection();
        });

        // Update reset NIP section visibility when roles are changed
        $('input[name="roles[]"]').on('change', function() {
            updateResetNipSection();
        });

        function updateResetNipSection() {
            const isKepalaRuangan = $('input[name="roles[]"][value="kepala_ruangan"]').is(':checked');
            
            if (isKepalaRuangan) {
                $('#reset-nip-section').show();
            } else {
                $('#reset-nip-section').hide();
                $('#reset_password_to_nip').prop('checked', false);
            }
        }

        // Check initial state
        updateResetNipSection();
    });
</script>
@endpush
