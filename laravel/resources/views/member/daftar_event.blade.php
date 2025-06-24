@extends('layout.index')

@section('title', 'Daftar Event')

@section('content')
    <main class="main">
        <section class="section py-5">
            <div class="container">
                <div class="row gy-5">
                    {{-- Kiri: Detail Event --}}
                    <div class="col-lg-7">
                        <img src="http://localhost:3000{{ $daftar_event['poster_path'] }}"
                            class="img-fluid rounded mb-4 shadow-sm" alt="Event Image"
                            style="max-height: 350px; object-fit: cover; width: 100%;">


                        <h2 class="mb-4 fw-bold text-primary">{{ $daftar_event['name'] }}</h2>

                        {{-- Card Info Event --}}
                        <div class="card shadow-sm rounded mb-4 border-0">
                            <div class="card-body">
                                <div class="row gx-4 gy-3 fs-6 text-secondary">
                                    <div class="col-6 fw-semibold">Tanggal</div>
                                    <div class="col-6">
                                        {{ \Carbon\Carbon::parse($daftar_event['date_start'])->translatedFormat('d M Y') }}
                                        - {{ \Carbon\Carbon::parse($daftar_event['date_end'])->translatedFormat('d M Y') }}
                                    </div>

                                    <div class="col-6 fw-semibold">Jam</div>
                                    <div class="col-6">{{ $daftar_event['time'] }}</div>

                                    <div class="col-6 fw-semibold">Lokasi</div>
                                    <div class="col-6">{{ $daftar_event['location'] }}</div>

                                    <div class="col-6 fw-semibold">Biaya</div>
                                    <div class="col-6 text-success fw-bold">Rp
                                        {{ number_format($daftar_event['registration_fee'], 0, ',', '.') }}
                                    </div>

                                    <div class="col-6 fw-semibold">Peserta</div>
                                    <div class="col-6">{{ $daftar_event['max_participants'] }}</div>

                                    <div class="col-6 fw-semibold">Kategori</div>
                                    <div class="col-6 text-wrap">
                                        @foreach ($daftar_event['categories'] as $category)
                                            <span class="badge bg-info text-dark me-1 mb-1">{{ $category['name'] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi Event --}}
                        <div class="card shadow-sm rounded mb-5 border-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Keterangan</h5>
                                <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $daftar_event['description'] }}
                                </p>
                            </div>
                        </div>

                        {{-- Pembicara --}}
                        <h4 class="mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-person fs-4 text-primary"></i> Pembicara
                        </h4>
                        @if (!empty($daftar_event['speakers']))
                            <div class="row g-4">
                                @foreach ($daftar_event['speakers'] as $speaker)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 rounded hover-shadow">
                                            <img src="{{ asset('assets/img/team/team-1.jpg') }}" class="card-img-top"
                                                alt="{{ $speaker['name'] }}" style="height: 180px; object-fit: cover;">

                                            <div class="card-body">
                                                <h5 class="card-title">{{ $speaker['name'] }}</h5>
                                                <p class="card-text text-muted small" style="min-height: 60px;">
                                                    @if (!empty($speaker['description']))
                                                        {{ Str::limit($speaker['description'], 100) }}
                                                    @else
                                                        <em>Deskripsi belum tersedia.</em>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted fst-italic">Tidak Ada Pembicara</p>
                        @endif
                    </div>

                    {{-- Kanan: Form Daftar --}}
                    <div class="col-lg-5">
                        <div class="card shadow-sm rounded p-4 border-0">
                            <h4 class="mb-4 fw-semibold text-primary">Form Pendaftaran</h4>
                            <form id="registration-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Masukkan nama lengkap" required readonly
                                        style="background-color: rgb(238, 237, 237)">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="email@example.com" required readonly
                                        style="background-color: rgb(238, 237, 237)">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Pilihan Sesi</label>



                                    @if (!empty($daftar_event['details']))
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="checkAllSessions">
                                            <label class="form-check-label" for="checkAllSessions">
                                                Semua Sesi
                                            </label>
                                        </div>
                                        @foreach ($daftar_event['details'] as $detail)
                                            <div class="form-check">
                                                <input class="form-check-input sesi-checkbox" type="checkbox" name="sesi[]"
                                                    value="{{ $detail['idevent_detail'] }}" id="sesi_{{ $loop->index }}">
                                                <label class="form-check-label" for="sesi_{{ $loop->index }}">
                                                    {{ $detail['sesi'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">Belum ada sesi tersedia.</p>
                                    @endif
                                </div>


                                <button type="submit" class="btn btn-primary w-100 fw-bold daftar-btn">Daftar
                                    Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.3s ease;
        }

        .daftar-btn {
            transition: background-color 0.3s ease;
        }

        .daftar-btn:hover {
            background-color: #004085;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
    <script>
        async function loadProfile() {
            const token = localStorage.getItem('token');
            if (!token) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Silakan login terlebih dahulu.</div>';
                return;
            }
            let decoded;
            try {
                decoded = window.jwt_decode(token);
            } catch (e) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Token tidak valid. Silakan login ulang.</div>';
                return;
            }
            const userId = decoded.id;
            try {
                const res = await fetch(`http://localhost:3000/api/users/profile/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (!res.ok) throw new Error('Gagal mengambil data profil');
                const user = await res.json();
                document.getElementById('name').value = user.name || '';
                document.getElementById('email').value = user.email || '';
                // document.getElementById('status').value = user.status || '';
            } catch (err) {
                document.getElementById('profileMessage').innerHTML = '<div class="alert alert-danger">' + err.message +
                    '</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadProfile);

        // sesi
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAllSessions');
            const sesiCheckboxes = document.querySelectorAll('.sesi-checkbox');

            checkAll.addEventListener('change', function() {
                sesiCheckboxes.forEach(cb => cb.checked = checkAll.checked);
            });

            sesiCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        checkAll.checked = false;
                    } else {
                        if ([...sesiCheckboxes].every(x => x.checked)) {
                            checkAll.checked = true;
                        }
                    }
                });
            });
        });

        // daftar 
        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token');
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Silakan login terlebih dahulu.',
                });
                return;
            }

            const decoded = jwt_decode(token);
            const userId = decoded.id;

            const selectedSesi = [...document.querySelectorAll('.sesi-checkbox:checked')]
                .map(cb => cb.value);

            if (selectedSesi.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Sesi',
                    text: 'Pilih setidaknya satu sesi.',
                });
                return;
            }

            const konfirmasi = await Swal.fire({
                title: 'Konfirmasi Pendaftaran',
                text: 'Apakah kamu yakin ingin mendaftar pada sesi yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Daftar!',
                cancelButtonText: 'Batal'
            });

            if (!konfirmasi.isConfirmed) {
                return;
            }
            const eventId = {{ $daftar_event['idevents'] }};
            try {
                const res = await fetch("http://localhost:3000/api/events/registrasi", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        userId: userId,
                        eventId: eventId,
                        sesi: selectedSesi
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Registrasi berhasil!',
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat registrasi.',
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat registrasi.',
                });
            }
        });
    </script>

@endsection
