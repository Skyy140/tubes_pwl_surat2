@extends('layout.indexPanit')

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
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
    <script>
        async function loadProfile() {
            const token = localStorage.getItem('token');
            if (!token) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Silakan login terlebih dahulu.</div>';
                return;
            }
            let decoded;
            try {
                decoded = window.jwt_decode(token);
            } catch (e) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Token tidak valid. Silakan login ulang.</div>';
                return;
            }
            const userId = decoded.id;
            try {
                const res = await fetch(`http://localhost:3000/api/users/profile/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (!res.ok) throw new Error('Gagal mengambil data profil');
                const user = await res.json();
                document.getElementById('name').value = user.name || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('status').value = user.status || '';
            } catch (err) {
                document.getElementById('profileMessage').innerHTML = '<div class="alert alert-danger">' + err.message +
                    '</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadProfile);

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            if (!token) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Silakan login terlebih dahulu.</div>';
                return;
            }
            let decoded;
            try {
                decoded = window.jwt_decode(token);
            } catch (e) {
                document.getElementById('profileMessage').innerHTML =
                    '<div class="alert alert-danger">Token tidak valid. Silakan login ulang.</div>';
                return;
            }
            const userId = decoded.id;
            const name = document.getElementById('name').value;
            const password = document.getElementById('password').value;
            const res = await fetch(`http://localhost:3000/api/users/profile/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    name,
                    password
                }),
            });
            const data = await res.json();
            const msg = document.getElementById('profileMessage');
            if (res.ok) {
                // Tampilkan pesan sukses, lalu redirect ke dashboard setelah 1.5 detik
                msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                document.getElementById('password').value = '';
                setTimeout(function() {
                    window.location.href = '/panit/dashboard';
                }, 1500);
            } else {
                msg.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Gagal update profil') +
                    '</div>';
            }
        });
    </script>
@endsection
