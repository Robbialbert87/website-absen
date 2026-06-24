@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-picker {
        height: 380px;
        border-radius: 12px;
        border: 2px solid #dee2e6;
        display: none;
        margin-top: 10px;
        z-index: 0;
    }
    #map-picker.visible { display: block; }
    .map-tip {
        background: #e8f4f2;
        border-left: 4px solid #1A7A6E;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #1A7A6E;
        margin-bottom: 10px;
        display: none;
    }
    .map-tip.visible { display: block; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Kegiatan Baru</h1>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('kegiatan.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" required value="{{ old('tanggal_kegiatan') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required value="{{ old('jam_mulai') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required value="{{ old('jam_selesai') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Lokasi (Nama Tempat)</label>
                        <input type="text" name="lokasi" class="form-control" required value="{{ old('lokasi', 'RSUD H. Abdul Manap Kota Jambi') }}">
                    </div>
                </div>

                {{-- === BAGIAN PETA & KOORDINAT === --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Titik Lokasi Kegiatan</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" id="btn-open-map" class="btn btn-info text-white btn-sm rounded-pill shadow-sm">
                            <i class="fas fa-map-marked-alt"></i> Pilih Lokasi di Peta
                        </button>
                        <a href="#" id="btn-view-gmaps" target="_blank" class="btn btn-outline-success btn-sm rounded-pill d-none">
                            <i class="fas fa-external-link-alt"></i> Lihat di Google Maps
                        </a>
                    </div>

                    <div class="map-tip" id="map-tip">
                        <i class="fas fa-info-circle"></i>
                        <strong>Klik pada peta</strong> untuk menentukan titik lokasi kegiatan. Lingkaran biru menunjukkan area radius <strong>100 meter</strong>.
                    </div>

                    <div id="map-picker"></div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control bg-light" required readonly
                                value="{{ old('latitude', '-1.6411802') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control bg-light" required readonly
                                value="{{ old('longitude', '103.5793161') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Radius (Meter)</label>
                            <input type="number" id="radius_meter" name="radius_meter" class="form-control" required
                                value="{{ old('radius_meter', 100) }}" min="10" max="5000">
                        </div>
                    </div>
                </div>

                {{-- === TIPE KEGIATAN & PEMILIHAN PEGAWAI === --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Tipe Kegiatan</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe" id="tipe_kegiatan" value="kegiatan" {{ old('tipe', 'kegiatan') === 'kegiatan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_kegiatan">Kegiatan Biasa (pilih peserta)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe" id="tipe_apel" value="apel" {{ old('tipe') === 'apel' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_apel">Apel Pagi (semua pegawai)</label>
                        </div>
                    </div>
                </div>

                <div id="pegawai-selection" class="mb-4">
                    <label class="form-label fw-bold">Pilih Peserta Kegiatan</label>
                    <div class="mb-2">
                        <input type="text" id="filter-pegawai" class="form-control form-control-sm" style="max-width: 300px;" placeholder="Cari nama pegawai...">
                    </div>
                    <div style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px;">
                        @foreach ($ruangans as $ruangan)
                            <div class="mb-2 ruangan-group">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <input type="checkbox" class="select-all-ruangan" data-ruangan="{{ $ruangan->id }}">
                                    <strong class="small">{{ $ruangan->nama_ruangan }}</strong>
                                    <span class="text-muted small">({{ $ruangan->pegawai->count() }} pegawai)</span>
                                </div>
                                <div class="ms-3 row g-1" data-ruangan="{{ $ruangan->id }}">
                                    @forelse ($ruangan->pegawai->where('status_aktif', 1) as $pegawai)
                                        <div class="col-md-4 col-sm-6 pegawai-item">
                                            <div class="form-check">
                                                <input class="form-check-input pegawai-checkbox" type="checkbox"
                                                    name="pegawai_ids[]" value="{{ $pegawai->id }}"
                                                    id="pegawai-{{ $pegawai->id }}"
                                                    data-nama="{{ strtolower($pegawai->nama) }}"
                                                    {{ in_array($pegawai->id, old('pegawai_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="pegawai-{{ $pegawai->id }}">
                                                    {{ $pegawai->nama }} <code class="text-muted">{{ $pegawai->nip }}</code>
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12"><span class="text-muted small">Tidak ada pegawai aktif.</span></div>
                                    @endforelse
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-2">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <button type="button" id="select-all-all" class="btn btn-outline-primary btn-sm rounded-pill">Pilih Semua</button>
                        <button type="button" id="deselect-all-all" class="btn btn-outline-secondary btn-sm rounded-pill">Hapus Semua</button>
                        <span id="selected-count" class="small text-muted ms-2">0 pegawai dipilih</span>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn text-white" style="background-color: #1A7A6E;">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const RSUD_LAT = -1.6411802;
    const RSUD_LON = 103.5793161;

    const inputLat    = document.getElementById('latitude');
    const inputLon    = document.getElementById('longitude');
    const inputRadius = document.getElementById('radius_meter');
    const btnOpenMap  = document.getElementById('btn-open-map');
    const btnViewGmaps = document.getElementById('btn-view-gmaps');
    const mapDiv      = document.getElementById('map-picker');
    const mapTip      = document.getElementById('map-tip');

    let map = null;
    let marker = null;
    let circle = null;
    let mapInitialized = false;

    // Update Google Maps link setiap kali lat/lon berubah
    function updateGmapsLink() {
        const lat = inputLat.value;
        const lon = inputLon.value;
        if (lat && lon) {
            btnViewGmaps.href = `https://www.google.com/maps?q=${lat},${lon}&z=18`;
            btnViewGmaps.classList.remove('d-none');
        }
    }

    // Gambar marker & circle di peta
    function placeMarker(lat, lng) {
        const radius = parseInt(inputRadius.value) || 100;

        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        marker = L.marker([lat, lng], { draggable: true })
            .addTo(map)
            .bindPopup(`<b>Lokasi Kegiatan</b><br>Lat: ${lat.toFixed(7)}<br>Lng: ${lng.toFixed(7)}<br>Radius: ${radius}m`)
            .openPopup();

        circle = L.circle([lat, lng], {
            radius: radius,
            color: '#1A7A6E',
            fillColor: '#2A9D8F',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(map);

        // Drag marker → update coords
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            setCoords(pos.lat, pos.lng);
        });

        inputLat.value = lat.toFixed(7);
        inputLon.value = lng.toFixed(7);
        updateGmapsLink();
    }

    function setCoords(lat, lng) {
        inputLat.value = lat.toFixed(7);
        inputLon.value = lng.toFixed(7);
        placeMarker(lat, lng);
        updateGmapsLink();
    }

    // Tombol "Pilih Lokasi di Peta"
    btnOpenMap.addEventListener('click', function() {
        mapDiv.classList.toggle('visible');
        mapTip.classList.toggle('visible');

        if (!mapInitialized) {
            mapInitialized = true;

            // Inisialisasi peta terpusat di RSUD H. Abdul Manap
            map = L.map('map-picker').setView([RSUD_LAT, RSUD_LON], 18);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 20
            }).addTo(map);

            // Pasang marker awal di RSUD
            const initLat = parseFloat(inputLat.value) || RSUD_LAT;
            const initLon = parseFloat(inputLon.value) || RSUD_LON;
            placeMarker(initLat, initLon);

            // Klik peta → pindahkan marker
            map.on('click', function(e) {
                setCoords(e.latlng.lat, e.latlng.lng);
            });
        } else {
            // Paksa re-render ukuran peta jika sudah ada
            setTimeout(() => map.invalidateSize(), 50);
        }

        btnOpenMap.innerHTML = mapDiv.classList.contains('visible')
            ? '<i class="fas fa-times"></i> Tutup Peta'
            : '<i class="fas fa-map-marked-alt"></i> Pilih Lokasi di Peta';
    });

    // Saat radius berubah → update circle di peta
    inputRadius.addEventListener('input', function() {
        if (marker && circle && map) {
            const lat = parseFloat(inputLat.value);
            const lng = parseFloat(inputLon.value);
            placeMarker(lat, lng);
        }
    });

    // Init Google Maps link dari nilai default
    updateGmapsLink();

    // === TIPE KEGIATAN TOGGLE ===
    const tipeKegiatan = document.getElementById('tipe_kegiatan');
    const tipeApel = document.getElementById('tipe_apel');
    const pegawaiSelection = document.getElementById('pegawai-selection');

    function togglePegawaiSelection() {
        pegawaiSelection.style.display = tipeKegiatan.checked ? 'block' : 'none';
    }
    tipeKegiatan.addEventListener('change', togglePegawaiSelection);
    tipeApel.addEventListener('change', togglePegawaiSelection);
    togglePegawaiSelection();
    updateSelectedCount();

    // === SELECT ALL PER RUANGAN ===
    document.querySelectorAll('.select-all-ruangan').forEach(function(cb) {
        cb.addEventListener('change', function() {
            const ruangan = this.dataset.ruangan;
            const container = document.querySelector(`.row[data-ruangan="${ruangan}"]`);
            container.querySelectorAll('.pegawai-checkbox').forEach(function(c) {
                c.checked = cb.checked;
            });
            updateSelectedCount();
        });
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll('.pegawai-checkbox:checked').length;
        document.getElementById('selected-count').textContent = count + ' pegawai dipilih';
    }

    document.querySelectorAll('.pegawai-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Select / Deselect All
    document.getElementById('select-all-all').addEventListener('click', function() {
        document.querySelectorAll('.pegawai-checkbox').forEach(function(c) { c.checked = true; });
        document.querySelectorAll('.select-all-ruangan').forEach(function(c) { c.checked = true; });
        updateSelectedCount();
    });
    document.getElementById('deselect-all-all').addEventListener('click', function() {
        document.querySelectorAll('.pegawai-checkbox').forEach(function(c) { c.checked = false; });
        document.querySelectorAll('.select-all-ruangan').forEach(function(c) { c.checked = false; });
        updateSelectedCount();
    });

    // === FILTER PEGAWAI ===
    document.getElementById('filter-pegawai').addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        document.querySelectorAll('.pegawai-item').forEach(function(item) {
            const nama = item.querySelector('.pegawai-checkbox').dataset.nama;
            item.style.display = nama.includes(keyword) ? '' : 'none';
        });
        document.querySelectorAll('.ruangan-group').forEach(function(group) {
            const visible = group.querySelectorAll('.pegawai-item[style*="display: none"]').length;
            const total = group.querySelectorAll('.pegawai-item').length;
            // Hide ruangan group if all pegawai are hidden
            if (keyword !== '' && visible === total) {
                group.style.display = 'none';
            } else {
                group.style.display = '';
            }
        });
    });
});
</script>
@endpush
