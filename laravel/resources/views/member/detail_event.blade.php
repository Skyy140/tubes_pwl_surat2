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
                @if (!empty($event['details']))
                    <p class="mb-3"><i class=""></i>
                        @foreach ($event['details'] as $detail)
                            @foreach ($detail['speakers'] as $speaker)
                                {{ $speaker['name'] }}@if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @endforeach
                    </p>
                @else
                    <p>Tidak Ada Nama</p>
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