@extends('layouts.app')

@section('title', 'Absensi')

@push('style')
@endpush

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Absensi</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body text-center">

                    {{-- STATUS --}}
                    <div id="statusInfo" class="alert alert-secondary">
                        Mengecek sistem absensi...
                    </div>

                    {{-- DISTANCE --}}
                    <div id="distanceInfo" class="alert alert-secondary">
                        Mengambil lokasi...
                    </div>

                    {{-- ALERT --}}
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf

                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <input type="hidden" id="photo" name="photo">
                        <input type="hidden" id="face_descriptor" name="face_descriptor">

                        <div class="row">

                            {{-- CAMERA --}}
                            <div class="col-md-6">
                                <div style="position:relative;">
                                    <video id="video" width="100%" autoplay></video>

                                    <div id="guideBox"
                                        style="position:absolute; top:20%; left:20%; width:60%; height:60%;
                                               border:2px dashed #00ffcc; border-radius:10px;">
                                    </div>
                                </div>

                                <canvas id="canvas" style="display:none;"></canvas>

                                <div id="instruction" class="mt-2 text-primary fw-bold">
                                    Siapkan wajah...
                                </div>

                                <img id="preview" width="100%" class="mt-2">
                            </div>

                            {{-- INFO --}}
                            <div class="col-md-6 text-left">
                                <input type="text" id="locationText"
                                       class="form-control mb-2"
                                       readonly
                                       placeholder="Lokasi Anda">

                                @if($attendance)
                                    <div class="alert alert-info">
                                        Check In: {{ $attendance->check_in ?? '-' }} <br>
                                        Check Out: {{ $attendance->check_out ?? '-' }}
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- TASK --}}
                        @if($attendance && $attendance->check_in && !$attendance->check_out)
                            <div class="form-group mt-3 text-left">
                                <label>Task / Kegiatan Hari Ini</label>
                                <textarea name="task" class="form-control"></textarea>
                            </div>
                        @endif

                        <br>

                        {{-- BUTTON --}}
                        <div class="text-center" id="actionArea" style="display:none">
                            @if(!$attendance || !$attendance->check_in)
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    Check In
                                </button>
                            @elseif(!$attendance->check_out)
                                <button type="submit" class="btn btn-warning btn-lg px-5">
                                    Check Out
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg px-5" disabled>
                                    Sudah Absen
                                </button>
                            @endif
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async function () {

    const video  = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    let clientLat = {{ $clientLat ?? 'null' }};
    let clientLng = {{ $clientLng ?? 'null' }};
    let radius    = {{ $radius ?? 100 }};

    // FIX BOOLEAN
    let useDistance = {{ $useDistance ? 'true' : 'false' }};
    let isWithinRadius = !useDistance;

    let targetDirection = null;
    let movementPassed  = false;
    let hasMoved        = false;
    let autoCaptured    = false;
    let initialized     = false;

    const directions = ['LEFT', 'RIGHT', 'UP', 'DOWN'];

    function randomDirection() {
        return directions[Math.floor(Math.random() * directions.length)];
    }

    function show(text, type = 'secondary') {
        let el = document.getElementById('distanceInfo');
        el.className = 'alert alert-' + type;
        el.innerHTML = text;
    }

    // ================= DISTANCE =================
    function calculateDistance(lat1, lon1, lat2, lon2) {
        let R    = 6371000;
        let dLat = (lat2 - lat1) * Math.PI / 180;
        let dLon = (lon2 - lon1) * Math.PI / 180;

        let a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) *
                Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) ** 2;

        return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    }

    navigator.geolocation.getCurrentPosition(function (pos) {

        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;

        document.getElementById('latitude').value  = lat;
        document.getElementById('longitude').value = lng;

        if (!useDistance) {
            isWithinRadius = true;
            show("✅ Absensi tanpa lokasi aktif", "success");
            startLiveness();
            return;
        }

        let meter = calculateDistance(lat, lng, clientLat, clientLng);
        meter = Math.round(meter);

        if (meter <= radius) {
            isWithinRadius = true;
            show("✅ Dalam area (" + meter + " meter)", "success");
            startLiveness();
        } else {
            isWithinRadius = false;
            show("❌ Di luar area (" + meter + " meter)", "danger");
            document.getElementById('actionArea').style.display = 'none';
        }

    }, function () {
        show("❌ GPS gagal", "danger");
    });

    // ================= LOAD MODEL =================
    async function startLiveness() {

        const MODEL_URL = window.location.origin + '/models';

        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => video.srcObject = stream);

        show("📷 Arahkan wajah ke kamera", "info");

        setInterval(async () => {

            if (!video.videoWidth) return;

            const detection = await faceapi
                .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks();

            if (!detection) {
                show("👤 Wajah tidak terdeteksi", "warning");
                return;
            }

            if (!initialized) {
                targetDirection = randomDirection();
                initialized = true;
                show("➡️ Gerakkan kepala ke: <b>" + targetDirection + "</b>", "primary");
                return;
            }

            const nose   = detection.landmarks.getNose()[3];
            const jaw    = detection.landmarks.getJawOutline();
            const left   = jaw[0];
            const right  = jaw[16];
            const top    = detection.detection.box.top;
            const bottom = detection.detection.box.bottom;

            const centerX = (left.x + right.x) / 2;
            const centerY = (top + bottom) / 2;

            const dx = nose.x - centerX;
            const dy = nose.y - centerY;

            const thresholdX = 20;
            const thresholdY = 15;

            let currentDirection = 'CENTER';

            if (dx > thresholdX) currentDirection = 'RIGHT';
            else if (dx < -thresholdX) currentDirection = 'LEFT';
            else if (dy > thresholdY) currentDirection = 'DOWN';
            else if (dy < -thresholdY) currentDirection = 'UP';

            // ================= GERAK =================
            if (!movementPassed) {
                if (currentDirection === targetDirection) {
                    hasMoved = true;
                    movementPassed = true;
                    show("✅ Bagus! Kembali ke tengah", "success");
                } else {
                    show("➡️ Ikuti arah: <b>" + targetDirection + "</b>", "primary");
                }
                return;
            }

            // ================= BALIK KE TENGAH =================
            if (movementPassed && hasMoved && currentDirection === 'CENTER' && !autoCaptured) {

                autoCaptured = true;

                let ctx = canvas.getContext('2d');
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;

                ctx.drawImage(video, 0, 0);

                const fullDetection = await faceapi
                    .detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!fullDetection) {
                    show("❌ Gagal ambil wajah", "danger");
                    autoCaptured = false;
                    return;
                }

                let descriptor = Array.from(fullDetection.descriptor);

                document.getElementById('face_descriptor').value =
                    JSON.stringify(descriptor);

                let data = canvas.toDataURL('image/png');

                document.getElementById('photo').value   = data;
                document.getElementById('preview').src   = data;

                show("✅ Siap absen", "success");

                document.getElementById('actionArea').style.display = 'block';
            }

        }, 700);
    }

});
</script>
@endpush