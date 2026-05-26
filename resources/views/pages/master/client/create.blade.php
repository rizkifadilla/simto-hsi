@extends('layouts.app')

@section('title', 'Tambah Client')

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Add Client</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('clients.store') }}">
                        @csrf

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Client Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Check In Time</label>
                                    <input type="text" id="check_in_time" name="check_in_time" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Check Out Time</label>
                                    <input type="text" id="check_out_time" name="check_out_time" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Attendance Radius (meters)</label>
                                    <input type="number" name="attendance_radius" class="form-control" placeholder="e.g. 50">
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label>Find Location</label>
                                <input type="text" id="searchLocation" class="form-control mb-2" placeholder="Find location...">

                                <button type="button" id="btnSearch" class="btn btn-primary mb-2">Find</button>
                                <button type="button" id="btnGps" class="btn btn-info mb-2">Use My Location</button>

                                <div id="map" style="height: 300px;"></div>

                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">

                                <div class="mt-2">
                                    <small>
                                        Lat: <span id="latText">-</span> |
                                        Long: <span id="lngText">-</span>
                                    </small>
                                </div>

                            </div>

                        </div>

                        <button class="btn btn-primary mt-3">Save</button>

                    </form>

                </div>
            </div>
        </div>

    </section>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    flatpickr("#check_in_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    flatpickr("#check_out_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    document.addEventListener('DOMContentLoaded', function () {

        let map = L.map('map').setView([-6.2, 106.8], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let marker = null;

        function setLatLng(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            document.getElementById('latText').innerText = lat;
            document.getElementById('lngText').innerText = lng;
        }

        // klik map
        map.on('click', function (e) {
            if (marker) marker.setLatLng(e.latlng);
            else marker = L.marker(e.latlng).addTo(map);

            setLatLng(e.latlng.lat, e.latlng.lng);
        });

        // tombol search
        document.getElementById('btnSearch').addEventListener('click', function () {
            let query = document.getElementById('searchLocation').value;
            if (!query) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.length) return alert('Lokasi tidak ditemukan');

                    let lat = parseFloat(data[0].lat);
                    let lng = parseFloat(data[0].lon);

                    map.setView([lat, lng], 15);

                    if (marker) marker.setLatLng([lat, lng]);
                    else marker = L.marker([lat, lng]).addTo(map);

                    setLatLng(lat, lng);
                });
        });

        // enter search
        document.getElementById('searchLocation').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnSearch').click();
            }
        });

        // GPS
        document.getElementById('btnGps').addEventListener('click', function () {
            navigator.geolocation.getCurrentPosition(function (pos) {
                let lat = pos.coords.latitude;
                let lng = pos.coords.longitude;

                map.setView([lat, lng], 15);

                if (marker) marker.setLatLng([lat, lng]);
                else marker = L.marker([lat, lng]).addTo(map);

                setLatLng(lat, lng);
            });
        });

    });
</script>
@endpush