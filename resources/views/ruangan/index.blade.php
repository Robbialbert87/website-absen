@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Ruangan</h1>
        <div>
            <a href="{{ route('ruangan.import.index') }}" class="btn btn-success me-2">
                <i class="fas fa-file-import me-1"></i> Import Excel
            </a>
            <a href="{{ route('ruangan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Ruangan
            </a>
        </div>
    </div> --}}

        <x-filter-card title="Master Ruangan" createRoute="{{ route('ruangan.create') }}" createText="Tambah Ruangan"
            exportExcel="{{ route('ruangan.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            exportPdf="{{ route('ruangan.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari Kode, Nama Ruangan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="kepala_pegawai_id" class="form-select form-select-sm">
                    <option value="">Semua Kepala Ruangan</option>
                    @foreach ($kepalaRuangan as $kepala)
                        <option value="{{ $kepala->id }}"
                            {{ request('kepala_pegawai_id') == $kepala->id ? 'selected' : '' }}>{{ $kepala->nama }}</option>
                    @endforeach
                </select>
            </div>
        </x-filter-card>

        @include('ruangan._table')
    </div>

    @push('scripts')
        <script src="{{ asset('js/datatable.js') }}"></script>
        <script>
            $(document).ready(function() {
                const modalAddPegawai = new bootstrap.Modal(document.getElementById('modalAddPegawai'));
                const modalShowPegawai = new bootstrap.Modal(document.getElementById('modalShowPegawai'));

                // --- Lihat Pegawai ---
                $(document).on('click', '.btn-show-pegawai', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    
                    $('#showModalRoomName').text(name);
                    $('#show_room_id').val(id);
                    
                    $('#listPegawaiBody').html('<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin me-1"></i> Memuat data...</td></tr>');
                    modalShowPegawai.show();

                    fetchRoomEmployees(id);
                });

                function fetchRoomEmployees(roomId) {
                    const url = "{{ route('ruangan.show-pegawai', ':id') }}".replace(':id', roomId);
                    $.get(url, function(response) {
                        let rows = '';
                        if (response.pegawais.length === 0) {
                            rows = '<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada pegawai di ruangan ini.</td></tr>';
                        } else {
                            response.pegawais.forEach((p, index) => {
                                rows += `
                                    <tr>
                                        <td class="text-center small">${index + 1}</td>
                                        <td>
                                            <div class="fw-bold">${p.nama}</div>
                                            <div class="small text-muted">${p.nip}</div>
                                        </td>
                                        <td class="small">${p.jabatan || '-'}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger btn-remove-pegawai" 
                                                    data-id="${p.id}" 
                                                    data-room-id="${roomId}"
                                                    title="Keluarkan dari ruangan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#listPegawaiBody').html(rows);
                    });
                }

                $(document).on('click', '.btn-remove-pegawai', function() {
                    const pegawaiId = $(this).data('id');
                    const roomId = $(this).data('room-id');
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Pegawai akan dikeluarkan dari ruangan ini.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Keluarkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const url = "{{ route('ruangan.remove-pegawai', [':roomId', ':pegawaiId']) }}"
                                .replace(':roomId', roomId)
                                .replace(':pegawaiId', pegawaiId);

                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: { _token: '{{ csrf_token() }}' },
                                success: function(response) {
                                    Swal.fire('Berhasil', response.message, 'success');
                                    fetchRoomEmployees(roomId);
                                },
                                error: function() {
                                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengeluarkan pegawai.', 'error');
                                }
                            });
                        }
                    });
                });

                // --- Tambah Pegawai ---
                $(document).on('click', '.btn-add-pegawai', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    
                    $('#modalRoomName').text(name);
                    $('#room_id').val(id);
                    
                    // Reset and show loading
                    $('#pegawai_ids').empty().prop('disabled', true);
                    $('#btnSavePegawai').prop('disabled', true);
                    
                    modalAddPegawai.show();

                    // Fetch employees
                    const url = "{{ route('ruangan.add-pegawai', ':id') }}".replace(':id', id);
                    $.get(url, function(response) {
                        let options = '';
                        response.pegawais.forEach(p => {
                            options += `<option value="${p.id}">${p.nama} (${p.nip})</option>`;
                        });
                        $('#pegawai_ids').html(options).prop('disabled', false).select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalAddPegawai'),
                            placeholder: 'Pilih satu atau lebih pegawai',
                            allowClear: true
                        });
                        $('#btnSavePegawai').prop('disabled', false);
                    });
                });

                $('#btnSavePegawai').on('click', function() {
                    const id = $('#room_id').val();
                    const pegawaiIds = $('#pegawai_ids').val();
                    
                    if (!pegawaiIds || pegawaiIds.length === 0) {
                        Swal.fire('Peringatan', 'Pilih minimal satu pegawai', 'warning');
                        return;
                    }

                    const url = "{{ route('ruangan.store-pegawai', ':id') }}".replace(':id', id);
                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            pegawai_ids: pegawaiIds
                        },
                        success: function(response) {
                            modalAddPegawai.hide();
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).text('Simpan');
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                        }
                    });
                });
            });
        </script>
    @endpush

    <!-- Modal Lihat Pegawai -->
    <div class="modal fade" id="modalShowPegawai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold" style="color: #1A7A6E;">Daftar Pegawai di <span id="showModalRoomName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" id="show_room_id">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Pegawai</th>
                                    <th>Jabatan</th>
                                    <th class="text-center" width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listPegawaiBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pegawai -->
    <div class="modal fade" id="modalAddPegawai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold" style="color: #1A7A6E;">Tambah Pegawai ke <span id="modalRoomName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" id="room_id">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Pilih Pegawai</label>
                        <select id="pegawai_ids" class="form-select" multiple style="width: 100%">
                            <!-- Loaded via AJAX -->
                        </select>
                        <div class="form-text mt-2 small">
                            <i class="fas fa-info-circle me-1"></i> Hanya menampilkan pegawai yang belum berada di ruangan ini.
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" id="btnSavePegawai" class="btn btn-primary py-2 fw-bold" style="border-radius: 12px;">
                            Simpan
                        </button>
                        <button type="button" class="btn btn-light py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
