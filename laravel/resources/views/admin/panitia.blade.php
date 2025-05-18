@extends('layout.indexAdmin')

@section('content')
    @if (request()->has('success'))
        <div class="alert alert-success">{{ request('success') }}</div>
    @endif
    @if (request()->has('error'))
        <div class="alert alert-danger">{{ request('error') }}</div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Tabel Tim Panitia</h1>
        <p class="mb-4">Berikut adalah daftar user dengan role Panitia.</p>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data User Panitia</h6>
                <a href="/admin/tambah-tim-panitia" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Tanggal Dibuat</th>
                                <th>Terakhir Update</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="panitia-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('http://localhost:3000/api/users/panitia')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('panitia-table-body');
                    tbody.innerHTML = '';
                    data.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${user.idusers}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.created_at ? user.created_at : '-'}</td>
                        <td>${user.updated_at ? user.updated_at : '-'}</td>
                        <td>${user.status ? user.status : '-'}</td>
                        <td class="text-center">
                            <a href="/admin/edit-tim-panitia/${user.idusers}" title="Edit" class="mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" title="Delete" class="delete-user-btn" data-user-id="${user.idusers}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    document.getElementById('panitia-table-body').innerHTML =
                        `<tr><td colspan="7">Gagal memuat data</td></tr>`;
                });
        });
        // Modal HTML
        const modalHtml = `
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Apakah anda ingin menonaktifkan user ini?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirm-nonaktif-btn">Ya</button>
              </div>
            </div>
          </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        let selectedUserId = null;
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-user-btn')) {
                e.preventDefault();
                selectedUserId = e.target.closest('.delete-user-btn').getAttribute('data-user-id');
                $('#confirmDeleteModal').modal('show');
            }
        });

        document.getElementById('confirm-nonaktif-btn').addEventListener('click', function() {
            if (!selectedUserId) return;
            fetch(`http://localhost:3000/api/users/panitia/nonaktifkan/${selectedUserId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(async res => {
                    const result = await res.json();
                    $('#confirmDeleteModal').modal('hide');
                    let alertDiv = document.querySelector('.alert-success, .alert-danger');
                    if (alertDiv) alertDiv.remove();
                    if (res.ok) {
                        const notif = document.createElement('div');
                        notif.className = 'alert alert-success';
                        notif.innerText = 'Berhasil nonaktifkan user';
                        document.querySelector('.container-fluid').prepend(notif);
                    } else {
                        const notif = document.createElement('div');
                        notif.className = 'alert alert-danger';
                        notif.innerText = result.message || result.error || 'Gagal menonaktifkan user';
                        document.querySelector('.container-fluid').prepend(notif);
                    }
                    setTimeout(() => {
                        let alertDiv = document.querySelector('.alert-success, .alert-danger');
                        if (alertDiv) alertDiv.remove();
                    }, 3000);
                    return fetch('http://localhost:3000/api/users/panitia');
                })
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('panitia-table-body');
                    tbody.innerHTML = '';
                    data.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${user.idusers}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.created_at ? user.created_at : '-'}</td>
                        <td>${user.updated_at ? user.updated_at : '-'}</td>
                        <td>${user.status ? user.status : '-'}</td>
                        <td class="text-center">
                            <a href="/admin/edit-tim-panitia/${user.idusers}" title="Edit" class="mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" title="Delete" class="delete-user-btn" data-user-id="${user.idusers}">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    let alertDiv = document.querySelector('.alert-success, .alert-danger');
                    if (alertDiv) alertDiv.remove();
                    const notif = document.createElement('div');
                    notif.className = 'alert alert-danger';
                    notif.innerText = 'Gagal menonaktifkan user';
                    document.querySelector('.container-fluid').prepend(notif);
                    setTimeout(() => {
                        let alertDiv = document.querySelector('.alert-success, .alert-danger');
                        if (alertDiv) alertDiv.remove();
                    }, 3000);
                });
        });
    </script>
@endsection
