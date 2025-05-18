@extends('layout.indexAdmin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Edit User Panitia</h1>
        <p class="mb-4">Silakan edit data user panitia berikut.</p>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="formEditPanitia" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    <input type="hidden" name="role" value="2">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="aktif" {{ $user->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ $user->status == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif
                            </option>
                        </select>
                    </div>
                    <div id="passwordError" class="text-danger mb-3" style="display:none;">Konfirmasi password tidak sama!
                    </div>
                    <div id="emailError" class="text-danger mb-3" style="display:none;">Email sudah terdaftar!
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="/admin/panitia" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('formEditPanitia').addEventListener('submit', async function(e) {
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
            const status = document.getElementById('status').value;
            const id = {{ $user->idusers }};
            let body = {
                name,
                email,
                status
            };
            if (password) body.password = password;
            try {
                const response = await fetch(`http://localhost:3000/api/users/panitia/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                const result = await response.json();
                if (response.ok) {
                    window.location.href = '/admin/panitia?success=User berhasil diupdate';
                } else if (
                    result.message &&
                    result.message.toLowerCase().includes('email') &&
                    (
                        result.message.toLowerCase().includes('sudah') ||
                        result.message.toLowerCase().includes('already') ||
                        result.message.toLowerCase().includes('register')
                    )
                ) {
                    document.getElementById('emailError').style.display = 'block';
                    document.getElementById('emailError').textContent = 'Email sudah terdaftar!';
                } else {
                    document.getElementById('emailError').style.display = 'block';
                    document.getElementById('emailError').textContent = result.message || 'Terjadi kesalahan';
                }
            } catch (err) {
                window.location.href = '/admin/panitia?error=Gagal terhubung ke server';
            }
        });
    </script>
@endsection
