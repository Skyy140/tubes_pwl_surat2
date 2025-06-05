<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Dasbor')</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

</head>

<body>
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="#" id="eventku-logo" class="logo d-flex align-items-center me-auto">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">EventKu</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Beranda<br></a></li>
                    <li><a href="/#services" class="{{ request()->is('/') ? '' : '' }}">Event</a></li>
                    <li><a href="/#team" class="{{ request()->is('/') ? '' : '' }}">Tim</a></li>
                    <li id="eventSayaMenu"><a href="{{ route('event.saya') }}"
                            class="{{ request()->is('event-saya') ? 'active' : '' }}">Event Saya</a></li>
                    <li id="riwayatPembayaranMenu"><a href="{{ route('riwayat.pembayaran') }}"
                            class="{{ request()->is('riwayat-pembayaran') ? 'active' : '' }}">Riwayat Pembayaran</a>
                    </li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>


            <div id="auth-section">
                <a class="btn-getstarted" href="/login">Daftar / Masuk</a>
            </div>

            <!-- Profile Dropdown Script and Style -->
            <link href="{{ asset('assets/css/profile-dropdown.css') }}" rel="stylesheet">

        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <footer id="footer" class="footer">

            <div class="container">
                <div class="copyright text-center ">
                    <p>© <span>Hak Cipta</span> <strong class="px-1 sitename">Vesperr</strong> <span>Semua Hak
                            Dilindungi</span></p>
                </div>
                <div class="social-links d-flex justify-content-center">
                    <a href=""><i class="bi bi-twitter-x"></i></a>
                    <a href=""><i class="bi bi-facebook"></i></a>
                    <a href=""><i class="bi bi-instagram"></i></a>
                    <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
                <div class="credits">
                    <!-- All the links in the footer should remain intact. -->
                    <!-- You can delete the links only if you've purchased the pro version. -->
                    <!-- Licensing information: https://bootstrapmade.com/license/ -->
                    <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
                    Dirancang oleh <a href="https://bootstrapmade.com/">BootstrapMade</a>
                </div>
            </div>

        </footer>

        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

        <!-- Preloader -->
        <div id="preloader"></div>
    </footer>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const eventkuLogo = document.getElementById("eventku-logo");
            // Helper: get role from JWT
            function getRoleFromToken(token) {
                if (!token) return null;
                try {
                    const payload = JSON.parse(atob(token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')));
                    return payload.role || payload.roles_idroles || null;
                } catch (e) {
                    return null;
                }
            }

            // Logo EventKu click logic
            if (eventkuLogo) {
                eventkuLogo.addEventListener("click", function(e) {
                    e.preventDefault();
                    let role = getRoleFromToken(token);
                    if (role == 3 || role == "3" || role == "keuangan") {
                        window.location.href = "/keuangan/dashboard";
                    } else {
                        window.location.href = "/";
                    }
                });
            }
            const token = localStorage.getItem("token");
            const authSection = document.getElementById("auth-section");
            const tokenBaru = localStorage.getItem("tokenBaru");

            // Helper: get role from JWT
            function getRoleFromToken(token) {
                if (!token) return null;
                try {
                    const payload = JSON.parse(atob(token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')));
                    return payload.role || payload.roles_idroles || null;
                } catch (e) {
                    return null;
                }
            }
            const eventSayaMenu = document.getElementById('eventSayaMenu');
            const riwayatPembayaranMenu = document.getElementById('riwayatPembayaranMenu');
            let role = getRoleFromToken(token);
            if (role != "member") {
                if (eventSayaMenu) eventSayaMenu.style.display = 'none';
                if (riwayatPembayaranMenu) riwayatPembayaranMenu.style.display = 'none';
            }
            if (token) {
                let role = getRoleFromToken(token);
                let profileUrl = "/member/profile";
                if (role == 3 || role == "3" || role == "keuangan") {
                    profileUrl = "/keuangan/profile";
                }
                authSection.innerHTML = `
                <div class="profile-dropdown" id="profileDropdown">
                    <button class="profile-dropdown-toggle" type="button" id="profileDropdownBtn">
                        <i class="bi bi-person-circle" style="font-size:18px;"></i> Profil
                    </button>
                    <div class="profile-dropdown-menu" id="profileDropdownMenu">
                        <a href="${profileUrl}">Profile</a>
                        <a href="#" id="logoutBtn">Logout</a>
                    </div>
                </div>
                `;

                // Dropdown toggle logic
                const dropdown = document.getElementById('profileDropdown');
                const btn = document.getElementById('profileDropdownBtn');
                const menu = document.getElementById('profileDropdownMenu');
                let isOpen = false;

                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isOpen = !isOpen;
                    dropdown.classList.toggle('open', isOpen);
                });
                document.addEventListener('click', function(e) {
                    if (isOpen && !dropdown.contains(e.target)) {
                        isOpen = false;
                        dropdown.classList.remove('open');
                    }
                });

                // Tampilkan notif login hanya jika tokenBaru ada
                if (tokenBaru) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Login berhasil, Selamat datang!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    localStorage.removeItem("tokenBaru");
                }

                // Logout handler
                document.getElementById("logoutBtn").addEventListener("click", function() {
                    localStorage.removeItem("token");
                    localStorage.removeItem("tokenBaru");
                    window.location.href = "/";
                });
            }
        });
    </script>




</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</html>
