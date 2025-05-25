@extends('layout.index')

@section('title', 'Event Saya')

@section('content')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

	<main class="main py-5">
		<div class="container">
			<h2 class="mb-4 fw-bold">Event yang Saya Daftar</h2>

			<div class="table-responsive">
				<table id="eventTable" class="display table table-bordered table-striped" style="width:100%">
					<thead class="table-dark">
						<tr>
							<th>No</th> 
							<th>Nama Event</th>
							<th>Sesi</th>
							<th>Tanggal</th>
							<th>Waktu</th>
							<th>Status</th>
							<th>Bukti Pembayaran</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody id="eventTableBody">
						<tr>
							<td colspan="7">Tidak ada event untuk ditampilkan</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Modal Upload Bukti -->
		<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<form id="uploadForm" enctype="multipart/form-data">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Upload Bukti Pembayaran</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="registrasiIdInput" name="registrasiId">
							<div class="mb-3">
								<label for="buktiPembayaran" class="form-label">Pilih File</label>
								<input type="file" class="form-control" id="buktiPembayaran" name="bukti"
									accept="image/*,application/pdf" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">Upload</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
						</div>
					</div>
				</form>
			</div>
		</div>

	</main>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
	<script>
		async function loadEventsSaya() {
			const token = localStorage.getItem('token');
			const tableBody = $('#eventTableBody');

			if (!token) {
				tableBody.html('<tr><td colspan="7"><div class="alert alert-warning mb-0">Silakan login terlebih dahulu.</div></td></tr>');
				return;
			}

			const decoded = jwt_decode(token);
			const userId = decoded.id;

			function formatTanggal(dateString) {
				if (!dateString) return '-';
				const date = new Date(dateString);
				const day = String(date.getDate()).padStart(2, '0');
				const month = String(date.getMonth() + 1).padStart(2, '0');
				const year = date.getFullYear();
				return `${day}-${month}-${year}`;
			}

			try {
				const res = await fetch(`http://localhost:3000/api/events/registrasi/user/${userId}`, {
					headers: { 'Authorization': `Bearer ${token}` }
				});

				const data = await res.json();
				console.log('Data dari API:', data);

				if (!res.ok) {
					throw new Error(data.message || 'Gagal ambil data');
				}

				if (data.length === 0) {
					tableBody.html('<tr><td colspan="7" class="text-muted text-center">Belum ada event yang didaftarkan.</td></tr>');
					return;
				}

				const eventsWithButton = new Set();
				const eventsWithStatus = new Set();
				const eventsWithName = new Set();

				let no = 1
				const rows = data.map(registrasi => {
					if (!registrasi.registrasiDetail || registrasi.registrasiDetail.length === 0) {
						return `
							<tr>
								<td>-</td>
								<td colspan="4" class="text-center">Belum ada sesi</td>
								<td style="text-align: center;">
									<button class="btn btn-sm btn-primary upload-btn" data-id="${registrasi.idregistrations}">Upload Bukti</button>
								</td>
								<td style="text-align: center;">
									<button class="btn btn-sm btn-success update-btn" data-id="${registrasi.idregistrations}">Update</button>
									<button class="btn btn-sm btn-danger delete-btn" data-id="${registrasi.idregistrations}">Delete</button>
								</td>
							</tr>
						`;
					}

					return registrasi.registrasiDetail.map((detail, index) => {
						const ed = detail.eventDetail || {};
						const event = ed.event || {};
						const eventId = event.idevents;
						const eventName = event.name || '-';

						let showUploadBtn = false;
						if (!eventsWithButton.has(eventId)) {
							showUploadBtn = true;
							eventsWithButton.add(eventId);
						}

						let statusContent = '';
						if (!eventsWithStatus.has(eventId)) {
							statusContent = `<strong>${registrasi.status || '-'}</strong>`;
							eventsWithStatus.add(eventId);
						} else {
							statusContent = ''; 
						}

						let eventNameContent = '';
						if (!eventsWithName.has(eventId)) {
							eventNameContent = eventName;
							eventsWithName.add(eventId);
						} else {
							eventNameContent = ''; 
						}
						
						return `
							<tr>
								<td>${no++}</td>
								<td>${eventNameContent}</td>
								<td>${ed.sesi || '-'}</td>
								<td>${formatTanggal(ed.date)}</td>
								<td>${ed.time_start || '-'} - ${ed.time_end || '-'}</td>
								<td>${statusContent}</td>
								<td style="text-align: center;">
									${showUploadBtn ? `<button class="btn btn-sm btn-primary upload-btn" data-id="${registrasi.idregistrations}">Upload Bukti</button>` : ''}
								</td>
								<td style="text-align: center;">
									<button class="btn btn-sm btn-success update-btn" data-id="${registrasi.idregistrations}">Update</button>
									<button class="btn btn-sm btn-danger delete-btn" data-id="${registrasi.idregistrations}">Delete</button>
								</td>
							</tr>
						`;
					}).join('');
				}).join('');



				tableBody.html(rows);

				if (!$.fn.DataTable.isDataTable('#eventTable')) {
					$('#eventTable').DataTable({
						paging: true,
						searching: true,
						ordering: true,
						info: true,
						lengthMenu: [5, 10, 25, 50],
						pageLength: 5,
						language: {
							search: "Cari:",
							lengthMenu: "Tampilkan _MENU_ entri",
							info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
							paginate: {
								first: "Pertama",
								last: "Terakhir",
								next: "Selanjutnya",
								previous: "Sebelumnya"
							},
							zeroRecords: "Tidak ada data yang cocok",
						}
					});
				}
			} catch (err) {
				tableBody.html(`<tr><td colspan="7"><div class="alert alert-danger mb-0">${err.message}</div></td></tr>`);
			}
		}

		$(document).ready(function () {
			loadEventsSaya();
		});

		let selectedRegistrasiId = null;

		$('#eventTableBody').on('click', '.upload-btn', function () {
			const id = $(this).data('id');
			selectedRegistrasiId = id;
			$('#registrasiIdInput').val(id);
			$('#uploadModal').modal('show');
		});

		$('#uploadForm').on('submit', async function (e) {
			e.preventDefault();
			const token = localStorage.getItem('token');
			if (!token) {
				return Swal.fire({
					icon: 'warning',
					title: 'Login Diperlukan',
					text: 'Silakan login terlebih dahulu.',
				});
			}

			const formData = new FormData(this);
			const registrasiId = formData.get('registrasiId');
			console.log("ID yang dikirim:", registrasiId);

			try {
				const res = await fetch(`http://localhost:3000/api/events/upload-payment/${registrasiId}`, {
					method: 'POST',
					headers: { 'Authorization': `Bearer ${token}` },
					body: formData
				});

				const result = await res.json();

				if (!res.ok) throw new Error(result.message || 'Gagal upload bukti');

				await Swal.fire({
					icon: 'success',
					title: 'Berhasil',
					text: 'Bukti berhasil diupload!',
				});

				$('#uploadModal').modal('hide');
				loadEventsSaya();
			} catch (err) {
				Swal.fire({
					icon: 'error',
					title: 'Gagal',
					text: 'Error: ' + err.message,
				});
			}
		});

	</script>
@endsection