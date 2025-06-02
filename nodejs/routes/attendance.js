const express = require("express");
const router = express.Router();
const { Attendance } = require("../models/semuaRelasi");

// Update attendance status to 'attend' by registrations_detail_idregistrations_detail
router.post("/update-status", async (req, res) => {
  try {
    const { idregistrations_detail } = req.body;
    if (!idregistrations_detail) {
      return res
        .status(400)
        .json({ message: "idregistrations_detail wajib diisi" });
    }
    // Find the attendance record
    const attendance = await Attendance.findOne({
      where: {
        registrations_detail_idregistrations_detail: idregistrations_detail,
      },
    });
    if (!attendance) {
      return res.status(404).json({ message: "Attendance tidak ditemukan" });
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
