@extends('layout.indexPanit')

@section('content')
    @if (request()->has('success'))
        <div class="alert alert-success">{{ request('success') }}</div>
    @endif
    @if (request()->has('error'))
        <div class="alert alert-danger">{{ request('error') }}</div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Tabel Event</h1>
        <p class="mb-4">Berikut adalah daftar event yang tersedia.</p>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Event</h6>
                <a href="/panit/tambah-event" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Event</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Poster</th>
                                <th>Jam</th>
                                <th>Lokasi</th>
                                <th>Biaya Registrasi</th>
                                <th>Maks. Peserta</th>
                                <th>Status</th>
                                <th>Deskripsi</th>
                                <th>Koordinator</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="event-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk detail event -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Detail Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="event-detail-body">
                    <!-- Data detail event akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('http://localhost:3000/api/events')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('event-table-body');
                    tbody.innerHTML = '';
                    data.forEach(event => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${event.idevents}</td>
                    <td>${event.name}</td>
                    <td>${event.date_start ? event.date_start : '-'}</td>
                    <td>${event.date_end ? event.date_end : '-'}</td>
                    <td>${event.poster_path ? `<img src="${event.poster_path}" alt="Poster" style="max-width:60px;max-height:60px;">` : '-'}</td>
                    <td>${event.time ? event.time : '-'}</td>
                    <td>${event.location ? event.location : '-'}</td>
                    <td>${event.registration_fee ? event.registration_fee : '-'}</td>
                    <td>${event.max_participants ? event.max_participants : '-'}</td>
                    <td>${event.status ? event.status : '-'}</td>
                    <td>${event.description ? event.description : '-'}</td>
                    <td>${event.coordinator ? event.coordinator : '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm lihat-detail-btn" data-event-id="${event.idevents}">Lihat Detail</button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    document.getElementById('event-table-body').innerHTML =
                        `<tr><td colspan="6">Gagal memuat data</td></tr>`;
                });

            // Untuk admin: tampilkan event_detail (date, sesi, time_start, time_end, description, speaker, kategori) hanya untuk event yang dipilih
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('lihat-detail-btn')) {
                    const eventId = e.target.getAttribute('data-event-id');
                    fetch(`http://localhost:3000/api/events/admin/event-details/${eventId}`)
                        .then(response => response.json())
                        .then(details => {
                            let html = ``;
                            if (details && details.length > 0) {
                                html +=
                                    `<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Nama Event</th><th>Tanggal</th><th>Sesi</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Deskripsi</th><th>Kategori</th><th>Pembicara</th></tr></thead><tbody>`;
                                details.forEach(detail => {
                                    // Kategori dari relasi event.categories
                                    let kategori = '-';
                                    if (detail.event && detail.event.categories && detail.event
                                        .categories.length > 0) {
                                        kategori = detail.event.categories.map(cat => cat.name)
                                            .join(', ');
                                    }
                                    // Speaker dari relasi speakers
                                    let speakers = '-';
                                    if (detail.speakers && detail.speakers.length > 0) {
                                        speakers = detail.speakers.map(spk => spk.name).join(
                                            ', ');
                                    }
                                    html += `<tr>`;
                                    html +=
                                        `<td>${detail.event && detail.event.name ? detail.event.name : '-'}</td>`;
                                    html += `<td>${detail.date || '-'}</td>`;
                                    html += `<td>${detail.sesi || '-'}</td>`;
                                    html += `<td>${detail.time_start || '-'}</td>`;
                                    html += `<td>${detail.time_end || '-'}</td>`;
                                    html += `<td>${detail.description || '-'}</td>`;
                                    html += `<td>${kategori}</td>`;
                                    html += `<td>${speakers}</td>`;
                                    html += `</tr>`;
                                });
                                html += `</tbody></table></div>`;
                            } else {
                                html += `<p>Tidak ada data event detail.</p>`;
                            }
                            document.getElementById('event-detail-body').innerHTML = html;
                            $('#eventDetailModal').modal('show');
                        })
                        .catch(err => {
                            document.getElementById('event-detail-body').innerHTML =
                                '<div class="alert alert-danger">Gagal memuat data event detail</div>';
                            $('#eventDetailModal').modal('show');
                        });
                }
            });
        });
    </script>
@endsection
