@extends('layouts.app')

@section('title', 'Dashboard')

@section('main')
<div class="main-content">
<section class="section">

<div class="section-header">
    <h1>Dashboard Kinerja</h1>
</div>

{{-- STAT --}}
<div class="row">

    <div class="col-lg-3 col-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="far fa-user"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total Karyawan</h4></div>
                <div class="card-body">{{ $totalEmployee }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-check"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Hadir Hari Ini</h4></div>
                <div class="card-body">{{ $presentToday }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i class="fas fa-times"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Tidak Hadir</h4></div>
                <div class="card-body">{{ $notPresent }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Rata Jam Kerja</h4></div>
                <div class="card-body">{{ $avgWorkHour }} Jam</div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    {{-- CHART KEHADIRAN --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4>Kehadiran 7 Hari</h4></div>
            <div class="card-body">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    {{-- PIE --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4>Perbandingan Kehadiran</h4></div>
            <div class="card-body">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row">

    {{-- AVG CHECK IN --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4>Rata-rata Jam Masuk</h4></div>
            <div class="card-body">
                <canvas id="avgCheckInChart"></canvas>
            </div>
        </div>
    </div>

    {{-- RANKING --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4>Top Karyawan</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($ranking as $r)
                    <li class="list-group-item d-flex justify-content-between">
                        {{ $r->full_name }}
                        <span class="badge badge-primary">
                            {{ $r->total_hadir }} hari
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- ACTIVITY --}}
<div class="card">
    <div class="card-header">
        <h4>Aktivitas Terbaru</h4>
    </div>
    <div class="card-body">
        <ul class="list-unstyled list-unstyled-border">
            @foreach($recentActivities as $act)
            <li class="media">
                <div class="media-body">
                    <div class="float-right text-primary">
                        {{ $act->created_at->diffForHumans() }}
                    </div>
                    <div class="media-title">
                        {{ $act->employee->full_name }}
                    </div>
                    <span class="text-small text-muted">
                        {{ $act->task }}
                    </span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>

</section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>

<script>

// ================= LINE CHART =================
new Chart(document.getElementById("lineChart"), {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Hadir',
            data: {!! json_encode($data) !!},
            borderColor: '#6777ef',
            backgroundColor: 'rgba(103,119,239,0.2)',
            borderWidth: 3,
            fill: true
        }]
    }
});

// ================= PIE =================
new Chart(document.getElementById("pieChart"), {
    type: 'pie',
    data: {
        labels: ['Hadir', 'Tidak Hadir'],
        datasets: [{
            data: [
                {{ $attendanceSummary['hadir'] }},
                {{ $attendanceSummary['tidak'] }}
            ],
            backgroundColor: ['#63ed7a', '#fc544b']
        }]
    }
});

// ================= AVG CHECK IN =================
new Chart(document.getElementById("avgCheckInChart"), {
    type: 'line',
    data: {
        labels: {!! json_encode($avgCheckInLabels) !!},
        datasets: [{
            label: 'Rata-rata Jam Masuk',
            data: {!! json_encode($avgCheckInData) !!},
            borderColor: '#ffa426',
            backgroundColor: 'rgba(255,164,38,0.2)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    callback: function(value) {
                        let jam = Math.floor(value / 60);
                        let menit = value % 60;
                        return jam + ':' + (menit < 10 ? '0' : '') + menit;
                    }
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem) {
                    let value = tooltipItem.yLabel;
                    let jam = Math.floor(value / 60);
                    let menit = value % 60;
                    return jam + ':' + (menit < 10 ? '0' : '') + menit;
                }
            }
        }
    }
});

</script>
@endpush