@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-fallback {
        height: 300px;
        border-radius: 12px;
        border: 2px solid #dee2e6;
        z-index: 0;
    }
    .video-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 3/4;
        border-radius: 14px;
        overflow: hidden;
        background: #000;
    }
    #video { width: 100%; height: 100%; object-fit: cover; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 mx-auto" style="border-radius: 15px; max-width: 540px;">
        <div class="card-body p-4">

            <h4 class="fw-bold text-center mb-1" style="color: #1A7A6E;">{{ $kegiatan->nama_kegiatan }}</h4>
            <p class="text-muted text-center small mb-4">
                {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d M Y') }}
                &bull; {{ $kegiatan->jam_mulai }} – {{ $kegiatan->jam_selesai }}
                &bull; {{ $kegiatan->lokasi }}
            </p>

            @if($sudahAbsen)
                <div class="text-center py-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                    <h5 class="mt-3 fw-bold">Absensi Berhasil</h5>
                    <p class="text-muted">Anda sudah melakukan absensi untuk kegiatan ini.</p>
                    <span class="badge fs-6 px-3 py-2
                        @if($sudahAbsen->status == 'hadir') bg-success
                        @elseif($sudahAbsen->status == 'terlambat') bg-warning text-dark
                        @else bg-danger @endif">
                        {{ ucfirst(str_replace('_', ' ', $sudahAbsen->status)) }}
                    </span>
                    <div class="mt-4">
                        <a href="{{ route('user.kegiatan.index') }}" class="btn btn-secondary rounded-pill">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            @else
                <div id="location-status" class="text-center mb-3">
                    <span class="status-badge bg-warning text-dark">
                        <i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...
                    </span>
                </div>

                <div id="gps-error-box" class="alert alert-warning d-none" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>GPS tidak tersedia</strong> — Browser tidak dapat mengakses lokasi otomatis
                    (kemungkinan situs diakses via HTTP atau izin lokasi ditolak).<br>
                    <small>Silakan <strong>klik posisi Anda</strong> di peta di bawah ini.</small>
                </div>

                <div id="map-fallback-wrapper" class="d-none mb-3">
                    <p class="small text-muted mb-1">
                        <i class="fas fa-info-circle text-info"></i> Klik titik lokasi Anda di peta:
                    </p>
                    <div id="map-fallback"></div>
                    <div id="map-coords-info" class="small text-muted text-center mt-1 d-none">
                        📍 Lokasi dipilih: <span id="map-lat-display"></span>, <span id="map-lon-display"></span>
                    </div>
                </div>

                <div class="video-wrapper mb-3">
                    <video id="video" autoplay playsinline muted></video>
                    <canvas id="canvas" class="d-none"></canvas>
                </div>

                <button id="btn-absen"
                    class="btn text-white w-100 rounded-pill py-3 fw-bold shadow-sm"
                    style="background-color: #1A7A6E; font-size: 1.1rem;" disabled>
                    <i class="fas fa-camera me-2"></i> AMBIL FOTO & ABSEN
                </button>
                <p class="text-center text-muted small mt-2" id="btn-hint">
                    Menunggu lokasi dan kamera siap...
                </p>

                {{-- Data kegiatan untuk JS --}}
                <div id="kegiatan-data"
                    data-lat="{{ $kegiatan->latitude }}"
                    data-lon="{{ $kegiatan->longitude }}"
                    data-radius="{{ $kegiatan->radius_meter }}"
                    data-absen-url="{{ route('user.kegiatan.absen', $kegiatan->id) }}"
                    data-csrf="{{ csrf_token() }}"
                    style="display:none;">
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dataEl = document.getElementById('kegiatan-data');
    if (!dataEl) return; // sudah absen, tidak perlu JS

    const KEGIATAN_LAT = parseFloat(dataEl.dataset.lat);
    const KEGIATAN_LON = parseFloat(dataEl.dataset.lon);
    const RADIUS       = parseInt(dataEl.dataset.radius);
    const ABSEN_URL    = dataEl.dataset.absenUrl;
    const CSRF         = dataEl.dataset.csrf;

    const video          = document.getElementById('video');
    const canvas         = document.getElementById('canvas');
    const btnAbsen       = document.getElementById('btn-absen');
    const locationStatus = document.getElementById('location-status');
    const gpsErrorBox    = document.getElementById('gps-error-box');
    const mapWrapper     = document.getElementById('map-fallback-wrapper');
    const mapCoordsInfo  = document.getElementById('map-coords-info');
    const mapLatDisplay  = document.getElementById('map-lat-display');
    const mapLonDisplay  = document.getElementById('map-lon-display');
    const btnHint        = document.getElementById('btn-hint');

    let userLat = null, userLon = null;
    let cameraReady = false;
    let mapInstance = null, mapMarker = null;

    // 1. Buka Kamera
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false })
            .then(stream => { video.srcObject = stream; cameraReady = true; checkReady(); })
            .catch(() => { cameraReady = true; checkReady(); });
    } else {
        cameraReady = true;
    }

    // 2. Coba GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                userLat = pos.coords.latitude;
                userLon = pos.coords.longitude;
                const dist = haversine(userLat, userLon, KEGIATAN_LAT, KEGIATAN_LON);
                const ok   = dist <= RADIUS;
                locationStatus.innerHTML = ok
                    ? `<span class="status-badge bg-success text-white"><i class="fas fa-check-circle"></i> Dalam radius (${Math.round(dist)}m)</span>`
                    : `<span class="status-badge bg-danger text-white"><i class="fas fa-times-circle"></i> Luar radius (${Math.round(dist)}m, maks ${RADIUS}m)</span>`;
                checkReady();
            },
            function() { showMapFallback(); },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    } else {
        showMapFallback();
    }

    // 3. Peta Fallback jika GPS gagal
    function showMapFallback() {
        gpsErrorBox.classList.remove('d-none');
        mapWrapper.classList.remove('d-none');
        locationStatus.innerHTML = `<span class="status-badge" style="background:#fff3cd;color:#856404;"><i class="fas fa-map-marker-alt"></i> Pilih lokasi Anda di peta</span>`;

        mapInstance = L.map('map-fallback').setView([KEGIATAN_LAT, KEGIATAN_LON], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 20
        }).addTo(mapInstance);

        // Lingkaran radius kegiatan sebagai referensi
        L.circle([KEGIATAN_LAT, KEGIATAN_LON], {
            radius: RADIUS, color: '#1A7A6E', fillColor: '#2A9D8F',
            fillOpacity: 0.15, weight: 2, dashArray: '5,5'
        }).addTo(mapInstance).bindPopup(`Area kegiatan (radius ${RADIUS}m)`);

        mapInstance.on('click', function(e) {
            userLat = e.latlng.lat;
            userLon = e.latlng.lng;

            if (mapMarker) mapInstance.removeLayer(mapMarker);
            mapMarker = L.marker([userLat, userLon], { draggable: false })
                .addTo(mapInstance).bindPopup('Posisi Anda').openPopup();

            mapLatDisplay.textContent = userLat.toFixed(7);
            mapLonDisplay.textContent = userLon.toFixed(7);
            mapCoordsInfo.classList.remove('d-none');

            const dist = haversine(userLat, userLon, KEGIATAN_LAT, KEGIATAN_LON);
            const ok   = dist <= RADIUS;
            locationStatus.innerHTML = ok
                ? `<span class="status-badge bg-success text-white"><i class="fas fa-check-circle"></i> Dalam radius (${Math.round(dist)}m)</span>`
                : `<span class="status-badge bg-danger text-white"><i class="fas fa-times-circle"></i> Luar radius (${Math.round(dist)}m, maks ${RADIUS}m)</span>`;
            checkReady();
        });
    }

    // 4. Cek apakah siap untuk absen
    function checkReady() {
        if (userLat !== null && cameraReady) {
            btnAbsen.disabled = false;
            btnHint.textContent = 'Klik tombol di atas untuk mengambil foto & menyimpan absensi.';
        }
    }

    // 5. Submit Absensi
    btnAbsen.addEventListener('click', function() {
        btnAbsen.disabled = true;
        btnAbsen.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';

        const ctx = canvas.getContext('2d');
        canvas.width  = video.videoWidth  || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoData = canvas.toDataURL('image/jpeg', 0.85);

        fetch(ABSEN_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ foto: photoData, latitude: userLat, longitude: userLon })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert('❌ ' + data.message);
                btnAbsen.disabled = false;
                btnAbsen.innerHTML = '<i class="fas fa-camera me-2"></i> AMBIL FOTO & ABSEN';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan koneksi.');
            btnAbsen.disabled = false;
            btnAbsen.innerHTML = '<i class="fas fa-camera me-2"></i> AMBIL FOTO & ABSEN';
        });
    });

    // Haversine formula
    function haversine(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 +
                  Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
});
</script>
@endpush
