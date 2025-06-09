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
            // Helper: extract user ID from JWT in localStorage
            function getUserIdFromJWT() {
                const token = localStorage.getItem('token');
                if (!token) return null;
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    // Cek semua kemungkinan properti id yang digunakan backend
                    if (payload.id !== undefined && payload.id !== null) return String(payload.id);
                    if (payload.user_id !== undefined && payload.user_id !== null) return String(payload.user_id);
                    if (payload.userid !== undefined && payload.userid !== null) return String(payload.userid);
                    return null;
                } catch (e) {
                    return null;
                }
            }
            const loggedInUserId = getUserIdFromJWT();
            fetch('http://localhost:3000/api/events-sertif')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('sertif-table-body');
                    tbody.innerHTML = '';
                    // Filter events: only show if coordinator matches logged-in user
                    const filteredEvents = data.filter(event => {
                        // event.coordinator could be string or number, so cast both to string for strict comparison
                        return String(event.coordinator) === String(loggedInUserId);
                    });
                    if (filteredEvents.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="7" class="text-center">Tidak ada event yang Anda koordinatori.</td></tr>`;
                        return;
                    }
                    filteredEvents.forEach(event => {
                        const eventRow = document.createElement('tr');
                        // Siapkan HTML untuk kolom sesi
                        let sesiHtml = '';
                        let actionHtml = '';
                        if (event.details && event.details.length > 0) {
                            sesiHtml = event.details.map(detail => `
                                <div class="d-flex flex-column flex-md-row align-items-stretch mb-1" style="gap: 0.5rem;">
                                    <div class="flex-fill mb-1 mb-md-0" style="word-break:break-word;">
                                        <span class="font-weight-bold">${detail.sesi}</span>
                                    </div>
                                    <div class="text-right" style="min-width:110px;">
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
                                let hasUser = false;
                                html +=
                                    `<table class="table table-bordered"><thead><tr><th>Nama User</th><th class='text-center'>Action</th></tr></thead><tbody>`;
                                users.forEach(user => {
                                    if (!user.user_name || user.user_name === '-')
                                        return; // skip if no name
                                    hasUser = true;
                                    let sertifHtml = '';
                                    if (user.certificate_path && user.certificate_path !== '') {
                                        const url = 'http://localhost:3000' + user
                                            .certificate_path;
                                        if (/\.(jpg|jpeg|png|gif)$/i.test(user
                                                .certificate_path)) {
                                            sertifHtml =
                                                `<div class="d-flex justify-content-center"><a href="${url}" target="_blank"><img src="${url}" alt="Sertifikat" style="max-width:80px;max-height:80px;border:1px solid #ccc;margin-bottom:4px;cursor:pointer;"></a></div>`;
                                        } else if (/\.pdf$/i.test(user.certificate_path)) {
                                            sertifHtml =
                                                `<div class="d-flex justify-content-center"><a href="${url}" target="_blank" class="btn btn-info btn-sm mb-1">Lihat Sertif (PDF)</a></div>`;
                                        }
                                    } else {
                                        sertifHtml =
                                            `<span class="text-muted">Belum ada sertifikat</span>`;
                                    }
                                    html += `<tr>
                                            <td class='align-middle'>${user.user_name}</td>
                                            <td class='text-center align-middle'>
                                                ${sertifHtml}
                                                <form class="upload-sertif-form mt-2" enctype="multipart/form-data" style="display:inline-block">
                                                    <input type="file" name="sertif" accept=".pdf,image/*" style="display:none" required />
                                                    <input type="hidden" name="user_id" value="${user.user_id}" />
                                                    <input type="hidden" name="sesi" value="${sesiName}" />
                                                    <input type="hidden" name="event_id" value="${eventId}" />
                                                    <button type="button" class="btn btn-primary btn-sm btn-upload-trigger">Upload</button>
                                                    <button type="submit" class="btn btn-success btn-sm" style="display:none">Kirim</button>
                                                </form>
                                            </td>
                                        </tr>`;
                                });
                                html += `</tbody></table>`;
                                if (!hasUser) {
                                    html =
                                        '<div class="alert alert-info">Belum ada peserta yang hadir.</div>';
                                }
                            } else {
                                html =
                                    '<div class="alert alert-info">Belum ada peserta yang hadir.</div>';
                            }
                            document.getElementById('modal-user-attend-body').innerHTML = html;
                            $('#modalUserAttend').modal('show');

                            // Event: klik tombol upload, trigger input file
                            document.querySelectorAll('.btn-upload-trigger').forEach(btn => {
                                btn.onclick = function() {
                                    const form = btn.closest('form');
                                    const fileInput = form.querySelector(
                                        'input[type="file"]');
                                    fileInput.click();
                                };
                            });
                            // Event: input file change, show submit
                            document.querySelectorAll('.upload-sertif-form input[type="file"]').forEach(
                                input => {
                                    input.onchange = function() {
                                        const form = input.closest('form');
                                        form.querySelector('button[type="submit"]').style
                                            .display = '';
                                    };
                                });
                            // Event: submit upload form
                            document.querySelectorAll('.upload-sertif-form').forEach(form => {
                                form.onsubmit = async function(e) {
                                    e.preventDefault();
                                    const fileInput = form.querySelector(
                                        'input[type="file"]');
                                    if (!fileInput.files[0]) return alert(
                                        'Pilih file sertifikat!');
                                    const formData = new FormData(form);
                                    try {
                                        const res = await fetch(
                                            'http://localhost:3000/api/sertif/upload', {
                                                method: 'POST',
                                                body: formData
                                            });
                                        const data = await res.json();
                                        if (res.ok) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Upload Berhasil',
                                                text: 'Sertifikat berhasil diupload!',
                                                timer: 1800,
                                                showConfirmButton: false
                                            });
                                            form.querySelector('button[type="submit"]')
                                                .style.display = 'none';
                                            fileInput.value = '';
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Gagal',
                                                text: data.message ||
                                                    'Upload gagal!'
                                            });
                                        }
                                    } catch (err) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Gagal',
                                            text: 'Upload gagal!'
                                        });
                                    }
                                };
                            });
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
