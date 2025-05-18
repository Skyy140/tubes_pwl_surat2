@extends('layout.indexAdmin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Tambah User Panitia</h1>
        <p class="mb-4">Silakan isi form berikut untuk menambah user dengan role Panitia.</p>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="formTambahPanitia" method="POST" action="http://localhost:3000/api/users/panitia">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            required>
                    </div>
                    <input type="hidden" name="role" value="4">
                    <input type="hidden" name="status" value="aktif">
                    <div id="passwordError" class="text-danger mb-3" style="display:none;">Konfirmasi password tidak sama!
                    </div>
                    <div id="emailError" class="text-danger mb-3" style="display:none;">Email sudah terdaftar!
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <a href="/admin/panitia" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('formTambahPanitia').addEventListener('submit', async function(e) {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            if (password !== confirm) {
                document.getElementById('passwordError').style.display = 'block';
                document.getElementById('emailError').style.display = 'none';
                return;
            } else {
                document.getElementById('passwordError').style.display = 'none';
                document.getElementById('emailError').style.display = 'none';
            }

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;

            try {
                const response = await fetch('http://localhost:3000/api/users/panitia', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        status: 'aktif'
                    })
                });
                const result = await response.json();
                if (response.ok) {
                    window.location.href = '/admin/panitia?success=User berhasil ditambahkan';
                } else if (result.message && (result.message.toLowerCase().includes('email') && (result.message
                        .toLowerCase().includes('sudah') || result.message.toLowerCase().includes('already')
                    ))) {
                    document.getElementById('emailError').style.display = 'block';
                } else {
                    window.location.href = '/admin/panitia?error=' + encodeURIComponent(result.message ||
                        'Terjadi kesalahan');
                }
            } catch (err) {
                window.location.href = '/admin/panitia?error=Gagal terhubung ke server';
            }
        });
    </script>
@endsection
