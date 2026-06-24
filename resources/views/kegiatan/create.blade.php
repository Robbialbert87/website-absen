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
});
</script>
@endpush
