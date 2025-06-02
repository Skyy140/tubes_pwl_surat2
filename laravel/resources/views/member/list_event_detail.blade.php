@extends('layout.index')

@section('title', 'Detail Event dengan QR')

@section('content')
    <main class="main py-5">
        <div class="container">
            <h2 class="mb-4 fw-bold" id="eventTitle">Detail Event</h2>
            <div id="eventDetail"></div>
        </div>
    </main>

    <script>
        const eventId = window.location.pathname.split('/').pop();

        async function loadEventDetail() {
            try {
                const res = await fetch(`http://localhost:3000/api/events/event-detail-with-qr/${eventId}`);
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal ambil detail event');

                document.getElementById('eventTitle').innerText = data.name;

                const qrCodePath = data.registrasi?.[0]?.qr_code ?
                    `http://localhost:3000${data.registrasi.qr_code}` :
                    '';

                let html = `
                    <p><strong>Deskripsi:</strong> ${data.description || '-'}</p>
                    <p><strong>Tanggal:</strong> ${data.date_start || '-'} - ${data.date_end || '-'}</p>
                    <p><strong>Lokasi:</strong> ${data.location || '-'}</p>
                    ${qrCodePath ? `
                            <p>QR KAMU</p>
                            <img src="${qrCodePath}" alt="QR Code" style="max-width: 200px;">` : '<p><em></em></p>'}
                    <h4 class="mt-4">Sesi</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>No</th><th>Sesi</th><th>Tanggal</th><th>Waktu</th></tr>
                        </thead>
                        <tbody>
                            ${(data.details || []).map((sesi, i) => `
                                        <tr>
                                            <td>${i + 1}</td>
                                            <td>${sesi.sesi}</td>
                                            <td>${sesi.date}</td>
                                            <td>${sesi.time_start} - ${sesi.time_end}</td>
                                        </tr>
                                    `).join('')}
                        </tbody>
                    </table>
                `;

                document.getElementById('eventDetail').innerHTML = html;
            } catch (err) {
                document.getElementById('eventDetail').innerHTML =
                    `<div class="alert alert-danger">${err.message}</div>`;
            }
        }

        loadEventDetail();
    </script>
@endsection
