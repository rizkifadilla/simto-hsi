@extends('layouts.app')

@section('title', 'Absensi')

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Absensi</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body text-center">

                    {{-- DISTANCE --}}
                    <div id="distanceInfo" class="alert alert-secondary">
                        Mengambil lokasi...
                    </div>

                    {{-- ALERT --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
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
                                <video id="video" width="100%" autoplay></video>

                                <canvas id="canvas" style="display:none;"></canvas>

                                <button type="button" id="btnCapture" class="btn btn-primary mt-2">
                                    Ambil Foto
                                </button>

                                <img id="preview" width="100%" class="mt-2">
                            </div>

                            {{-- INFO --}}
                            <div class="col-md-6 text-left">
                                <input type="text"
                                       id="locationText"
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

                        {{-- BUTTON CENTER --}}
                        <div class="text-center" id="actionArea" style="display: none">
                            @if(!$attendance || !$attendance->check_in)
                                <button type="submit" id="btnSubmit" class="btn btn-success btn-lg px-5">
                                    Check In
                                </button>
                            @elseif(!$attendance->check_out)
                                <button type="submit" id="btnSubmit" class="btn btn-warning btn-lg px-5">
                                    Check Out
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg px-5" disabled>
                                    Sudah Absen Hari Ini
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

    let clientLat = {{ $clientLat ?? 'null' }};
    let clientLng = {{ $clientLng ?? 'null' }};
    let radius    = {{ $radius ?? 100 }};
    let isWithinRadius = false;

    // ================= MODEL =================
    const MODEL_URL = window.location.origin + '/models';

    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

    // ================= CAMERA =================
    let video  = document.getElementById('video');
    let canvas = document.getElementById('canvas');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => video.srcObject = stream);

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

    // ================= AUTO GPS =================
    navigator.geolocation.getCurrentPosition(function (pos) {

        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;

        document.getElementById('latitude').value  = lat;
        document.getElementById('longitude').value = lng;

        let meter = calculateDistance(lat, lng, clientLat, clientLng);
        meter = Math.round(meter);

        let info = document.getElementById('distanceInfo');
        let actionArea = document.getElementById('actionArea');

        if (meter <= radius) {
            info.className = 'alert alert-success';
            info.innerHTML = "✅ Dalam area (" + meter + " meter)";
            isWithinRadius = true;
            actionArea.style.display = 'block';
        } else {
            info.className = 'alert alert-danger';
            info.innerHTML = "❌ Di luar area (" + meter + " meter)";
            isWithinRadius = false;
            actionArea.style.display = 'none';
        }

    }, function () {
        document.getElementById('distanceInfo').innerHTML = "❌ GPS gagal";
    });

    // ================= CAPTURE =================
    document.getElementById('btnCapture').addEventListener('click', async function () {

        let ctx = canvas.getContext('2d');

        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0);

        const detections = await faceapi.detectAllFaces(
            canvas,
            new faceapi.TinyFaceDetectorOptions()
        )
        .withFaceLandmarks()
        .withFaceDescriptors();

        if (detections.length === 0) {
            alert('❌ Wajah tidak terdeteksi!');
            return;
        }

        if (detections.length > 1) {
            alert('❌ Hanya 1 wajah!');
            return;
        }

        const detection = detections[0];

        if (detection.detection.box.width < 150) {
            alert('❌ Wajah terlalu jauh!');
            return;
        }

        let descriptor = Array.from(detection.descriptor);

        document.getElementById('face_descriptor').value =
            JSON.stringify(descriptor);

        let data = canvas.toDataURL('image/png');

        document.getElementById('photo').value = data;
        document.getElementById('preview').src = data;

        alert('✅ Foto berhasil diambil');
    });

    // ================= VALIDASI =================
    document.querySelector('form').addEventListener('submit', function (e) {

        if (!isWithinRadius) {
            e.preventDefault();
            alert('❌ Anda di luar area!');
            return;
        }

        if (!document.getElementById('photo').value) {
            e.preventDefault();
            alert('❌ Ambil foto dulu!');
            return;
        }

        let isCheckout = {{ $attendance && $attendance->check_in && !$attendance->check_out ? 'true' : 'false' }};

        if (isCheckout) {
            let task = document.querySelector('[name="task"]').value;

            if (!task) {
                e.preventDefault();
                alert('❌ Isi kegiatan dulu!');
            }
        }

    });

});
</script>

@endpush