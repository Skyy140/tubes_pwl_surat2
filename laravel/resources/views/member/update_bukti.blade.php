@extends('layout.index')

@section('title', 'Update Bukti Pembayaran')

@section('content')
<div class="container py-5">
  <h2 class="mb-4">Update Bukti Pembayaran</h2>

  <div id="alertContainer"></div>

  <form id="updatePaymentForm" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Bukti Pembayaran Saat Ini:</label>
      <div id="currentProofContainer">
        <!-- Bukti pembayaran akan ditampilkan di sini -->
      </div>
    </div>

    <div class="mb-3">
      <label for="buktiPembayaran" class="form-label">Upload Bukti Pembayaran Baru</label>
      <input type="file" class="form-control" id="buktiPembayaran" name="bukti" accept="image/*,application/pdf" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Bukti</button>
    <a href="/riwayat-pembayaran" class="btn btn-secondary">Kembali</a>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
  const registrasiId = {{ $registrasiId }};

  async function loadCurrentProof() {
    const token = localStorage.getItem('token');
    if (!token) {
      $('#alertContainer').html('<div class="alert alert-warning">Silakan login terlebih dahulu.</div>');
      return;
    }

    try {
      const res = await fetch(`http://localhost:3000/api/events/riwayat-pembayaran/registrasi/${registrasiId}`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      if (!res.ok) throw new Error('Gagal mengambil data bukti pembayaran');

      const data = await res.json();
      const payment = data.payment?.[0];

      if (payment && payment.payment_proof_path) {
        const fileExt = payment.payment_proof_path.split('.').pop().toLowerCase();
        let content = '';

        if (['png', 'jpg', 'jpeg', 'gif'].includes(fileExt)) {
          content = `<img src="http://localhost:3000${payment.payment_proof_path}" alt="Bukti Pembayaran" style="max-width:100%; max-height:400px;">`;
        } else {
          content = `<a href="http://localhost:3000${payment.payment_proof_path}" target="_blank">Lihat Bukti Pembayaran (File)</a>`;
        }

        $('#currentProofContainer').html(content);
      } else {
        $('#currentProofContainer').html('<p>Tidak ada bukti pembayaran yang diupload.</p>');
      }
    } catch (err) {
      $('#alertContainer').html(`<div class="alert alert-danger">${err.message}</div>`);
    }
  }

  $(document).ready(() => {
    loadCurrentProof();

    $('#updatePaymentForm').on('submit', async (e) => {
      e.preventDefault();

      const token = localStorage.getItem('token');
      if (!token) {
        alert('Silakan login terlebih dahulu.');
        return;
      }

      const formData = new FormData();
      const fileInput = $('#buktiPembayaran')[0];
      if (fileInput.files.length === 0) {
        alert('Pilih file bukti pembayaran baru.');
        return;
      }
      formData.append('bukti', fileInput.files[0]);

      try {
        const res = await fetch(`http://localhost:3000/api/events/update-payment/${registrasiId}`, {
          method: 'PUT',
          headers: { 'Authorization': `Bearer ${token}` },
          body: formData
        });

        const result = await res.json();
        if (!res.ok) throw new Error(result.message || 'Gagal update bukti');

        alert('Bukti pembayaran berhasil diupdate!');
        window.location.href = '/riwayat-pembayaran';
      } catch (err) {
        alert('Error: ' + err.message);
      }
    });
  });
</script>
@endsection
