@extends('layout.index')

@section('title', 'Riwayat Pembayaran')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <main class="main py-5">
        <div class="container">
            <h2 class="mb-4 fw-bold">Riwayat Pembayaran</h2>

            <div class="table-responsive">
                <table id="eventTable" class="display table table-bordered table-striped" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Event</th>
							<th>Nama User</th>
                            <th>Status</th>
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

            try {
                const res = await fetch(`http://localhost:3000/api/events/keuangan/riwayat-pembayaran`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Gagal ambil data');
                }

                if (data.length === 0) {
                    tableBody.html('<tr><td colspan="7" class="text-muted text-center">Belum ada event yang didaftarkan.</td></tr>');
                    return;
                }

                const rows = data.map((registrasi, index) => {
                    const detail = registrasi.registrasiDetail?.[0];
                    const ed = detail?.eventDetail || {};
                    const event = ed.event || {};
                    const status = registrasi.status?.toLowerCase() || '-';

                    let aksiButton = `<a href="/riwayat-pembayaran-detail/${event.idevents}/${registrasi.users_idusers}" class="btn btn-sm btn-primary me-1">Detail</a>`;

                    return `
						<tr>
							<td>${index + 1}</td>
							<td>${event.name || '-'}</td>
							<td>${registrasi.user.name || '-'}</td>
							<td><strong>${status}</strong></td>
							<td class="text-center">${aksiButton}</td>
						</tr>
					`;
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
    </script>
@endsection