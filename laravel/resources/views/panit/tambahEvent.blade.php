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
                        <label for="poster_path">Poster (Gambar)</label>
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="poster_path" name="poster_path"
                                accept="image/*" required>
                            <label class="custom-file-label" for="poster_path">Pilih file gambar...</label>
                        </div>
                        <img id="previewPoster" src="#" alt="Preview Poster"
                            style="max-width:150px;max-height:150px;display:none;margin-top:10px;" />
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
                        <input type="number" class="form-control" id="coordinator" name="coordinator" readonly required>
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
                    <div id="formError" class="text-danger mb-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary">Tambah Event</button>
                    <a href="/panit/event" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
    <script>
        // Set coordinator field to logged-in user id (from JWT)
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const decoded = window.jwt_decode(token);
                    if (decoded && decoded.id) {
                        document.getElementById('coordinator').value = decoded.id;
                    }
                } catch (e) {
                    // Token invalid, do nothing
                }
            }
        });

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
                <div class="form-group mt-2">
                    <label>Speaker untuk Sesi Ini</label>
                    <div class="speakerContainer"></div>
                    <button type="button" class="btn btn-warning mt-2 btnAddSpeaker">Tambah Speaker</button>
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

        // Speaker per sesi
        // Dropdown speaker
        let allSpeakers = [];
        async function loadAllSpeakers() {
            const res = await fetch('http://localhost:3000/api/events/admin/all-speakers');
            allSpeakers = await res.json();
        }

        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('btnAddSpeaker')) {
                if (allSpeakers.length === 0) await loadAllSpeakers();
                const eventDetailGroup = e.target.closest('.event-detail-group');
                const speakerContainer = eventDetailGroup.querySelector('.speakerContainer');
                const idx = speakerContainer.children.length;
                const detailIdx = Array.from(document.getElementById('eventDetailContainer').children).indexOf(
                    eventDetailGroup);
                const div = document.createElement('div');
                div.className = 'card p-3 mb-2 speaker-group';
                // Dropdown + input deskripsi dan photo_path
                div.innerHTML = `
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nama Speaker</label>
                            <select class="form-control speaker-select" name="details[${detailIdx}][speakers][${idx}][idspeaker]">
                                <option value="">-- Pilih Speaker --</option>
                                ${allSpeakers.map(spk => `<option value="${spk.idspeaker}">${spk.name}</option>`).join('')}
                                <option value="__new__">Lainnya (Input Baru)</option>
                            </select>
                            <input type="text" class="form-control mt-2 speaker-name-input" name="details[${detailIdx}][speakers][${idx}][name]" placeholder="Nama Speaker Baru" style="display:none;" />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Deskripsi</label>
                            <input type="text" class="form-control speaker-desc-input" name="details[${detailIdx}][speakers][${idx}][description]" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Photo Path</label>
                            <input type="text" class="form-control speaker-photo-input" name="details[${detailIdx}][speakers][${idx}][photo_path]" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-remove-speaker mt-2">Hapus Speaker</button>
                `;
                speakerContainer.appendChild(div);
            }
            if (e.target.classList.contains('btn-remove-speaker')) {
                e.target.parentElement.remove();
            }
        });

        // Event delegation untuk dropdown speaker
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('speaker-select')) {
                const select = e.target;
                const group = select.closest('.speaker-group');
                const descInput = group.querySelector('.speaker-desc-input');
                const photoInput = group.querySelector('.speaker-photo-input');
                const nameInput = group.querySelector('.speaker-name-input');
                const selectedId = select.value;
                if (selectedId === "__new__") {
                    nameInput.style.display = '';
                    nameInput.required = true;
                    descInput.value = '';
                    photoInput.value = '';
                    descInput.readOnly = false;
                    photoInput.readOnly = false;
                } else if (selectedId) {
                    const spk = allSpeakers.find(s => s.idspeaker == selectedId);
                    if (spk) {
                        nameInput.style.display = 'none';
                        nameInput.required = false;
                        descInput.value = spk.description || '';
                        photoInput.value = spk.photo_path || '';
                        descInput.readOnly = true;
                        photoInput.readOnly = true;
                    }
                } else {
                    nameInput.style.display = 'none';
                    nameInput.required = false;
                    descInput.value = '';
                    photoInput.value = '';
                    descInput.readOnly = false;
                    photoInput.readOnly = false;
                }
            }
        });

        // Preview poster & update label
        document.getElementById('poster_path').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('previewPoster');
            const label = document.querySelector('label.custom-file-label[for="poster_path"]');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                if (label) label.textContent = file.name;
            } else {
                preview.src = '#';
                preview.style.display = 'none';
                if (label) label.textContent = 'Pilih file gambar...';
            }
        });

        // Submit form dengan FormData (upload file)
        document.getElementById('formTambahEvent').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData();
            formData.append('name', form.name.value);
            formData.append('date_start', form.date_start.value);
            formData.append('date_end', form.date_end.value);
            formData.append('time', form.time.value);
            formData.append('location', form.location.value);
            formData.append('registration_fee', form.registration_fee.value);
            formData.append('max_participants', form.max_participants.value);
            formData.append('description', form.description.value);
            formData.append('coordinator', form.coordinator.value);
            // Poster file
            if (form.poster_path.files[0]) {
                formData.append('poster', form.poster_path.files[0]);
            }
            // Kategori
            Array.from(document.querySelectorAll('.category-select')).map(s => s.value).filter(Boolean).forEach(
                cat => {
                    formData.append('categories[]', cat);
                });
            // Details (sesi + speakers)
            const details = [];
            document.querySelectorAll('.event-detail-group').forEach((group, i) => {
                const inputs = group.querySelectorAll('input');
                const selects = group.querySelectorAll('select');
                const detail = {};
                // Ambil field detail
                inputs.forEach(input => {
                    if (input.name.includes('[speakers]')) return;
                    const matches = input.name.match(/\[(\w+)\]$/);
                    if (matches) {
                        const key = matches[1];
                        detail[key] = input.value;
                    }
                });
                detail.speakers = [];
                group.querySelectorAll('.speaker-group').forEach((spkGroup, j) => {
                    const spkInputs = spkGroup.querySelectorAll('input');
                    const spkSelect = spkGroup.querySelector('select.speaker-select');
                    const speaker = {};
                    if (spkSelect && spkSelect.value && spkSelect.value !== "__new__") {
                        speaker.idspeaker = spkSelect.value;
                        const descInput = spkGroup.querySelector('.speaker-desc-input');
                        const photoInput = spkGroup.querySelector('.speaker-photo-input');
                        if (descInput) speaker.description = descInput.value;
                        if (photoInput) speaker.photo_path = photoInput.value;
                    } else {
                        spkInputs.forEach(input => {
                            const matches = input.name.match(/\[(\w+)\]$/);
                            if (matches) {
                                const key = matches[1];
                                speaker[key] = input.value;
                            }
                        });
                    }
                    detail.speakers.push(speaker);
                });
                details.push(detail);
            });
            formData.append('details', JSON.stringify(details));
            try {
                const token = localStorage.getItem('token');
                const response = await fetch('http://localhost:3000/api/events/admin/tambah-event', {
                    method: 'POST',
                    body: formData,
                    headers: token ? {
                        'Authorization': `Bearer ${token}`
                    } : undefined
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
