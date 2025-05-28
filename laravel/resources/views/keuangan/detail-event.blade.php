@extends('layout.index')

@section('title', 'Detail Event')

@section('content')
    <main class="main py-5">
        <div class="container">
            <h2 class="mb-4 fw-bold">Detail Event</h2>

            <div id="eventDetail">
                <p>Memuat data...</p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const eventId = window.location.pathname.split('/').pop();
            const detailContainer = document.getElementById('eventDetail');

            try {
                const res = await fetch(`http://localhost:3000/api/events/${eventId}`);
                const data = await res.json();

                if (!res.ok) throw new Error(data.message || 'Gagal mengambil detail event');
                const detailHtml = `
                <ul class="list-group">
                  <li class="list-group-item"><strong>ID Event:</strong> ${data.idevents}</li>
                  <li class="list-group-item"><strong>Nama:</strong> ${data.name}</li>
                  <li class="list-group-item">
                    <strong>Sesi:</strong>
                    ${data.details && data.details.length > 0 ? `
                      <ul>
                       ${data.details.map(d => {
                    const date = new Date(d.date);
                    const formattedDate = new Intl.DateTimeFormat('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    }).format(date);

                    return `<li>${d.sesi || '-'} - ${formattedDate} (${d.time_start} - ${d.time_end})</li>`;
                }).join('')}

                      </ul>
                    ` : 'Tidak ada detail'}
                  </li>
                </ul>
              `;
                detailContainer.innerHTML = detailHtml;

            } catch (err) {
                detailContainer.innerHTML = `<p class="text-danger">${err.message}</p>`;
            }
        });
    </script>
@endsection