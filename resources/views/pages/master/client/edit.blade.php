@extends('layouts.app')

@section('title', 'Edit Client')

@section('main')
<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Edit Client</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('clients.update', $client->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Client Name</label>
                                    <input type="text" name="name" value="{{ $client->name }}" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control">{{ $client->address }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="contact_person" value="{{ $client->contact_person }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone" value="{{ $client->phone }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Check In Time</label>
                                    <input 
                                        type="text" 
                                        id="check_in_time" 
                                        name="check_in_time" 
                                        value="{{ $client->check_in_time ? \Carbon\Carbon::parse($client->check_in_time)->format('H:i') : '' }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Check Out Time</label>
                                    <input 
                                        type="text" 
                                        id="check_out_time" 
                                        name="check_out_time" 
                                        value="{{ $client->check_out_time ? \Carbon\Carbon::parse($client->check_out_time)->format('H:i') : '' }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Attendance Radius (meters)</label>
                                    <input type="number" name="attendance_radius" value="{{ $client->attendance_radius }}" class="form-control">
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label>Find Location</label>
                                <input type="text" id="searchLocation" class="form-control mb-2">

                                <button type="button" id="btnSearch" class="btn btn-primary mb-2">Find</button>
                                <button type="button" id="btnGps" class="btn btn-info mb-2">Use My Location</button>

                                <div id="map" style="height: 300px;"></div>

                                <input type="hidden" name="latitude" id="latitude" value="{{ $client->latitude }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ $client->longitude }}">

                                <div class="mt-2">
                                    <small>
                                        Lat: <span id="latText">{{ $client->latitude }}</span> |
                                        Long: <span id="lngText">{{ $client->longitude }}</span>
                                    </small>
                                </div>

                            </div>

                        </div>

                        <button class="btn btn-primary mt-3">Update</button>

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

            let lat = {{ $client->latitude ?? -6.2 }};
            let lng = {{ $client->longitude ?? 106.8 }};

            let map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            let marker = L.marker([lat, lng]).addTo(map);

            function setLatLng(lat, lng) {
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                document.getElementById('latText').innerText = lat;
                document.getElementById('lngText').innerText = lng;
            }

            // klik map
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                setLatLng(e.latlng.lat, e.latlng.lng);
            });

            // search
            document.getElementById('btnSearch').addEventListener('click', function () {
                let query = document.getElementById('searchLocation').value;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) return alert('Location not found');

                        let lat = parseFloat(data[0].lat);
                        let lng = parseFloat(data[0].lon);

                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);

                        setLatLng(lat, lng);
                    });
            });

            // enter
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
                    marker.setLatLng([lat, lng]);

                    setLatLng(lat, lng);
                });
            });

        });
    </script>
@endpush