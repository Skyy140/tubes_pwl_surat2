@extends('layout.indexPanit')

@section('content')
    @if (request()->has('success'))
        <div class="alert alert-success">{{ request('success') }}</div>
    @endif
    @if (request()->has('error'))
        <div class="alert alert-danger">{{ request('error') }}</div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Tabel Sertifikat Event</h1>
        <p class="mb-4">Berikut adalah daftar event untuk sertifikat.</p>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Event Sertifikat</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTableSertif" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Event</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Sesi</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sertif-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('http://localhost:3000/api/events-sertif')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('sertif-table-body');
                    tbody.innerHTML = '';
                    data.forEach(event => {
                        const eventRow = document.createElement('tr');
                        // Siapkan HTML untuk kolom sesi
                        let sesiHtml = '';
                        let actionHtml = '';
                        if (event.details && event.details.length > 0) {
                            sesiHtml = event.details.map(detail => `
                                <div class="row align-items-center mb-1">
                                    <div class="col-7"><span class="font-weight-bold">${detail.sesi}</span></div>
                                    <div class="col-5 text-right">
                                        <button class="btn btn-success btn-sm pilih-sesi-btn" data-event-id="${event.idevents}" data-sesi="${detail.sesi}">Pilih Sesi</button>
                                    </div>
                                </div>
                            `).join('');
                            actionHtml = '';
                        } else {
                            sesiHtml = '<span class="text-muted">Tidak ada sesi</span>';
                            actionHtml = '';
                        }
                        eventRow.innerHTML = `
                            <td>${event.idevents}</td>
                            <td>${event.name}</td>
                            <td>${event.date_start ? event.date_start : '-'}</td>
                            <td>${event.date_end ? event.date_end : '-'}</td>
                            <td>${event.status ? event.status : '-'}</td>
                            <td colspan="2">${sesiHtml}</td>
                        `;
                        tbody.appendChild(eventRow);
                    });
                })
                .catch(err => {
                    document.getElementById('sertif-table-body').innerHTML =
                        `<tr><td colspan="7">Gagal memuat data</td></tr>`;
                });
            // Modal HTML
            const modalHtml = `
                <div class="modal fade" id="modalUserAttend" tabindex="-1" role="dialog" aria-labelledby="modalUserAttendLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalUserAttendLabel">Daftar User Hadir Sesi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div id="modal-user-attend-body">
                          <!-- Tabel user hadir akan diisi oleh JS -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('pilih-sesi-btn')) {
                    const eventId = e.target.getAttribute('data-event-id');
                    const sesiName = e.target.getAttribute('data-sesi');
                    // Ambil user yang hadir di sesi ini
                    fetch(
                            `http://localhost:3000/api/attendances/users?eventId=${eventId}&sesi=${encodeURIComponent(sesiName)}`
                        )
                        .then(res => res.json())
                        .then(users => {
                            let html = '';
                            if (users && users.length > 0) {
                                html +=
                                    `<table class="table table-bordered"><thead><tr><th>Nama User</th><th class='text-center'>Action</th></tr></thead><tbody>`;
                                users.forEach(user => {
                                    html +=
                                        `<tr>
                                            <td>${user.user_name}</td>
                                            <td class='text-center'><a href="#" class="btn btn-primary btn-sm upload-sertif-btn">Upload</a></td>
                                        </tr>`;
                                });
                                html += `</tbody></table>`;
                            } else {
                                html =
                                    '<div class="alert alert-info">Tidak ada user yang hadir pada sesi ini.</div>';
                            }
                            document.getElementById('modal-user-attend-body').innerHTML = html;
                            $('#modalUserAttend').modal('show');
                        })
                        .catch(() => {
                            document.getElementById('modal-user-attend-body').innerHTML =
                                '<div class="alert alert-danger">Gagal memuat data user hadir.</div>';
                            $('#modalUserAttend').modal('show');
                        });
                }
            });
        });
    </script>
@endsection
