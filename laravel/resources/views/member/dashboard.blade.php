@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">

            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1>Temukan Acara yang Menarik hanya di EventKu</h1>
                        <p>Kami adalah platform untuk menemukan dan mendaftar acara menarik dengan mudah.</p>
                        <div class="d-flex">
                            <a href="#services" class="btn-get-started">Get Started</a>
                            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8"
                                class="glightbox btn-watch-video d-flex align-items-center"></a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img">
                        <img src="{{ asset('assets/img/hero-img.png') }}" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>

        </section>
        <!-- /Hero Section -->

        <!-- Clients Section -->
        <section id="clients" class="clients section light-background">

            <div class="container" data-aos="fade-up">

                <div class="row gy-4">

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-1.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-2.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-3.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-4.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-5.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('assets/img/clients/client-6.png') }}" class="img-fluid" alt="">
                    </div><!-- End Client Item -->

                </div>

            </div>

        </section>
        <!-- /Clients Section -->

        <!-- Services Section -->
        <section id="services" style="padding-bottom: 0px" class="services section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Daftar Event</h2>
                <p>Berikut ini adalah beberapa event terbaru yang menarik</p>
            </div><!-- End Section Title -->

        </section>
        <!-- /Services Section -->


        <section id="alt-services" class="alt-services section py-1">
            <div class="container mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button class="btn btn-outline-primary btn-sm category-filter" data-id="all">Semua</button>
                    @foreach ($categories as $category)
                        <button class="btn btn-outline-primary btn-sm category-filter"
                            data-id="{{ $category['idcategory'] }}">
                            {{ $category['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="container">
                <div class="d-flex justify-content-center mt-4 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <nav>
                        <ul class="pagination" id="pagination-container"></ul>
                    </nav>
                </div>
                <div class="row gy-4" id="event-list" data-aos="fade-up" data-aos-delay="100">
                    @foreach ($events as $event)
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="card shadow-sm h-100 border-0 position-relative overflow-hidden">
                                <div class="position-relative">
                                    <img src="{{ asset('assets/img/services-1.jpg') }}" class="card-img-top"
                                        alt="Event Image">
                                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-wrap gap-1">
                                        @if (!empty($event['categories']))
                                            @foreach ($event['categories'] as $category)
                                                <span class="badge bg-success">{{ $category['name'] }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">Tidak ada kategori</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-2">{{ $event['name'] }}</h5>
                                    <p class="mb-1"><i class="bi bi-calendar-event"></i> {{ $event['date_start'] }} -
                                        {{ $event['date_end'] }}
                                    </p>
                                    <p class="mb-1"><i class="bi bi-clock"></i> {{ $event['time'] }}</p>
                                    <p class="mb-1"><i class="bi bi-geo-alt"></i>
                                        {{ $event['location'] ?? 'Lokasi belum ditentukan' }}</p>
                                    <p class="mb-1"><i class="bi bi-cash-stack"></i> Biaya:
                                        {{ $event['registration_fee'] }}</p>
                                    <p class="mb-1"><i class="bi bi-people"></i> Peserta:
                                        {{ $event['max_participants'] }}</p>
                                    @if (!empty($event['details']))
                                        <p class="mb-3"><i class="bi bi-person"></i> Sesi:
                                            @foreach ($event['details'] as $detail)
                                                {{ $detail['sesi'] }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p>Tidak Ada Sesi</p>
                                    @endif 
                                </div>
                                <div class="card-footer bg-transparent border-top-0 text-end">
                                    <a href="{{ route('event.detail', ['id' => $event['idevents']]) }}}"
                                        class="btn btn-sm btn-outline-primary me-2">Lihat Detail</a>
                                    {{-- <a href="{{ url('/event/' . $event['idevents']) . '/daftar' }}"
                                        class="btn btn-sm btn-outline-primary">Daftar</a> --}}
                                    <a href="{{ route('event.daftar', ['id' => $event['idevents']]) }}"
                                        class="btn btn-sm btn-outline-primary daftar-btn">Daftar</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- <a href="{{ route('riwayat-event', ['user_id' => auth()->user()->id ?? 1]) }}" class="btn btn-primary"
                style="margin-bottom: 20px; display: inline-block;">
                Lihat Riwayat Event
            </a> --}}

        </section>

        <!-- /Alt Services Section -->

        <!-- Team Section -->
        <section id="team" class="team section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Team</h2>
                <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="{{ asset('assets/img/team/team-1.jpg') }}" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Walter White</h4>
                                <span>Chief Executive Officer</span>
                            </div>
                        </div>
                    </div><!-- End Team Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="{{ asset('assets/img/team/team-2.jpg') }}" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Sarah Jhonson</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                    </div><!-- End Team Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="{{ asset('assets/img/team/team-3.jpg') }}" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>William Anderson</h4>
                                <span>CTO</span>
                            </div>
                        </div>
                    </div><!-- End Team Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="{{ asset('assets/img/team/team-4.jpg') }}" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Amanda Jepson</h4>
                                <span>Accountant</span>
                            </div>
                        </div>
                    </div><!-- End Team Member -->

                </div>

            </div>

        </section>
        <!-- /Team Section -->

    </main>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.category-filter');
            const eventList = document.getElementById('event-list');
            const paginationContainer = document.getElementById('pagination-container');
            const eventsPerPage = 9;
            let currentPage = 1;
            let allEvents = Array.from(eventList.querySelectorAll('.col-md-6.col-lg-4'));

            function displayEvents(page) {
                const start = (page - 1) * eventsPerPage;
                const end = start + eventsPerPage;

                allEvents.forEach((card, index) => {
                    card.style.display = (index >= start && index < end) ? 'block' : 'none';
                });
            }

            function setupPagination() {
                const totalPages = Math.ceil(allEvents.length / eventsPerPage);
                paginationContainer.innerHTML = '';
                paginationContainer.style.display = 'flex';

                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<button class="page-link">Previous</button>`;
                prevLi.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        displayEvents(currentPage);
                        setupPagination();
                    }
                });
                paginationContainer.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<button class="page-link">${i}</button>`;
                    li.addEventListener('click', () => {
                        currentPage = i;
                        displayEvents(currentPage);
                        setupPagination();
                    });
                    paginationContainer.appendChild(li);
                }

                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<button class="page-link">Next</button>`;
                nextLi.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        displayEvents(currentPage);
                        setupPagination();
                    }
                });
                paginationContainer.appendChild(nextLi);
            }

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.dataset.id;
                    let url = 'http://localhost:3000/api/events';

                    if (categoryId !== 'all') {
                        url += `?category=${categoryId}`;
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(events => {
                            eventList.innerHTML = '';

                            if (events.length === 0) {
                                eventList.innerHTML =
                                    '<p class="text-muted">Tidak ada event untuk kategori ini.</p>';
                                paginationContainer.style.display = 'none';
                                return;
                            }

                            allEvents = events.map(event => {
                                let categoryBadges = '';
                                if (event.categories && event.categories.length > 0) {
                                    event.categories.forEach(cat => {
                                        categoryBadges +=
                                            `<span class="badge bg-primary me-1">${cat.name}</span>`;
                                    });
                                } else {
                                    categoryBadges =
                                        '<span class="badge bg-secondary">Tidak ada kategori</span>';
                                }

                                const div = document.createElement('div');
                                div.classList.add('col-md-6', 'col-lg-4');
                                div.style.display = 'none';
                                div.setAttribute('data-aos', 'fade-up');
                                div.setAttribute('data-aos-delay', '100');

                                div.innerHTML = `
                                                                            <div class="card shadow-sm h-100 border-0 position-relative overflow-hidden">
                                                                                <div class="position-relative" >
                                                                                    <img src="/assets/img/services-1.jpg" class="card-img-top" alt="Event Image">
                                                                                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-wrap gap-1">
                                                                                        ${categoryBadges}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <h5 class="card-title mb-2">${event.name}</h5>
                                                                                    <p class="mb-1"><i class="bi bi-calendar-event"></i> ${event.date_start} - ${event.date_end}</p>
                                                                                    <p class="mb-1"><i class="bi bi-clock"></i> ${event.time}</p>
                                                                                    <p class="mb-1"><i class="bi bi-geo-alt"></i> ${event.location || 'Lokasi belum ditentukan'}</p>
                                                                                    <p class="mb-1"><i class="bi bi-cash-stack"></i> Biaya: ${event.registration_fee}</p>
                                                                                    <p class="mb-3"><i class="bi bi-people"></i> Peserta: ${event.max_participants}</p>
                                                                                </div>
                                                                                <div class="card-footer bg-transparent border-top-0 text-end">
                                                                                    <a href="/event/${event.idevents}" class="btn btn-sm btn-outline-primary me-2">Lihat Detail</a>
                                                                                    <a href="/event/${event.idevents}/daftar" class="btn btn-sm btn-outline-primary daftar-btn">Daftar</a>
                                                                                </div>
                                                                            </div>
                                                                        `;
                                eventList.appendChild(div);
                                return div;
                            });

                            const token = localStorage.getItem("token");
                            if (!token) {
                                document.querySelectorAll(".daftar-btn").forEach(btn => {
                                    btn.addEventListener("click", function(e) {
                                        e.preventDefault();

                                        Swal.fire({
                                            icon: 'warning',
                                            title: '',
                                            text: 'Silakan masuk atau daftar terlebih dahulu untuk mendaftar event.',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'OK'
                                        });
                                    });

                                    btn.classList.remove("btn-outline-primary");
                                    btn.classList.add("btn-outline-primary");
                                    btn.innerText = "Daftar";
                                });
                            }
                            currentPage = 1;
                            displayEvents(currentPage);
                            setupPagination();
                        })
                        .catch(error => {
                            console.error('Gagal fetch event:', error);
                        });
                });
            });

            displayEvents(currentPage);
            setupPagination();
        });

        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem("token");

            if (!token) {
                document.querySelectorAll(".daftar-btn").forEach(btn => {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();

                        Swal.fire({
                            icon: 'warning',
                            title: '',
                            text: 'Silakan masuk atau daftar terlebih dahulu untuk mendaftar event.',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    });

                    btn.classList.remove("btn-outline-primary");
                    btn.classList.add("btn-outline-primary");
                    btn.innerText = "Daftar";
                });
            }
        });
    </script>

@endsection
