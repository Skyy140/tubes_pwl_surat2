<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, {{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Scan QR - Panitia</title>
    <link rel="icon" type="image/png"
        href="https://kompaspedia.kompas.id/wp-content/uploads/2021/07/logo_universitas-kristen-maranatha.png">
    <link href="{{ asset('assetsadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('assetsadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assetsAdmin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('layout.sidebarPanit')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layout.navAdmin')
                <div class="container mt-4">
                    <h2>Scan QR Code</h2>
                    <div class="card mt-3">
                        <div class="card-body">
                            <div id="qr-reader" style="width: 100%; min-height: 300px;"></div>
                            <div id="qr-result" class="mt-3"></div>
                            <div id="qr-error" class="mt-2 text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; All Rights Reserved </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assetsadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assetsadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assetsadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assetsadmin/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('assetsadmin/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assetsAdmin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assetsAdmin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Modal HTML for session selection
        function showSessionModal(sessions, onConfirm) {
            // Remove existing modal if any
            const oldModal = document.getElementById('sessionSelectModal');
            if (oldModal) oldModal.remove();
            let html = `<div class="modal fade" id="sessionSelectModal" tabindex="-1" role="dialog" aria-labelledby="sessionSelectModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sessionSelectModalLabel">Pilih Sesi untuk Absen</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="sessionSelectForm">
                                <div class="form-group">
                                    <label>Pilih sesi yang ingin diupdate ke hadir:</label>`;
            sessions.forEach((sesi, idx) => {
                html += `<div class="form-check">
                    <input class="form-check-input sesi-checkbox" type="checkbox" name="sesi" value="${sesi.idregistrations_detail}" id="sesi_${idx}">
                    <label class="form-check-label" for="sesi_${idx}">
                        ${sesi.sesi} <span class='text-muted small'>(${sesi.date || ''} ${sesi.time_start || ''}-${sesi.time_end || ''})</span>
                    </label>
                </div>`;
            });
            html += `</div>
                                <button type="submit" class="btn btn-primary mt-3">Update Absen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            $('#sessionSelectModal').modal('show');
            document.getElementById('sessionSelectForm').onsubmit = function(e) {
                e.preventDefault();
                const checked = [...document.querySelectorAll('.sesi-checkbox:checked')].map(cb => cb.value);
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Sesi',
                        text: 'Pilih setidaknya satu sesi.'
                    });
                    return;
                }
                $('#sessionSelectModal').modal('hide');
                onConfirm(checked);
            };
        }

        function onScanSuccess(decodedText, decodedResult) {
            try {
                const data = JSON.parse(decodedText);
                // Struktur baru: { registrasi_id, user_id, event_id, idregistrations_detail }
                const html = `
                <div class="alert alert-success">
                    <strong>QR Terdeteksi!</strong><br><br>
                    <b>Registrasi ID:</b> ${data.registrasi_id}<br>
                    <b>User ID:</b> ${data.user_id}<br>
                    <b>Event ID:</b> ${data.event_id}<br>
                </div>
            `;
                document.getElementById('qr-result').innerHTML = html;
                document.getElementById('qr-error').innerText = '';

                // Ambil user id login dari JWT
                const token = localStorage.getItem('token');
                let userIdLogin = null;
                if (token) {
                    try {
                        userIdLogin = window.jwt_decode(token).id;
                    } catch (e) {}
                }
                if (!userIdLogin) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'User login tidak valid. Silakan login ulang.'
                    });
                    return;
                }

                // Fetch sessions for this registration, with Authorization header
                fetch(`http://localhost:3000/api/registrasi/${data.registrasi_id}/sessions`, {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    })
                    .then(async res => {
                        if (res.status === 403) {
                            const err = await res.json();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: err.message || 'Silahkan cari panitia yang membuat event ini.'
                            });
                            return null;
                        }
                        if (!res.ok) {
                            const err = await res.json();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: err.message || 'Gagal mengambil data sesi.'
                            });
                            return null;
                        }
                        return res.json();
                    })
                    .then(async sessions => {
                        if (!sessions) return;
                        if (!Array.isArray(sessions) || sessions.length === 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tidak Ada Sesi',
                                text: 'Tidak ada sesi untuk registrasi ini.'
                            });
                            return;
                        }

                        // Cek status attend untuk semua sesi user ini
                        const idList = sessions.map(s => s.idregistrations_detail);
                        // Cek status attend untuk setiap sesi (panggil endpoint attendance per sesi)
                        // Jika SEMUA sesi sudah attend, tampilkan pesan dan return
                        // Jika ADA salah satu yang belum attend, hanya tampilkan modal untuk yang belum attend
                        const attendChecks = await Promise.all(idList.map(id =>
                            fetch('http://localhost:3000/api/attendances/update-status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    idregistrations_detail: id,
                                    user_id: userIdLogin,
                                    check_only: true
                                })
                            })
                            .then(res => res.json())
                            .then(result => ({
                                id,
                                status: result.status,
                                message: result.message
                            }))
                            .catch(() => ({
                                id,
                                status: 'error',
                                message: 'Gagal cek attendance'
                            }))
                        ));
                        // Filter sesi yang BELUM attend
                        const belumAttend = attendChecks.filter(a => a.status !== 'attend').map(a => a.id);
                        if (belumAttend.length === 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sudah Hadir',
                                text: 'User sudah hadir di semua sesi yang didaftarkan.'
                            });
                            return;
                        }
                        // Filter sessions yang belum attend saja
                        const sessionsBelumAttend = sessions.filter(s => belumAttend.includes(s
                            .idregistrations_detail));
                        showSessionModal(sessionsBelumAttend, function(selectedIds) {
                            // Update attendance for selected sessions only
                            let successCount = 0;
                            let failCount = 0;
                            let failMsg = '';
                            Promise.all(selectedIds.map(id => {
                                return fetch(
                                        'http://localhost:3000/api/attendances/update-status', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                idregistrations_detail: id,
                                                user_id: userIdLogin
                                            })
                                        })
                                    .then(res => res.json())
                                    .then(result => {
                                        if (result.message && result.message.includes(
                                                'berhasil')) {
                                            successCount++;
                                        } else {
                                            failCount++;
                                            failMsg = result.message ||
                                                'Gagal update attendance';
                                        }
                                    })
                                    .catch(() => {
                                        failCount++;
                                        failMsg = 'Gagal update attendance';
                                    });
                            })).then(() => {
                                if (successCount > 0 && failCount === 0) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Status attendance berhasil diupdate!'
                                    });
                                } else if (successCount > 0 && failCount > 0) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Sebagian Berhasil',
                                        text: `Sebagian attendance berhasil diupdate. Gagal: ${failMsg}`
                                    });
                                } else {
                                    if (failMsg && failMsg.includes(
                                            'Silahkan cari panitia yang membuat event ini')) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Silahkan cari panitia yang membuat event ini.'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: failMsg || 'Gagal update attendance.'
                                        });
                                    }
                                }
                            });
                        });
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil data sesi.'
                        });
                    });
            } catch (e) {
                document.getElementById('qr-result').innerHTML = '';
                document.getElementById('qr-error').innerText = "QR tidak valid atau format salah.";
            }
            // --- END onScanSuccess ---
        }

        function onScanError(errorMessage) {
            document.getElementById('qr-error').innerText = errorMessage;
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('qr-reader')) {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({
                            video: true
                        })
                        .then(function(stream) {
                            stream.getTracks().forEach(track => track.stop());
                            const html5QrCode = new Html5Qrcode("qr-reader");
                            html5QrCode.start({
                                    facingMode: "environment"
                                }, {
                                    fps: 10,
                                    qrbox: 250
                                },
                                onScanSuccess,
                                onScanError
                            ).catch(err => {
                                document.getElementById('qr-error').innerText =
                                    'Gagal mengakses kamera: ' + err;
                            });
                        })
                        .catch(function(err) {
                            document.getElementById('qr-error').innerText =
                                'Izin kamera ditolak atau tidak tersedia.';
                        });
                } else {
                    document.getElementById('qr-error').innerText = 'Browser tidak mendukung akses kamera.';
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
