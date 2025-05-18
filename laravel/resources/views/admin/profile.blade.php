@extends('layout.indexAdmin')

@section('content')
    <div class="container mt-5">
        <h2>Edit Profil</h2>
        <form id="profileForm">
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name ?? '' }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email ?? '' }}"
                    readonly>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="{{ $user->status ?? '' }}"
                    readonly>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <div id="profileMessage" class="mt-3"></div>
    </div>
    <script>
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const password = document.getElementById('password').value;
            const res = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name,
                    password
                }),
            });
            const data = await res.json();
            const msg = document.getElementById('profileMessage');
            if (res.ok) {
                msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            } else {
                msg.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Gagal update profil') +
                    '</div>';
            }
        });
    </script>
@endsection
