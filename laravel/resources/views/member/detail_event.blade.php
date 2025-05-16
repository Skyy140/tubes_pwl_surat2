@extends('layout.index')

@section('title', 'Detail Event')

@section('content')
    <main class="main">
        <section class="section py-4">
            <div class="container">
                <img src="/assets/img/services-1.jpg" class="card-img-top mb-4" alt="Event Image"
                    style="max-height: 500px; object-fit: cover;">
                <h2 class="mb-3">{{ $event['name'] }}</h2>
                <p><strong>Tanggal:</strong> {{ $event['date_start'] }} - {{ $event['date_end'] }}</p>
                <p><strong>Jam:</strong> {{ $event['time'] }}</p>
                <p><strong>Lokasi:</strong> {{ $event['location'] }}</p>
                <p><strong>Biaya:</strong> Rp {{ number_format($event['registration_fee'], 0, ',', '.') }}</p>
                <p><strong>Peserta:</strong> {{ $event['max_participants'] }}</p>
                <p><strong>Kategori:</strong>
                    @foreach ($event['categories'] as $speaker){{ $speaker['name'] }}@if (!$loop->last), @endif @endforeach
                </p>
                <p><strong>Keterangan:</strong> {{ $event['description'] }}</p>
                <h4 class="mb-3"><i class="bi bi-person"></i> Pembicara</h4>
                @if (!empty($event['speakers']))
                    <div class="row">
                        @foreach ($event['speakers'] as $speaker)
                            <div class="col-md-4 mb-4" style="max-width: 250px">
                                <div class="card h-100">
                                    <img src="{{ asset('assets/img/team/team-1.jpg') }}" class="img-fluid" alt=""
                                        style="height: 250px; width: 250px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $speaker['name'] }}</h5>
                                        @if (!empty($speaker['description']))
                                            <p class="card-text">{{ $speaker['description'] }}</p>
                                        @else
                                            <p class="card-text"><em>Deskripsi belum tersedia.</em></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>Tidak Ada Pembicara</p>
                @endif
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ url('/event/' . $event['idevents']) . '/daftar' }}"
                        class="btn btn-lg btn-primary daftar-btn">Daftar</a>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const token = localStorage.getItem("token");

            if (!token) {
                document.querySelectorAll(".daftar-btn").forEach(btn => {
                    btn.addEventListener("click", function (e) {
                        e.preventDefault();

                        Swal.fire({
                            icon: 'warning',
                            title: '',
                            text: 'Silakan masuk atau daftar terlebih dahulu untuk mendaftar event.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    });


                    btn.innerText = "Daftar";
                });
            }
        });
    </script>
@endsection

{{-- @if (!empty($speaker['photo_path']))
<img src="{{ $speaker['photo_path'] }}" class="card-img-top" alt="{{ $speaker['name'] }}"
    style="height: 200px; object-fit: cover;">
@else
<img src="/assets/img/default-speaker.jpg" class="card-img-top" alt="Default Speaker"
    style="height: 200px; object-fit: cover;">
@endif --}}