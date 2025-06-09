const express = require("express");
const router = express.Router();
const {
  Attendance,
  RegistrasiDetail,
  Registrasi,
  Event,
} = require("../models/semuaRelasi");

// Update attendance status to 'attend' by registrations_detail_idregistrations_detail
router.post("/update-status", async (req, res) => {
  try {
    // Paksa user_id dan coordinatorId jadi string untuk perbandingan yang konsisten
    const { idregistrations_detail, user_id, check_only } = req.body;
    if (!idregistrations_detail || !user_id) {
      return res
        .status(400)
        .json({ message: "idregistrations_detail dan user_id wajib diisi" });
    }

    // Cari attendance
    const attendance = await Attendance.findOne({
      where: {
        registrations_detail_idregistrations_detail: idregistrations_detail,
      },
    });
    if (!attendance) {
      return res.status(404).json({ message: "Attendance tidak ditemukan" });
    }

    // Join ke RegistrasiDetail -> Registrasi (as: 'Registrasi') -> Event (as: 'events')
    const regDetail = await RegistrasiDetail.findOne({
      where: { idregistrations_detail },
      include: [
        {
          model: Registrasi,
          as: "Registrasi",
          include: [
            {
              model: Event,
              as: "events",
            },
          ],
        },
      ],
    });
    if (!regDetail || !regDetail.Registrasi || !regDetail.Registrasi.events) {
      return res.status(404).json({ message: "Data event tidak ditemukan" });
    }

    const coordinatorId = regDetail.Registrasi.events.coordinator;
    // Pastikan perbandingan id tidak gagal karena tipe data (string vs number)
    if (String(user_id) !== String(coordinatorId)) {
      return res
        .status(403)
        .json({ message: "Silahkan cari panitia yang membuat event ini" });
    }

    // Jika check_only true, hanya return status attendance
    if (check_only) {
      return res.json({
        status: attendance.status,
        message: `Status attendance: ${attendance.status}`,
      });
    }

    attendance.status = "attend";
    await attendance.save();
    res.json({ message: "Status attendance berhasil diupdate" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Gagal update status attendance" });
  }
});

module.exports = router;
