@extends('layout.indexPanit')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Tambah Event</h1>
        <p class="mb-4">Silakan isi form berikut untuk menambah event baru.</p>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="formTambahEvent" method="POST" action="#">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Event</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="date_start">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" required>
                    </div>
                    <div class="form-group">
                        <label for="date_end">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" required>
                    </div>
                    <div class="form-group">
                        <label for="poster_path">Poster (URL)</label>
                        <input type="text" class="form-control" id="poster_path" name="poster_path">
                    </div>
                    <div class="form-group">
                        <label for="time">Waktu (HH:MM:SS)</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Lokasi</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="registration_fee">Biaya Registrasi</label>
                        <input type="number" class="form-control" id="registration_fee" name="registration_fee" required>
                    </div>
                    <div class="form-group">
                        <label for="max_participants">Maksimal Peserta</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi Event</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="coordinator">ID Koordinator</label>
                        <input type="number" class="form-control" id="coordinator" name="coordinator" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Event</label>
                        <div id="categoryContainer">
                            <div class="input-group mb-2 category-group">
                                <select class="form-control category-select" name="categories[]" required></select>
                                <button type="button" class="btn btn-success btn-add-category">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Detail Event (Sesi)</label>
                        <div id="eventDetailContainer"></div>
                        <button type="button" class="btn btn-info mt-2" id="btnAddEventDetail">Tambah Sesi</button>
                    </div>
                    <div class="form-group">
                        <label>Speaker</label>
                        <div id="speakerContainer"></div>
                        <button type="button" class="btn btn-warning mt-2" id="btnAddSpeaker">Tambah Speaker</button>
                    </div>
                    <div id="formError" class="text-danger mb-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary">Tambah Event</button>
                    <a href="/panit/event" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fetch categories for dropdown
        async function loadCategories() {
            const res = await fetch('http://localhost:3000/api/events/categories/all');
            const categories = await res.json();
            document.querySelectorAll('.category-select').forEach(select => {
                select.innerHTML = '<option value="">Pilih Kategori</option>';
                categories.forEach(cat => {
                    select.innerHTML += `<option value="${cat.idcategory}">${cat.name}</option>`;
                });
            });
        }
        // Add new category dropdown
        function addCategoryDropdown() {
            const container = document.getElementById('categoryContainer');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 category-group';
            div.innerHTML = `<select class="form-control category-select" name="categories[]" required></select>
                <button type="button" class="btn btn-danger btn-remove-category">-</button>`;
            container.appendChild(div);
            loadCategories();
        }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-add-category')) {
                addCategoryDropdown();
            }
            if (e.target.classList.contains('btn-remove-category')) {
                e.target.parentElement.remove();
            }
        });
        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
        });

        // Event Detail (Sesi)
        function addEventDetailForm() {
            const container = document.getElementById('eventDetailContainer');
            const idx = container.children.length;
            const div = document.createElement('div');
            div.className = 'card p-3 mb-2 event-detail-group';
            div.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="details[${idx}][date]" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Sesi</label>
                        <input type="text" class="form-control" name="details[${idx}][sesi]" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Waktu Mulai</label>
                        <input type="time" class="form-control" name="details[${idx}][time_start]" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Waktu Selesai</label>
                        <input type="time" class="form-control" name="details[${idx}][time_end]" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Deskripsi</label>
                        <input type="text" class="form-control" name="details[${idx}][description]" required>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-remove-detail mt-2">Hapus Sesi</button>
            `;
            container.appendChild(div);
        }
        document.getElementById('btnAddEventDetail').addEventListener('click', addEventDetailForm);
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-detail')) {
                e.target.parentElement.remove();
            }
        });

        // Speaker
        function addSpeakerForm() {
            const container = document.getElementById('speakerContainer');
            const idx = container.children.length;
            const div = document.createElement('div');
            div.className = 'card p-3 mb-2 speaker-group';
            div.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Nama Speaker</label>
                        <input type="text" class="form-control" name="speakers[${idx}][name]" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Deskripsi</label>
                        <input type="text" class="form-control" name="speakers[${idx}][description]" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Photo Path</label>
                        <input type="text" class="form-control" name="speakers[${idx}][photo_path]" required>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-remove-speaker mt-2">Hapus Speaker</button>
            `;
            container.appendChild(div);
        }
        document.getElementById('btnAddSpeaker').addEventListener('click', addSpeakerForm);
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-speaker')) {
                e.target.parentElement.remove();
            }
        });

        // Submit form
        document.getElementById('formTambahEvent').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const data = {
                name: form.name.value,
                date_start: form.date_start.value,
                date_end: form.date_end.value,
                poster_path: form.poster_path.value,
                time: form.time.value,
                location: form.location.value,
                registration_fee: form.registration_fee.value,
                max_participants: form.max_participants.value,
                description: form.description.value,
                coordinator: form.coordinator.value,
                categories: Array.from(document.querySelectorAll('.category-select')).map(s => s.value)
                    .filter(Boolean),
                details: []
            };
            document.querySelectorAll('.event-detail-group').forEach((group, i) => {
                const inputs = group.querySelectorAll('input');
                const detail = {};
                inputs.forEach(input => {
                    const matches = input.name.match(/\[(\w+)\]$/);
                    if (matches) {
                        const key = matches[1];
                        detail[key] = input.value;
                    }
                });
                data.details.push(detail);
            });
            // Ambil data speaker
            data.speakers = [];
            document.querySelectorAll('.speaker-group').forEach((group, i) => {
                const inputs = group.querySelectorAll('input');
                const speaker = {};
                inputs.forEach(input => {
                    const matches = input.name.match(/\[(\w+)\]$/);
                    if (matches) {
                        const key = matches[1];
                        speaker[key] = input.value;
                    }
                });
                data.speakers.push(speaker);
            });
            try {
                const response = await fetch('http://localhost:3000/api/events/admin/tambah-event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (response.ok) {
                    window.location.href = '/panit/event?success=Event berhasil ditambahkan';
                } else {
                    document.getElementById('formError').innerText = result.message || 'Terjadi kesalahan';
                    document.getElementById('formError').style.display = 'block';
                }
            } catch (err) {
                document.getElementById('formError').innerText = 'Gagal terhubung ke server';
                document.getElementById('formError').style.display = 'block';
            }
        });
    </script>
@endsection
