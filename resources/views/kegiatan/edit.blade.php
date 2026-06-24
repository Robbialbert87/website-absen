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
        <h1 class="h3 mb-0 text-gray-800">Edit Kegiatan</h1>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('kegiatan.update', $kegiatan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" required value="{{ old('tanggal_kegiatan', $kegiatan->tanggal_kegiatan) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required value="{{ old('jam_mulai', $kegiatan->jam_mulai) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required value="{{ old('jam_selesai', $kegiatan->jam_selesai) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" {{ $kegiatan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ $kegiatan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Lokasi (Nama Tempat)</label>
                        <input type="text" name="lokasi" class="form-control" required value="{{ old('lokasi', $kegiatan->lokasi) }}">
                    </div>
                </div>

                {{-- === BAGIAN PETA & KOORDINAT === --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Titik Lokasi Kegiatan</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" id="btn-open-map" class="btn btn-info text-white btn-sm rounded-pill shadow-sm">
                            <i class="fas fa-map-marked-alt"></i> Ubah Lokasi di Peta
                        </button>
                        <a href="#" id="btn-view-gmaps" target="_blank" class="btn btn-outline-success btn-sm rounded-pill d-none">
                            <i class="fas fa-external-link-alt"></i> Lihat di Google Maps
                        </a>
                    </div>

                    <div class="map-tip" id="map-tip">
                        <i class="fas fa-info-circle"></i>
                        <strong>Klik pada peta</strong> untuk menentukan titik lokasi kegiatan. Lingkaran biru menunjukkan area radius. Anda juga dapat <strong>drag marker</strong> untuk menggeser posisi.
                    </div>

                    <div id="map-picker"></div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control bg-light" required readonly
                                value="{{ old('latitude', $kegiatan->latitude) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control bg-light" required readonly
                                value="{{ old('longitude', $kegiatan->longitude) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Radius (Meter)</label>
                            <input type="number" id="radius_meter" name="radius_meter" class="form-control" required
                                value="{{ old('radius_meter', $kegiatan->radius_meter) }}" min="10" max="5000">
                        </div>
                    </div>
                </div>

                {{-- === TIPE KEGIATAN & PEMILIHAN PEGAWAI === --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Tipe Kegiatan</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe" id="tipe_kegiatan" value="kegiatan"
                                {{ $kegiatan->tipe === 'kegiatan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_kegiatan">Kegiatan Biasa (pilih peserta)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe" id="tipe_apel" value="apel"
                                {{ $kegiatan->tipe === 'apel' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipe_apel">Apel Pagi (semua pegawai)</label>
                        </div>
                    </div>
                </div>

                <div id="pegawai-selection" class="mb-4" style="{{ $kegiatan->tipe === 'apel' ? 'display: none;' : '' }}">
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
                                                    {{ in_array($pegawai->id, $selectedPegawaiIds) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn text-white" style="background-color: #1A7A6E;">Update Kegiatan</button>
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
    const RSUD_LAT = -1.610122;
    const RSUD_LON = 103.613411;

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

    function updateGmapsLink() {
        const lat = inputLat.value;
        const lon = inputLon.value;
        if (lat && lon) {
            btnViewGmaps.href = `https://www.google.com/maps?q=${lat},${lon}&z=18`;
            btnViewGmaps.classList.remove('d-none');
        }
    }

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

    btnOpenMap.addEventListener('click', function() {
        mapDiv.classList.toggle('visible');
        mapTip.classList.toggle('visible');

        if (!mapInitialized) {
            mapInitialized = true;

            const initLat = parseFloat(inputLat.value) || RSUD_LAT;
            const initLon = parseFloat(inputLon.value) || RSUD_LON;

            map = L.map('map-picker').setView([initLat, initLon], 18);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 20
            }).addTo(map);

            placeMarker(initLat, initLon);

            map.on('click', function(e) {
                setCoords(e.latlng.lat, e.latlng.lng);
            });
        } else {
            setTimeout(() => map.invalidateSize(), 50);
        }

        btnOpenMap.innerHTML = mapDiv.classList.contains('visible')
            ? '<i class="fas fa-times"></i> Tutup Peta'
            : '<i class="fas fa-map-marked-alt"></i> Ubah Lokasi di Peta';
    });

    inputRadius.addEventListener('input', function() {
        if (marker && circle && map) {
            const lat = parseFloat(inputLat.value);
            const lng = parseFloat(inputLon.value);
            placeMarker(lat, lng);
        }
    });

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
        const el = document.getElementById('selected-count');
        if (el) el.textContent = count + ' pegawai dipilih';
    }

    document.querySelectorAll('.pegawai-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Select / Deselect All
    const selectAllBtn = document.getElementById('select-all-all');
    const deselectAllBtn = document.getElementById('deselect-all-all');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.pegawai-checkbox').forEach(function(c) { c.checked = true; });
            document.querySelectorAll('.select-all-ruangan').forEach(function(c) { c.checked = true; });
            updateSelectedCount();
        });
    }
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.pegawai-checkbox').forEach(function(c) { c.checked = false; });
            document.querySelectorAll('.select-all-ruangan').forEach(function(c) { c.checked = false; });
            updateSelectedCount();
        });
    }

    // === FILTER PEGAWAI ===
    const filterInput = document.getElementById('filter-pegawai');
    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('.pegawai-item').forEach(function(item) {
                const nama = item.querySelector('.pegawai-checkbox').dataset.nama;
                item.style.display = nama.includes(keyword) ? '' : 'none';
            });
            document.querySelectorAll('.ruangan-group').forEach(function(group) {
                const visible = group.querySelectorAll('.pegawai-item[style*="display: none"]').length;
                const total = group.querySelectorAll('.pegawai-item').length;
                if (keyword !== '' && visible === total) {
                    group.style.display = 'none';
                } else {
                    group.style.display = '';
                }
            });
        });
    }

    // Init selected count on load
    updateSelectedCount();
});
</script>
@endpush
