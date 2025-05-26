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
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('qr-result').innerHTML =
                `<div class='alert alert-success'>QR Terdeteksi: ${decodedText}</div>`;
            // Lakukan aksi lain, misal AJAX ke server
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
</body>

</html>
