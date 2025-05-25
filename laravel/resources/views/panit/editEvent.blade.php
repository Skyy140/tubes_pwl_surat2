@extends('layout.indexPanit')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Edit Event</h1>
        <p class="mb-4">Silakan edit data event berikut.</p>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="formEditEvent" method="POST" enctype="multipart/form-data">
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
                                accept="image/*">
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
                        <input type="number" class="form-control" id="coordinator" name="coordinator" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Event</label>
                        <div id="categoryContainer"></div>
                    </div>
                    <div class="form-group">
                        <label>Detail Event (Sesi)</label>
                        <div id="eventDetailContainer"></div>
                        <button type="button" class="btn btn-info mt-2" id="btnAddEventDetail">Tambah Sesi</button>
                    </div>
                    <div id="formError" class="text-danger mb-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="/panit/event" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let eventId = window.location.pathname.split('/').pop();
        // Fetch event data
        async function fetchEventData() {
            const res = await fetch(`http://localhost:3000/api/events/${eventId}`);
            const event = await res.json();
            document.getElementById('name').value = event.name || '';
            document.getElementById('date_start').value = event.date_start || '';
            document.getElementById('date_end').value = event.date_end || '';
            document.getElementById('time').value = event.time || '';
            document.getElementById('location').value = event.location || '';
            document.getElementById('registration_fee').value = event.registration_fee || '';
            document.getElementById('max_participants').value = event.max_participants || '';
            document.getElementById('description').value = event.description || '';
            document.getElementById('coordinator').value = event.coordinator || '';
            if (event.poster_path) {
                let posterFileName = event.poster_path.split('/').pop();
                let posterUrl = `http://localhost:3000/poster/${posterFileName}`;
                const preview = document.getElementById('previewPoster');
                preview.src = posterUrl;
                preview.style.display = 'block';
            }
            // Load categories
            await loadCategories((event.categories || []).map(cat => cat.idcategory));
            // Load event details
            await loadEventDetails(event.details || []);
        }
        // Load categories and preselect
        async function loadCategories(selectedCategories) {
            const res = await fetch('http://localhost:3000/api/events/categories/all');
            const categories = await res.json();
            const container = document.getElementById('categoryContainer');
            container.innerHTML = '';
            selectedCategories.forEach(catId => {
                const div = document.createElement('div');
                div.className = 'input-group mb-2 category-group';
                div.innerHTML = `<select class="form-control category-select" name="categories[]" required></select>
                <button type="button" class="btn btn-danger btn-remove-category">-</button>`;
                container.appendChild(div);
            });
            if (selectedCategories.length === 0) {
                const div = document.createElement('div');
                div.className = 'input-group mb-2 category-group';
                div.innerHTML = `<select class="form-control category-select" name="categories[]" required></select>
                <button type="button" class="btn btn-success btn-add-category">+</button>`;
                container.appendChild(div);
            }
            document.querySelectorAll('.category-select').forEach((select, idx) => {
                select.innerHTML = '<option value="">Pilih Kategori</option>';
                categories.forEach(cat => {
                    select.innerHTML +=
                        `<option value="${cat.idcategory}" ${selectedCategories[idx] == cat.idcategory ? 'selected' : ''}>${cat.name}</option>`;
                });
            });
        }
        // Add/remove category
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-add-category')) {
                const container = document.getElementById('categoryContainer');
                const div = document.createElement('div');
                div.className = 'input-group mb-2 category-group';
                div.innerHTML = `<select class="form-control category-select" name="categories[]" required></select>
                <button type="button" class="btn btn-danger btn-remove-category">-</button>`;
                container.appendChild(div);
                loadCategories([]);
            }
            if (e.target.classList.contains('btn-remove-category')) {
                e.target.parentElement.remove();
            }
        });
        // Load event details (sesi)
        async function loadEventDetails(details) {
            const container = document.getElementById('eventDetailContainer');
            container.innerHTML = '';
            details.forEach((detail, idx) => {
                addEventDetailForm(detail, idx);
            });
        }
        // Add event detail form
        function addEventDetailForm(detail = {}, idx = null) {
            const container = document.getElementById('eventDetailContainer');
            if (idx === null) idx = container.children.length;
            const div = document.createElement('div');
            div.className = 'card p-3 mb-2 event-detail-group';
            div.innerHTML = `
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tanggal</label>
                    <input type="date" class="form-control" name="details[${idx}][date]" value="${detail.date || ''}" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Sesi</label>
                    <input type="text" class="form-control" name="details[${idx}][sesi]" value="${detail.sesi || ''}" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Waktu Mulai</label>
                    <input type="time" class="form-control" name="details[${idx}][time_start]" value="${detail.time_start || ''}" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Waktu Selesai</label>
                    <input type="time" class="form-control" name="details[${idx}][time_end]" value="${detail.time_end || ''}" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Deskripsi</label>
                    <input type="text" class="form-control" name="details[${idx}][description]" value="${detail.description || ''}" required>
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
            // Load speakers if any
            if (detail.speakers && detail.speakers.length > 0) {
                detail.speakers.forEach((spk, j) => {
                    addSpeakerForm(div.querySelector('.speakerContainer'), spk, idx, j);
                });
            }
        }
        // Add/remove event detail
        document.getElementById('btnAddEventDetail').addEventListener('click', function() {
            addEventDetailForm();
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-detail')) {
                e.target.parentElement.remove();
            }
        });
        // Speaker per sesi
        let allSpeakers = [];
        async function loadAllSpeakers() {
            const res = await fetch('http://localhost:3000/api/events/admin/all-speakers');
            allSpeakers = await res.json();
        }
        // Add speaker form
        function addSpeakerForm(container, spk = {}, detailIdx, idx) {
            if (!container) return;
            if (allSpeakers.length === 0) return;
            if (idx === undefined) idx = container.children.length;
            const div = document.createElement('div');
            div.className = 'card p-3 mb-2 speaker-group';
            div.innerHTML = `
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Nama Speaker</label>
                    <select class="form-control speaker-select" name="details[${detailIdx}][speakers][${idx}][idspeaker]">
                        <option value="">-- Pilih Speaker --</option>
                        ${allSpeakers.map(s => `<option value="${s.idspeaker}" ${spk.idspeaker == s.idspeaker ? 'selected' : ''}>${s.name}</option>`).join('')}
                        <option value="__new__" ${(spk.idspeaker === undefined && spk.name) ? 'selected' : ''}>Lainnya (Input Baru)</option>
                    </select>
                    <input type="text" class="form-control mt-2 speaker-name-input" name="details[${detailIdx}][speakers][${idx}][name]" placeholder="Nama Speaker Baru" value="${spk.name || ''}" style="${(spk.idspeaker === undefined && spk.name) ? '' : 'display:none;'}" />
                </div>
                <div class="form-group col-md-4">
                    <label>Deskripsi</label>
                    <input type="text" class="form-control speaker-desc-input" name="details[${detailIdx}][speakers][${idx}][description]" value="${spk.description || ''}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Photo Path</label>
                    <input type="text" class="form-control speaker-photo-input" name="details[${detailIdx}][speakers][${idx}][photo_path]" value="${spk.photo_path || ''}" required>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-remove-speaker mt-2">Hapus Speaker</button>
        `;
            container.appendChild(div);
        }
        // Add speaker button
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('btnAddSpeaker')) {
                if (allSpeakers.length === 0) await loadAllSpeakers();
                const eventDetailGroup = e.target.closest('.event-detail-group');
                const speakerContainer = eventDetailGroup.querySelector('.speakerContainer');
                const detailIdx = Array.from(document.getElementById('eventDetailContainer').children).indexOf(
                    eventDetailGroup);
                addSpeakerForm(speakerContainer, {}, detailIdx);
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
        // Submit form
        document.getElementById('formEditEvent').addEventListener('submit', async function(e) {
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
            if (form.poster_path.files[0]) {
                formData.append('poster', form.poster_path.files[0]);
            }
            Array.from(document.querySelectorAll('.category-select')).map(s => s.value).filter(Boolean).forEach(
                cat => {
                    formData.append('categories[]', cat);
                });
            const details = [];
            document.querySelectorAll('.event-detail-group').forEach((group, i) => {
                const inputs = group.querySelectorAll('input');
                const selects = group.querySelectorAll('select');
                const detail = {};
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
                const response = await fetch(`http://localhost:3000/api/events/admin/edit-event/${eventId}`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (response.ok) {
                    window.location.href = '/panit/event?success=Event berhasil diupdate';
                } else {
                    document.getElementById('formError').innerText = result.message || 'Terjadi kesalahan';
                    document.getElementById('formError').style.display = 'block';
                }
            } catch (err) {
                document.getElementById('formError').innerText = 'Gagal terhubung ke server';
                document.getElementById('formError').style.display = 'block';
            }
        });
        // Initial load
        (async function() {
            await loadAllSpeakers();
            await fetchEventData();
        })();
    </script>
@endsection
