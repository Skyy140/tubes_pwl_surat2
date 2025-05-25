const express = require("express");
const { Op } = require("sequelize");
const router = express.Router();
const multer = require("multer");
const path = require("path");
const fs = require("fs");
const {
  Event,
  Category,
  EventDetail,
  Speaker,
  Registrasi,
  Payment,
  RegistrasiDetail,
  User,
} = require("../models/semuaRelasi");

router.get("/", async (req, res) => {
  try {
    const categoryId = req.query.category;

    let include = [
      {
        model: Category,
        as: "categories",
        through: { attributes: [] },
      },
      {
        model: EventDetail,
        as: "details",
        include: [
          {
            model: Speaker,
            as: "speakers",
            through: { attributes: [] },
          },
        ],
      },
    ];

    if (categoryId) {
      include[0].where = { idcategory: categoryId };
    }

    const events = await Event.findAll({ include });

    res.json(events);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Terjadi kesalahan saat mengambil event" });
  }
});

router.get("/:id", async (req, res) => {
  try {
    const event = await Event.findByPk(req.params.id, {
      include: [
        {
          model: Category,
          as: "categories",
          through: { attributes: [] },
        },
        {
          model: EventDetail,
          as: "details",
          include: [
            {
              model: Speaker,
              as: "speakers",
              through: { attributes: [] },
            },
          ],
        },
      ],
    });

    if (!event) {
      return res.status(404).json({ message: "Event tidak ditemukan" });
    }

    res.json(event);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Terjadi kesalahan saat mengambil event" });
  }
});

// POST new event
router.post("/", async (req, res) => {
  try {
    const event = await Event.create({
      name: req.body.name,
      date: req.body.date,
      time: req.body.time,
      location: req.body.location,
      speaker: req.body.speaker,
      poster: req.body.poster,
      registration_fee: req.body.registration_fee,
      max_participants: req.body.max_participants,
      status: req.body.status,
    });
    res.status(201).json(event);
  } catch (err) {
    res.status(400).json({ message: err.message });
  }
});

// PUT update event
router.put("/:id", async (req, res) => {
  try {
    const event = await Event.findByPk(req.params.id);
    if (!event) return res.status(404).send("Event not found");

    await event.update({
      name: req.body.name,
      date: req.body.date,
      time: req.body.time,
      location: req.body.location,
      speaker: req.body.speaker,
      poster: req.body.poster,
      registration_fee: req.body.registration_fee,
      max_participants: req.body.max_participants,
      status: req.body.status,
    });

    res.json(event);
  } catch (err) {
    res.status(400).json({ message: err.message });
  }
});

// DELETE event
router.delete("/:id", async (req, res) => {
  try {
    const event = await Event.findByPk(req.params.id);
    if (!event) return res.status(404).send("Event not found");

    await event.destroy();
    res.sendStatus(204);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get("/categories/all", async (req, res) => {
  try {
    const categories = await Category.findAll({
      attributes: ["idcategory", "name"],
    });
    res.json(categories);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

// Route khusus untuk admin: ambil event_detail beserta speaker dan kategori untuk event tertentu
router.get("/admin/event-details/:eventId", async (req, res) => {
  try {
    const {
      EventDetail,
      Event,
      Category,
      Speaker,
    } = require("../models/semuaRelasi");
    const eventId = req.params.eventId;
    const details = await EventDetail.findAll({
      where: { events_idevents: eventId },
      attributes: [
        "date",
        "sesi",
        "time_start",
        "time_end",
        "description",
        "events_idevents",
      ],
      include: [
        {
          model: Event,
          as: "event",
          attributes: ["name"],
          include: [
            {
              model: Category,
              as: "categories",
              attributes: ["name"],
              through: { attributes: [] },
            },
          ],
        },
        {
          model: Speaker,
          as: "speakers",
          attributes: ["name"],
          through: { attributes: [] },
        },
      ],
    });
    res.json(details);
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Terjadi kesalahan saat mengambil data event_detail" });
  }
});

// Tambah event lengkap (event, event_detail, events_has_category)
router.post("/admin/tambah-event", async (req, res) => {
  const t = await require("../config/db").transaction();
  try {
    const {
      name,
      date_start,
      date_end,
      poster_path,
      time,
      location,
      registration_fee,
      max_participants,
      description,
      coordinator,
      categories,
      details,
    } = req.body;
    // 1. Insert ke tabel event
    const now = new Date();
    const event = await Event.create(
      {
        name,
        date_start,
        date_end,
        poster_path,
        time,
        location,
        registration_fee,
        max_participants,
        description,
        coordinator,
        status: "active",
        created_at: now,
        updated_at: now,
      },
      { transaction: t }
    );

    // 2. Insert ke tabel events_has_category
    if (Array.isArray(categories)) {
      for (const catId of categories) {
        if (catId) {
          await event.addCategory(catId, { transaction: t });
        }
      }
    }

    // 3. Insert ke tabel event_detail dan speaker per sesi
    if (Array.isArray(details)) {
      for (const det of details) {
        // Insert event_detail
        const eventDetail = await EventDetail.create(
          {
            events_idevents: event.idevents,
            sesi: det.sesi,
            date: det.date,
            time_start: det.time_start,
            time_end: det.time_end,
            description: det.description,
          },
          { transaction: t }
        );
        // Insert/relasi speakers untuk sesi ini
        if (Array.isArray(det.speakers) && det.speakers.length > 0) {
          for (const spk of det.speakers) {
            let speakerInstance = null;
            if (spk.idspeaker && spk.idspeaker !== "__new__") {
              // Pilih speaker lama
              speakerInstance = await Speaker.findByPk(spk.idspeaker);
            } else if (spk.name) {
              // Tambah speaker baru
              speakerInstance = await Speaker.create(
                {
                  name: spk.name,
                  description: spk.description,
                  photo_path: spk.photo_path,
                },
                { transaction: t }
              );
            }
            if (speakerInstance) {
              await eventDetail.addSpeaker(speakerInstance, { transaction: t });
            }
          }
        }
      }
    }

    await t.commit();
    res.status(201).json({ message: "Event berhasil ditambahkan", event });
  } catch (err) {
    await t.rollback();
    res.status(400).json({ message: err.message });
  }
});

// API untuk mengambil semua speaker (untuk dropdown di tambah event)
router.get("/admin/all-speakers", async (req, res) => {
  try {
    const speakers = await Speaker.findAll({
      attributes: ["idspeaker", "name", "description", "photo_path"],
    });
    res.json(speakers);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.post("/registrasi", async (req, res) => {
  try {
    const { userId, eventId, sesi } = req.body;

    if (!userId || !eventId || !Array.isArray(sesi) || sesi.length === 0) {
      return res.status(400).json({ message: "Data tidak lengkap" });
    }

    const detailData = await EventDetail.findAll({
      where: {
        idevent_detail: sesi,
        events_idevents: eventId,
      },
    });

    if (detailData.length !== sesi.length) {
      return res.status(400).json({ message: "Beberapa sesi tidak valid" });
    }

    const existing = await Registrasi.findOne({
      where: {
        users_idusers: userId,
        events_idevents: eventId,
      },
    });

    if (existing) {
      return res.status(409).json({ message: "Sudah terdaftar di event ini" });
    }

    const registrasi = await Registrasi.create({
      users_idusers: userId,
      events_idevents: eventId,
      status: "pending",
      qr_code: "",
    });

    const detailRegistrasi = await Promise.all(
      detailData.map((detail) =>
        RegistrasiDetail.create({
          registrations_idregistrations: registrasi.idregistrations,
          event_detail_idevent_detail: detail.idevent_detail,
        })
      )
    );

    res.status(201).json({
      message: "Registrasi berhasil",
      data: {
        registrasi,
        sesi: detailRegistrasi,
      },
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Terjadi kesalahan saat registrasi" });
  }
});

router.get("/registrasi/user/:userId", async (req, res) => {
  try {
    const userId = req.params.userId;
    const data = await Registrasi.findAll({
      where: { users_idusers: userId },
      include: [
        {
          model: RegistrasiDetail,
          as: "registrasiDetail",
          include: [
            {
              model: EventDetail,
              as: "eventDetail",
              include: [
                {
                  model: Event,
                  as: "event",
                  include: [
                    {
                      model: Category,
                      as: "categories",
                      through: { attributes: [] },
                    },
                  ],
                },
              ],
            },
          ],
        },
      ],
    });

    res.json(data);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal mengambil data registrasi" });
  }
});

// multer
const uploadDir = path.join(__dirname, "../public/uploads/payments");
if (!fs.existsSync(uploadDir)) {
  fs.mkdirSync(uploadDir, { recursive: true });
}

const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, uploadDir),
  filename: (req, file, cb) => cb(null, Date.now() + "-" + file.originalname),
});
const upload = multer({ storage });

router.post(
  "/upload-payment/:registrasiId",
  upload.single("bukti"),
  async (req, res) => {
    try {
      const registrasiId = req.params.registrasiId;

      const buktiPath = req.file
        ? `/uploads/payments/${req.file.filename}`
        : null;
      if (!buktiPath)
        return res
          .status(400)
          .json({ message: "Bukti pembayaran tidak ditemukan" });

      const reg = await Registrasi.findByPk(registrasiId);
      if (!reg)
        return res.status(404).json({ message: "Registrasi tidak ditemukan" });

      const payment = await Payment.create({
        status: "rejected",
        note: "",
        payment_proof_path: buktiPath,
        registrations_idregistrations: registrasiId,
      });

      res.status(201).json({
        message: "Bukti pembayaran berhasil diunggah",
        data: payment,
      });
    } catch (error) {
      console.error(error);
      res.status(500).json({ message: "Terjadi kesalahan saat upload bukti" });
    }
  }
);

router.get("/keuangan/registrasi", async (req, res) => {
  try {
    const data = await Registrasi.findAll({
      include: [
        {
          model: Payment,
          as: "payment",
          where: { payment_proof_path: { [Op.ne]: null } },
          required: true,
        },
        {
          model: User,
          as: "user",
          attributes: ["idusers", "name", "email"],
        },
        {
          model: Event,
          as: "events",
          attributes: ["idevents", "name"],
          include: [
            {
              model: EventDetail,
              as: "details",
            },
          ],
        },
      ],
      order: [["created_at", "DESC"]],
    });
    console.log("pppp" + JSON.stringify(data, null, 2));

    res.json(data);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal mengambil data registrasi" });
  }
});
router.get("/download/:filename", (req, res) => {
  const filename = req.params.filename;
  const filePath = path.join(__dirname, "../public/uploads/payments", filename);
  res.download(filePath, filename, (err) => {
    if (err) {
      console.error("Gagal download file:", err);
      res.status(500).send("Gagal mengunduh file.");
    }
  });
});

// router.post("/keuangan/registrasi/:id/approve", async (req, res) => {
//   try {
//     const id = req.params.id;
//     const payment = await Payment.findOne({ where: { registrations_idregistrations: id } });
//     if (!payment) return res.status(404).json({ message: "Data pembayaran tidak ditemukan" });

//     await payment.update({ status: "approved", note: req.body.note || "" });
//     await Registrasi.update({ status: "approved" }, { where: { idregistrations: id } });

//     res.json({ message: "Pembayaran disetujui" });
//   } catch (error) {
//     console.error(error);
//     res.status(500).json({ message: "Terjadi kesalahan saat menyetujui pembayaran" });
//   }
// });
// router.post("/keuangan/registrasi/:id/reject", async (req, res) => {
//   try {
//     const id = req.params.id;
//     const payment = await Payment.findOne({ where: { registrations_idregistrations: id } });
//     if (!payment) return res.status(404).json({ message: "Data pembayaran tidak ditemukan" });

//     await payment.update({ status: "rejected", note: req.body.note || "" });
//     await Registrasi.update({ status: "rejected" }, { where: { idregistrations: id } });

//     res.json({ message: "Pembayaran ditolak" });
//   } catch (error) {
//     console.error(error);
//     res.status(500).json({ message: "Terjadi kesalahan saat menolak pembayaran" });
//   }
// });

module.exports = router;
