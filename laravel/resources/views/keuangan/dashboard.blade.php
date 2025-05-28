@extends('layout.index')

@section('title', 'Dashboard Keuangan')

@section('content')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

	<main class="main py-5">
		<div class="container">
			<h2 class="mb-4 fw-bold">Verifikasi Pembayaran</h2>

			<div class="table-responsive">
				<table id="registrasiTable" class="display table table-bordered table-striped" style="width:100%">
					<thead class="table-dark">
						<tr>
							<th>No</th>
							<th>Nama Member</th>
							<th>Email</th>
							<th>Nama Event</th>
							<th>Status Registrasi</th>
							<th>Bukti Pembayaran</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody id="registrasiTableBody">
						<tr>
							<td colspan="9">Memuat data...</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="modal" id="rejectModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Alasan Penolakan</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<textarea id="rejectNote" class="form-control"
							placeholder="Masukkan alasan penolakan..."></textarea>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
						<button type="button" class="btn btn-danger" id="confirmRejectBtn">Tolak</button>
					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function () {
			loadRegistrasi();

			async function loadRegistrasi() {
				try {
					const res = await fetch('http://localhost:3000/api/events/keuangan/registrasi');
					const data = await res.json();

					if (!res.ok) throw new Error(data.message || 'Gagal mengambil data');

					const tableBody = $('#registrasiTableBody');
					tableBody.empty();

					if (data.length === 0) {
						tableBody.html('<tr><td colspan="9" class="text-center">Tidak ada data registrasi.</td></tr>');
						return;
					}

					data.forEach((event, index) => {
						const paymentData = Array.isArray(event.payment) ? event.payment[0] : event.payment;
						const eventDet = Array.isArray(event.details) ? event.details[0] : event.details;
						const buktiUrl = `http://localhost:3000${paymentData.payment_proof_path}`;
						const buktiDownloadUrl = `http://localhost:3000/download/${paymentData.payment_proof_path.split('/').pop()}`;
						console.log("detttt" + event.details);


						const row = `
				  <tr>
					<td>${index + 1}</td>
					<td>${event.user.name}</td>
					<td>${event.user.email}</td>
					<td>${event.events.name}</td>
					<td>${event.status}</td>
					<td>
					<a href="${buktiUrl}" target="_blank">Lihat</a> |
					<a href="${buktiDownloadUrl}">Download</a>
					</td>
					<td>
					<a href="/keuangan/event/event-detail/${event.events.idevents}" class="btn btn-sm btn-primary">Detail</a>
					<button class="btn btn-sm btn-success approve-btn" data-id="${event.idregistrations}">Approve</button>
					<button class="btn btn-sm btn-danger reject-btn" data-id="${event.idregistrations}">Reject</button>
					</td>
				  </tr>
				  `;
						tableBody.append(row);
					});

					$('#registrasiTable').DataTable();
				} catch (err) {
					console.error(err);
					$('#registrasiTableBody').html(`<tr><td colspan="9" class="text-center text-danger">${err.message}</td></tr>`);
				}
			}

			$('#registrasiTableBody').on('click', '.approve-btn', function () {
				const id = $(this).data('id');

				Swal.fire({
					title: 'Yakin menyetujui pembayaran ini?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Ya, Setujui!',
					cancelButtonText: 'Batal'
				}).then((result) => {
					if (result.isConfirmed) {
						fetch(`http://localhost:3000/api/events/registrasi/${id}/approve`, {
							method: 'POST',
							headers: { 'Content-Type': 'application/json' },
							body: JSON.stringify({ note: '' })
						})
							.then(res => res.json())
							.then(data => {
								Swal.fire('Berhasil!', data.message, 'success');
								loadRegistrasi(); 
							})
							.catch(err => {
								console.error(err);
								Swal.fire('Gagal', 'Terjadi kesalahan saat menyetujui pembayaran.', 'error');
							});
					}
				});
			});


			let id = null;

			$('#registrasiTableBody').on('click', '.reject-btn', function () {
				id = $(this).data('id');
				$('#rejectNote').val('');
				$('#rejectModal').modal('show');
			});

			$('#confirmRejectBtn').on('click', function () {
				const note = $('#rejectNote').val().trim();
				if (!note) {
					Swal.fire({
						icon: 'warning',
						title: 'Peringatan',
						text: 'Alasan penolakan harus diisi!',
					});
					return;
				}

				fetch(`http://localhost:3000/api/events/registrasi/${id}/reject`, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ note })
				})
					.then(res => res.json())
					.then(data => {
						Swal.fire({
							icon: 'success',
							title: 'Berhasil',
							text: data.message,
						});
						$('#rejectModal').modal('hide');
						loadRegistrasi();
					})
					.catch(err => {
						console.error(err);
						Swal.fire({
							icon: 'error',
							title: 'Gagal',
							text: 'Terjadi kesalahan saat menolak pembayaran.',
						});
					});
			});

		});
	</script>
@endsection