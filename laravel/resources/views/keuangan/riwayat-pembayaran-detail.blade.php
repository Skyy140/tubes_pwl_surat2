@extends('layout.index')

@section('title', 'Detail Event dengan QR')

@section('content')
    <main class="main py-5">
        <div class="container">
            <div id="profileMessage"></div> 
            
            <h2 class="mb-4 fw-bold" id="eventTitle">Detail Riwayat Pembayaran</h2>
            <div id="eventDetail"></div>

            <input type="text" id="name" hidden />
            <input type="email" id="email" hidden />
        </div>
    </main>

    {{-- <script>
        async function main() {
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
            const eventId = window.location.pathname.split('/').pop();


            try {
                const res = await fetch(`http://localhost:3000/api/events/keuangan/riwayat-pembayaran-detail/${eventId}/${userId}`);
                const data = await res.json();
                console.log('Data dari API:', data);
                if (!res.ok) throw new Error(data.message || 'Gagal ambil detail event');

                document.getElementById('eventTitle').innerText = data.name;

                let html = `
                    <p><strong>Deskripsi:</strong> ${data.bukti_pdf || '-'}</p>
                    <p><strong>Tanggal:</strong> ${data.date_start || '-'} - ${data.date_end || '-'}</p>
                    <p><strong>Lokasi:</strong> ${data.location || '-'}</p>
                    <img src="${data.payment?.payment_proof_path || '-'}" alt="Bukti Pembayaran" class="img-fluid mb-3" style="max-width: 200px;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sesi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(data.registrasi?.registrasiDetail || []).map((detail, i) => {
                                const sesi = data.details?.find(s => s.idevent_detail === detail.event_detail_idevent_detail);

                                if (!sesi) return ''; 


                                return `
                                    <tr>
                                        <td>${i + 1}</td>
                                        <td>${sesi.sesi}</td>
                                        <td>${sesi.date}</td>
                                        <td>${sesi.time_start} - ${sesi.time_end}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                `;
                

                document.getElementById('eventDetail').innerHTML = html;
            } catch (err) {
                document.getElementById('eventDetail').innerHTML =
                    `<div class="alert alert-danger">${err.message}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', main);
    </script> --}}
    <script>
        async function main() {
            const pathParts = window.location.pathname.split('/');
            const eventId = pathParts[pathParts.length - 2]; // ambil 11
            const userId = pathParts[pathParts.length - 1];

            try {
                const res = await fetch(`http://localhost:3000/api/events/keuangan/riwayat-pembayaran-detail/${eventId}/${userId}`);
                const apiData = await res.json(); // Ganti nama variabel agar tidak ambigu
                console.log('Data dari API:', apiData);

                if (!res.ok) throw new Error(apiData.message || 'Gagal ambil detail event');

                // Pastikan apiData adalah array dan ambil elemen pertama
                if (!Array.isArray(apiData) || apiData.length === 0) {
                    document.getElementById('eventDetail').innerHTML =
                        '<div class="alert alert-warning">Tidak ada detail pembayaran yang ditemukan.</div>';
                    return;
                }

                const data = apiData[0]; // Ambil objek pertama dari array

                // Mendapatkan nama event dari eventDetail di dalam registrasiDetail (jika ada)
                // Atau Anda mungkin perlu menambahkan properti nama event langsung di objek data yang dikembalikan API
                // const eventName = data.registrasiDetail && data.registrasiDetail.length > 0
                //     ? data.registrasiDetail[0].eventDetail.events.name // Sesuaikan path ini sesuai struktur API Anda
                //     : 'Detail Riwayat Pembayaran'; // Fallback jika tidak ditemukan

                // document.getElementById('eventTitle').innerText = eventName;
                const eventName = data.name || 'Detail Riwayat Pembayaran'; // Langsung dari objek data utama
                document.getElementById('eventTitle').innerText = eventName;

                const paymentProofPath = data.payment && data.payment.length > 0 ? data.payment[0].payment_proof_path : '';
                let html = `
                    <p><strong>Tanggal Registrasi:</strong> ${new Date(data.registration_date).toLocaleDateString('id-ID') || '-'}</p>
                    <p><strong>Status:</strong> ${data.status || '-'}</p>
                    <p></strong>Bukti Pembayaran:</strong></p>
                    <img src="http://localhost:3000${paymentProofPath}" alt="Bukti Pembayaran" class="img-fluid mb-3" style="max-width: 200px;">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sesi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(data.registrasiDetail || []).map((detail, i) => {
                                const sesi = detail.eventDetail; // Sesuaikan jika struktur API berbeda
                                if (!sesi) return '';

                                return `
                                    <tr>
                                        <td>${i + 1}</td>
                                        <td>${sesi.sesi || '-'}</td>
                                        <td>${sesi.date || '-'}</td>
                                        <td>${sesi.time_start || '-'} - ${sesi.time_end || '-'}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                `;

                document.getElementById('eventDetail').innerHTML = html;
            } catch (err) {
                document.getElementById('eventDetail').innerHTML =
                    `<div class="alert alert-danger">${err.message}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', main);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode/build/jwt-decode.min.js"></script>

@endsection
