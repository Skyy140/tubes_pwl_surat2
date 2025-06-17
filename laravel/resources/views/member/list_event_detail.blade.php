@extends('layout.index')

@section('title', 'Detail Event dengan QR')

@section('content')
    <main class="main py-5">
        <div class="container">
            <div id="profileMessage"></div> 
            
            <h2 class="mb-4 fw-bold" id="eventTitle">Detail Event</h2>
            <div id="eventDetail"></div>

            <input type="text" id="name" hidden />
            <input type="email" id="email" hidden />
        </div>
    </main>

    <script>
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
                const res = await fetch(`http://localhost:3000/api/users/profile/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (!res.ok) throw new Error('Gagal mengambil data profil');
                const user = await res.json();
                // pastikan elemen ada, kalau inputnya hidden maka tidak masalah
                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');
                if (nameInput) nameInput.value = user.name || '';
                if (emailInput) emailInput.value = user.email || '';
            } catch (err) {
                document.getElementById('profileMessage').innerHTML =
                    `<div class="alert alert-danger">${err.message}</div>`;
            }

            try {
                const res = await fetch(`http://localhost:3000/api/events/event-detail-with-qr/${eventId}?userId=${userId}`);
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal ambil detail event');

                document.getElementById('eventTitle').innerText = data.name;
                // const sertifikatPath = data.registrasi?.registrasiDetail?.attendances.certificate_path;
                // const sertifikatUrl = sertifikatPath ? `http://localhost:3000${sertifikatPath}` : '#';
                // const sertifikatDownloadUrl = sertifikatPath
                //     ? `http://localhost:3000/download/${sertifikatPath.split('/').pop()}`
                //     : '#';
                const attendance = data.registrasi?.registrasiDetail?.flatMap(detail => detail.hadir)?.find(a => a.certificate_path);
                const sertifikatPath = attendance?.certificate_path;

                const sertifikatUrl = sertifikatPath ? `http://localhost:3000${sertifikatPath}` : '#';

                const qrCodePath = data.registrasi?.qr_code
                    ? `http://localhost:3000${data.registrasi.qr_code}`
                    : '';
                let html = `
                    <p><strong>Deskripsi:</strong> ${data.description || '-'}</p>
                    <p><strong>Tanggal:</strong> ${data.date_start || '-'} - ${data.date_end || '-'}</p>
                    <p><strong>Lokasi:</strong> ${data.location || '-'}</p>
                    ${qrCodePath ? `
                        <p>QR KAMU</p>
                        <img src="${qrCodePath}" alt="QR Code" style="max-width: 200px;">
                    ` : '<p><em>Tidak ada QR</em></p>'}

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sesi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(data.registrasi?.registrasiDetail || []).map((detail, i) => {
                                const sesi = data.details?.find(s => s.idevent_detail === detail.event_detail_idevent_detail);

                                if (!sesi) return ''; 

                                const certPath = detail.hadir?.[0]?.certificate_path;
                                const certLink = certPath ? `<a href="http://localhost:3000${certPath}" target="_blank">Lihat</a>` : '-';

                                return `
                                    <tr>
                                        <td>${i + 1}</td>
                                        <td>${sesi.sesi}</td>
                                        <td>${sesi.date}</td>
                                        <td>${sesi.time_start} - ${sesi.time_end}</td>
                                        <td>${certLink}</td>
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
