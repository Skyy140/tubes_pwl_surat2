const auth = require("../middleware/auth");
const role = require("../middleware/role");
const express = require("express");
const { Sequelize, Op } = require("sequelize");
const router = express.Router();
const multer = require("multer");
const path = require("path");
const QRCode = require("qrcode");
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
  Attendance,
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

    const events = await Event.findAll({
      where: { status: "active" },
      include,
    });

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

// Tambah event lengkap (event, event_detail, events_has_category) dengan upload poster
const posterStorage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dest = path.join(__dirname, "../public/poster");
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    cb(null, dest);
  },
  filename: function (req, file, cb) {
    const unique = Date.now() + "-" + Math.round(Math.random() * 1e9);
    cb(null, unique + path.extname(file.originalname));
  },
});
const uploadPoster = multer({ storage: posterStorage });

router.post(
  "/admin/tambah-event",
  uploadPoster.single("poster"),
  async (req, res) => {
    const t = await require("../config/db").transaction();
    try {
      // Data dari form-data
      const {
        name,
        date_start,
        date_end,
        time,
        location,
        registration_fee,
        max_participants,
        description,
      } = req.body;
      // Ambil id user dari JWT jika ada Authorization header
      let coordinator = req.body.coordinator;
      if (!coordinator && req.headers.authorization) {
        try {
          const token = req.headers.authorization.replace("Bearer ", "");
          const decoded = require("jsonwebtoken").decode(token);
          if (decoded && decoded.id) coordinator = decoded.id;
        } catch (e) {}
      }
      // categories[] dan details (JSON string)
      let categories = req.body["categories[]"] || req.body.categories || [];
      if (typeof categories === "string") categories = [categories];
      let details = req.body.details;
      if (typeof details === "string") details = JSON.parse(details);
      // Poster
      let poster_path = null;
      if (req.file) {
        poster_path = "/poster/" + req.file.filename;
      }
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
                await eventDetail.addSpeaker(speakerInstance, {
                  transaction: t,
                });
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
  }
);

// Edit event lengkap (event, event_detail, events_has_category) dengan upload poster
router.post(
  "/admin/edit-event/:id",
  uploadPoster.single("poster"),
  async (req, res) => {
    const t = await require("../config/db").transaction();
    try {
      const eventId = req.params.id;
      const {
        name,
        date_start,
        date_end,
        time,
        location,
        registration_fee,
        max_participants,
        description,
        coordinator,
      } = req.body;
      let categories = req.body["categories[]"] || req.body.categories || [];
      if (typeof categories === "string") categories = [categories];
      let details = req.body.details;
      if (typeof details === "string") details = JSON.parse(details);
      let poster_path = null;
      if (req.file) {
        poster_path = "/poster/" + req.file.filename;
      }
      // 1. Update event
      const event = await Event.findByPk(eventId, { transaction: t });
      if (!event) {
        await t.rollback();
        return res.status(404).json({ message: "Event tidak ditemukan" });
      }
      event.name = name;
      event.date_start = date_start;
      event.date_end = date_end;
      if (poster_path) event.poster_path = poster_path;
      event.time = time;
      event.location = location;
      event.registration_fee = registration_fee;
      event.max_participants = max_participants;
      event.description = description;
      event.coordinator = coordinator;
      event.updated_at = new Date();
      await event.save({ transaction: t });
      // 2. Update categories (remove all, then add again)
      await event.setCategories([], { transaction: t });
      if (Array.isArray(categories)) {
        for (const catId of categories) {
          if (catId) {
            await event.addCategory(catId, { transaction: t });
          }
        }
      }
      // 3. Update event_detail and speakers: remove all, then add again
      // 3a. Hapus relasi event_detail_has_speaker terlebih dahulu
      const eventDetails = await EventDetail.findAll({
        where: { events_idevents: eventId },
        transaction: t,
      });
      const eventDetailIds = eventDetails.map((ed) => ed.idevent_detail);
      if (eventDetailIds.length > 0) {
        // Hapus dari tabel pivot event_detail_has_speaker
        await Event.sequelize.models.event_detail_has_speaker.destroy({
          where: { event_detail_idevent_detail: eventDetailIds },
          transaction: t,
        });
      }
      // 3b. Hapus event_detail
      await EventDetail.destroy({
        where: { events_idevents: eventId },
        transaction: t,
      });
      // 3c. Tambahkan ulang event_detail dan speakers
      if (Array.isArray(details)) {
        for (const det of details) {
          const eventDetail = await EventDetail.create(
            {
              events_idevents: eventId,
              sesi: det.sesi,
              date: det.date,
              time_start: det.time_start,
              time_end: det.time_end,
              description: det.description,
            },
            { transaction: t }
          );
          if (Array.isArray(det.speakers) && det.speakers.length > 0) {
            for (const spk of det.speakers) {
              let speakerInstance = null;
              if (spk.idspeaker && spk.idspeaker !== "__new__") {
                speakerInstance = await Speaker.findByPk(spk.idspeaker);
              } else if (spk.name) {
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
                await eventDetail.addSpeaker(speakerInstance, {
                  transaction: t,
                });
              }
            }
          }
        }
      }
      await t.commit();
      res.status(200).json({ message: "Event berhasil diupdate", event });
    } catch (err) {
      await t.rollback();
      res.status(400).json({ message: err.message });
    }
  }
);

// PATCH: Inactivate event (set status to 'inactive')
router.patch("/inactivate/:id", async (req, res) => {
  try {
    const event = await Event.findByPk(req.params.id);
    if (!event)
      return res.status(404).json({ message: "Event tidak ditemukan" });
    if (event.status === "inactive") {
      return res.status(400).json({ message: "Event sudah inactive" });
    }
    event.status = "inactive";
    await event.save();
    res.json({ message: "Status event berhasil diubah menjadi inactive" });
  } catch (err) {
    res.status(500).json({ message: err.message });
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
      status: "menunggu",
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
        {
          model: Payment,
          as: "payment",
          required: false,
        },
      ],
      having: Sequelize.literal("`payment`.`idpayments` IS NULL"),
    });

    res.json(data);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal mengambil data registrasi" });
  }
});

// hapus daftar
router.delete("/registrasi/:id", async (req, res) => {
  try {
    const id = req.params.id;

    await RegistrasiDetail.destroy({
      where: { registrations_idregistrations: id },
    });

    await Registrasi.destroy({ where: { idregistrations: id } });

    res.json({ message: "Registrasi berhasil dihapus" });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal menghapus registrasi" });
  }
});
// end hapus daftar

// riwayat
router.get("/riwayat-pembayaran/user/:userId", async (req, res) => {
  try {
    const userId = req.params.userId;

    const data = await Registrasi.findAll({
      where: { users_idusers: userId },
      subQuery: false,
      include: [
        {
          model: Payment,
          as: "payment",
          required: true,
        },
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
    res.status(500).json({
      message: "Gagal mengambil data registrasi dengan payment",
    });
  }
});

router.get("/keuangan/riwayat-pembayaran", auth, role(["keuangan", "member"]), async (req, res) => {
  try {
    const data = await Registrasi.findAll({
      subQuery: false,
      include: [
        {
          model: User,
          as: "user",
          required: true,
        },
        {
          model: Payment,
          as: "payment",
          required: true,
        },
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
    res.status(500).json({
      message: "Gagal mengambil data registrasi dengan payment",
    });
  }
});

router.get(
  "/keuangan/riwayat-pembayaran-detail/:eventId/:userId", auth, role(["keuangan"]),
  async (req, res) => {
    try {
      const { userId, eventId } = req.params;
      console.log("Params", req.params);
      const data = await Registrasi.findAll({
        where: { users_idusers: userId, events_idevents: eventId },
        include: [
          {
            model: User,
            as: "user",
            required: true,
          },
          {
            model: Payment,
            as: "payment",
            required: false,
          },
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
      console.log(data);
    } catch (error) {
      console.error(error);
      res.status(500).json({
        message: "Gagal mengambil data registrasi dengan payment",
      });
    }
  }
);

router.get("/riwayat-pembayaran/registrasi/:registrasiId", async (req, res) => {
  try {
    const { registrasiId } = req.params;

    const payment = await Payment.findAll({
      where: { registrations_idregistrations: registrasiId },
    });

    if (!payment || payment.length === 0) {
      return res
        .status(404)
        .json({ message: "Bukti pembayaran tidak ditemukan" });
    }

    res.status(200).json({ payment });
  } catch (err) {
    console.error(err);
    res
      .status(500)
      .json({ message: "Terjadi kesalahan saat mengambil bukti pembayaran" });
  }
});
// end riwayat + detail

// bikin bukti pembayaran
const getRegistrasiInfo = async (req, res, next) => {
  try {
    const registrasiId = req.params.registrasiId;
    const reg = await Registrasi.findOne({
      where: { idregistrations: registrasiId },
      include: [
        { model: User },
        {
          model: Event,
          as: "events",
        },
      ],
    });

    if (!reg)
      return res.status(404).json({ message: "Registrasi tidak ditemukan" });

    req.registrasiInfo = reg;
    next();
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal mengambil data registrasi" });
  }
};

const upload = multer({ storage: multer.memoryStorage() });
const uploadDir = path.join(__dirname, "../public/uploads/payments");
router.post(
  "/upload-payment/:registrasiId",
  getRegistrasiInfo,
  upload.single("bukti"),
  async (req, res) => {
    try {
      const reg = req.registrasiInfo;

      if (!req.file)
        return res
          .status(400)
          .json({ message: "Bukti pembayaran tidak ditemukan" });

      const sanitizedUserName = reg.user.name
        .replace(/[^a-z0-9]/gi, "_")
        .toLowerCase();
      const sanitizedEventName = reg.events.name
        .replace(/[^a-z0-9]/gi, "_")
        .toLowerCase();
      const tanggal = new Date(reg.created_at).toISOString().split("T")[0];

      const fileName = `${sanitizedUserName}-${sanitizedEventName}-${tanggal}.png`;
      const filePath = path.join(uploadDir, fileName);

      fs.writeFileSync(filePath, req.file.buffer);

      const dbPath = `/uploads/payments/${fileName}`;

      const payment = await Payment.create({
        status: "ditolak",
        note: "",
        payment_proof_path: dbPath,
        registrations_idregistrations: reg.idregistrations,
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
// end bikin bukti pembayaran

// update bukti pembayaran
router.put(
  "/update-payment/:registrasiId",
  getRegistrasiInfo,
  upload.single("bukti"),
  async (req, res) => {
    try {
      const reg = req.registrasiInfo;

      if (!req.file)
        return res
          .status(400)
          .json({ message: "Bukti pembayaran tidak ditemukan" });

      const sanitizedUserName = reg.user.name
        .replace(/[^a-z0-9]/gi, "_")
        .toLowerCase();
      const sanitizedEventName = reg.events.name
        .replace(/[^a-z0-9]/gi, "_")
        .toLowerCase();
      const tanggal = new Date(reg.created_at).toISOString().split("T")[0];

      const fileName = `${sanitizedUserName}-${sanitizedEventName}-${tanggal}.png`;
      const filePath = path.join(uploadDir, fileName);

      fs.writeFileSync(filePath, req.file.buffer);

      const dbPath = `/uploads/payments/${fileName}`;

      const existingPayment = await Payment.findOne({
        where: { registrations_idregistrations: reg.idregistrations },
      });

      if (existingPayment) {
        existingPayment.payment_proof_path = dbPath;
        existingPayment.status = "ditolak";
        await existingPayment.save();
      } else {
        await Payment.create({
          status: "ditolak",
          note: "",
          payment_proof_path: dbPath,
          registrations_idregistrations: reg.idregistrations,
        });
      }

      res.status(200).json({
        message: "Bukti pembayaran berhasil diupdate",
      });
    } catch (error) {
      console.error(error);
      res.status(500).json({ message: "Terjadi kesalahan saat update bukti" });
    }
  }
);
// end update bukti pembayaran

// biar bisa download
router.get("/download/:filename", (req, res) => {
  const filename = req.params.filename;
  const filePath = path.join(__dirname, "../public/uploads/payments", filename);

  if (fs.existsSync(filePath)) {
    res.download(filePath);
  } else {
    res.status(404).json({ message: "File tidak ditemukan" });
  }
});
// end biar bisa download

// awal tim keuangan
router.get("/keuangan/registrasi", async (req, res) => {
  try {
    const data = await Registrasi.findAll({
      where: {
        status: "menunggu",
      },
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
        },
      ],
      order: [["created_at", "DESC"]],
    });

    res.json(data);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: "Gagal mengambil data registrasi" });
  }
});
// end awal tim keuangan

// ambil buat detail
router.get("/events/:id", async (req, res) => {
  try {
    const event = await Event.findByPk(req.params.id, {
      attributes: ["idevents", "name"],
      include: [
        {
          model: EventDetail,
          as: "details",
        },
        {
          model: Registrasi,
          as: "registrasi",
        },
      ],
    });

    if (!event) {
      return res.status(404).json({ message: "Event tidak ditemukan" });
    }

    res.json(event);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Gagal mengambil data event" });
  }
});
// end ambil buat detail
// buat munculin qr, klo mau pdf ga perlu, pakai atas aja
router.get("/event-detail-with-qr/:id", async (req, res) => {
  const userId = req.query.userId;

  try {
    const event = await Event.findByPk(req.params.id, {
      attributes: [
        "idevents",
        "name",
        "description",
        "date_start",
        "date_end",
        "location",
      ],
      include: [
        {
          model: EventDetail,
          as: "details",
        },
        {
          model: Registrasi,
          as: "registrasi",
          where: { users_idusers: userId },
          attributes: ["qr_code", "idregistrations"],
          required: false,
          include: [
            {
              model: RegistrasiDetail,
              as: "registrasiDetail",
              include: [
                {
                  model: Attendance,
                  as: "hadir",
                  attributes: ["certificate_path"],
                },
              ],
            },
          ],
        },
      ],
    });

    if (!event) {
      return res.status(404).json({ message: "Event tidak ditemukan" });
    }

    const result = {
      ...event.toJSON(),
      registrasi: event.registrasi?.[0] || null,
    };
    console.log(JSON.stringify(result, null, 2));
    res.json(result);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Gagal mengambil data event" });
  }
});

const PDFDocument = require("pdfkit");

router.post("/registrasi/:id/approve", async (req, res) => {
  try {
    const id = req.params.id;

    const payment = await Payment.findOne({
      where: { registrations_idregistrations: id },
    });
    if (!payment)
      return res
        .status(404)
        .json({ message: "Data pembayaran tidak ditemukan" });

    const registrasi = await Registrasi.findOne({
      where: { idregistrations: id },
      include: [{ model: User }, { model: Event, as: "events" }],
    });
    if (!registrasi)
      return res
        .status(404)
        .json({ message: "Data registrasi tidak ditemukan" });

    await payment.update({ status: "disetujui", note: req.body.note || "" });
    await Registrasi.update(
      { status: "selesai" },
      { where: { idregistrations: id } }
    );

    // Ambil registrations_detail yang sudah ada untuk registrasi ini (sesi yang dipilih user)
    const regDetails = await RegistrasiDetail.findAll({
      where: { registrations_idregistrations: id },
    });

    let sesi = [];
    let idregistrations_detail_arr = [];
    for (const regDetail of regDetails) {
      sesi.push({
        idregistrations_detail: regDetail.idregistrations_detail,
        event_detail_idevent_detail: regDetail.event_detail_idevent_detail,
      });
      idregistrations_detail_arr.push(regDetail.idregistrations_detail);
      // Insert ke attendances jika belum ada
      let attendance = await Attendance.findOne({
        where: {
          registrations_detail_idregistrations_detail:
            regDetail.idregistrations_detail,
        },
      });
      if (!attendance) {
        await Attendance.create({
          status: "nattend",
          registrations_detail_idregistrations_detail:
            regDetail.idregistrations_detail,
        });
      }
    }

    // QR code hanya berisi id penting agar sederhana
    const qrData = {
      registrasi_id: registrasi.idregistrations,
      user_id: registrasi.user.idusers,
      event_id: registrasi.events.idevents,
      idregistrations_detail: idregistrations_detail_arr,
    };
    const qrCodeBuffer = await QRCode.toBuffer(JSON.stringify(qrData));
    const UserName = registrasi.user.name
      .replace(/[^a-z0-9]/gi, "_")
      .toLowerCase();
    const EventName = registrasi.events.name
      .replace(/[^a-z0-9]/gi, "_")
      .toLowerCase();
    const now = new Date();
    const timestamp = now.toISOString().replace(/[:.]/g, "-");
    const qrFileName = `${UserName}-${EventName}-${id}-${timestamp}.png`;
    const qrDir = path.join(__dirname, "../public/uploads/qr");
    if (!fs.existsSync(qrDir)) fs.mkdirSync(qrDir, { recursive: true });
    const qrFilePath = path.join(qrDir, qrFileName);
    fs.writeFileSync(qrFilePath, qrCodeBuffer);

    const qrDbPath = `/uploads/qr/${qrFileName}`;
    await Registrasi.update(
      { qr_code: qrDbPath },
      { where: { idregistrations: id } }
    );

    const pdfDir = path.join(__dirname, "../public/uploads/bukti_pembayaran");
    if (!fs.existsSync(pdfDir)) fs.mkdirSync(pdfDir, { recursive: true });

    const pdfFileName = `${UserName}-${EventName}-${id}-${timestamp}.pdf`;
    const pdfFilePath = path.join(pdfDir, pdfFileName);

    const generatePdfBuktiPembayaran = (
      registrasi,
      qrCodeFilePath,
      outputFilePath
    ) => {
      return new Promise((resolve, reject) => {
        const doc = new PDFDocument({ margin: 50 });
        const stream = fs.createWriteStream(outputFilePath);

        doc.pipe(stream);

        doc.fontSize(20).text("Bukti Pembayaran", { align: "center" });
        doc.moveDown();

        doc.fontSize(14).text(`Nama Event: ${registrasi.events.name}`);
        doc.text(`Nama Peserta: ${registrasi.user.name}`);
        doc.text(`Status Registrasi: Selesai Pembayaran`);
        doc.moveDown();

        doc.image(qrCodeFilePath, {
          fit: [150, 150],
          align: "center",
          valign: "center",
        });

        doc.end();

        stream.on("finish", () => resolve());
        stream.on("error", (err) => reject(err));
      });
    };
    await generatePdfBuktiPembayaran(registrasi, qrFilePath, pdfFilePath);

    const pdfDbPath = `/uploads/bukti_pembayaran/${pdfFileName}`;
    await Registrasi.update(
      { bukti_pdf: pdfDbPath },
      { where: { idregistrations: id } }
    );

    res.json({
      message: "Pembayaran berhasil disetujui",
      qrCodePath: qrDbPath,
      pdfBuktiPath: pdfDbPath,
      sesi,
    });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Terjadi kesalahan saat menyetujui pembayaran" });
  }
});

router.post("/registrasi/:id/reject", async (req, res) => {
  try {
    const id = req.params.id;
    const { note } = req.body;

    const payment = await Payment.findOne({
      where: { registrations_idregistrations: id },
    });

    if (!payment) {
      return res
        .status(404)
        .json({ message: "Data pembayaran tidak ditemukan" });
    }

    await payment.update({
      status: "ditolak",
      note: note || "",
    });

    await Registrasi.update(
      { status: "gagal" },
      { where: { idregistrations: id } }
    );

    return res.json({ message: "Pembayaran ditolak" });
  } catch (error) {
    console.error("Error saat menolak pembayaran:", error);
    return res
      .status(500)
      .json({ message: "Terjadi kesalahan saat menolak pembayaran" });
  }
});
// end reject approve

// API untuk sertif.blade.php: event + kolom sesi (dari event_detail)
router.get("/api/events-sertif", async (req, res) => {
  try {
    const events = await Event.findAll({
      attributes: [
        "idevents",
        "name",
        "date_start",
        "date_end",
        "status",
        "coordinator",
      ],
      include: [
        {
          model: EventDetail,
          as: "details",
          attributes: ["sesi"],
        },
      ],
      order: [["idevents", "DESC"]],
    });
    res.json(events);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

router.get("/api/attendances/users", async (req, res) => {
  try {
    const { eventId, sesi } = req.query;
    if (!eventId || !sesi) {
      return res.status(400).json({ message: "eventId dan sesi wajib diisi" });
    }

    const attendances = await Attendance.findAll({
      where: { status: "attend" },
      include: [
        {
          model: RegistrasiDetail,
          include: [
            {
              model: EventDetail,
              as: "eventDetail",
              where: {
                sesi: sesi,
                events_idevents: eventId,
              },
              include: [
                {
                  model: Event,
                  as: "event",
                },
              ],
            },
            {
              model: Registrasi,
              include: [
                {
                  model: User,
                },
              ],
            },
          ],
        },
      ],
    });

    const results = attendances.map((a) => {
      const rd = a.RegistrasiDetail;
      const reg = rd?.Registrasi;
      const user = reg?.user || reg?.User;
      return {
        user_name: user?.name || "-",
        user_id: user?.idusers || "-",
        certificate_path: a.certificate_path || "",
        registrations_detail_idregistrations_detail:
          a.registrations_detail_idregistrations_detail,
      };
    });

    res.json(results);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Terjadi kesalahan saat memuat data." });
  }
});

// Upload sertifikat untuk user hadir pada sesi tertentu
const sertifStorage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dest = path.join(__dirname, "../public/sertif");
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    cb(null, dest);
  },
  filename: function (req, file, cb) {
    const unique = Date.now() + "-" + Math.round(Math.random() * 1e9);
    cb(null, unique + path.extname(file.originalname));
  },
});
const uploadSertif = multer({ storage: sertifStorage });

// POST /api/sertif/upload
router.post(
  "/api/sertif/upload",
  uploadSertif.single("sertif"),
  async (req, res) => {
    try {
      // Dapatkan user_id dan sesi dari body
      const { user_id, sesi, event_id } = req.body;
      if (!req.file)
        return res
          .status(400)
          .json({ message: "File sertifikat wajib diupload" });
      if (!user_id || !sesi || !event_id)
        return res
          .status(400)
          .json({ message: "user_id, sesi, event_id wajib diisi" });

      const sertifPath = `/sertif/${req.file.filename}`;

      // Cari event_detail (sesi) yang sesuai
      const eventDetail = await EventDetail.findOne({
        where: { sesi: sesi, events_idevents: event_id },
      });
      if (!eventDetail) {
        return res.status(404).json({ message: "Sesi event tidak ditemukan" });
      }

      // Cari registrations_detail yang sesuai user dan sesi
      const regDetail = await RegistrasiDetail.findOne({
        where: {
          event_detail_idevent_detail: eventDetail.idevent_detail,
        },
        include: [
          {
            model: Registrasi,
            where: { users_idusers: user_id, events_idevents: event_id },
          },
        ],
      });
      if (!regDetail) {
        return res
          .status(404)
          .json({ message: "Registrasi detail tidak ditemukan" });
      }

      console.log(
        "regDetail.idregistrations_detail",
        regDetail.idregistrations_detail
      );
      // Cari attendance dengan status attend
      let attendance = await Attendance.findOne({
        where: {
          registrations_detail_idregistrations_detail:
            regDetail.idregistrations_detail,
          status: "attend",
        },
      });
      if (!attendance) {
        return res.status(404).json({ message: "Attendance tidak ditemukan" });
      }
      if (attendance.status !== "attend") {
        return res
          .status(400)
          .json({ message: "User belum absen (status attend)" });
      }

      // (Opsional) Hapus file lama jika ada
      if (attendance.certificate_path && attendance.certificate_path !== "") {
        const oldPath = path.join(
          __dirname,
          "../public",
          attendance.certificate_path
        );
        if (fs.existsSync(oldPath)) {
          try {
            fs.unlinkSync(oldPath);
          } catch (e) {
            /* ignore */
          }
        }
      }

      attendance.certificate_path = sertifPath;
      await attendance.save();

      res.status(201).json({ message: "Upload berhasil", path: sertifPath });
    } catch (err) {
      console.error(err);
      res.status(500).json({ message: "Gagal upload sertifikat" });
    }
  }
);

// Get all sessions (sesi) for a registration (for QR scan modal)
router.get("/api/registrasi/:registrasiId/sessions", async (req, res) => {
  try {
    const { registrasiId } = req.params;
    // Ambil user id login dari header (Authorization: Bearer <token>)
    let userIdLogin = null;
    if (req.headers.authorization) {
      try {
        const token = req.headers.authorization.replace("Bearer ", "");
        const decoded = require("jsonwebtoken").decode(token);
        if (decoded && decoded.id) userIdLogin = decoded.id;
      } catch (e) {}
    }
    // Cari Registrasi dan event terkait
    const registrasi = await Registrasi.findByPk(registrasiId, {
      include: [
        {
          model: Event,
          as: "events",
          attributes: ["coordinator", "name"],
        },
      ],
    });
    if (!registrasi || !registrasi.events) {
      return res
        .status(404)
        .json({ message: "Registrasi atau event tidak ditemukan" });
    }
    // Cek apakah user login adalah koordinator event
    if (
      !userIdLogin ||
      String(userIdLogin) !== String(registrasi.events.coordinator)
    ) {
      return res
        .status(403)
        .json({ message: "Silahkan cari panitia yang membuat event ini" });
    }
    // Find all RegistrasiDetail for this registration, include EventDetail for sesi name
    const regDetails = await RegistrasiDetail.findAll({
      where: { registrations_idregistrations: registrasiId },
      include: [
        {
          model: EventDetail,
          as: "eventDetail",
          attributes: ["sesi", "date", "time_start", "time_end", "description"],
        },
      ],
    });
    if (!regDetails || regDetails.length === 0) {
      return res
        .status(404)
        .json({ message: "Tidak ada sesi untuk registrasi ini" });
    }
    // Return array of { idregistrations_detail, sesi, ... }
    const result = regDetails.map((rd) => ({
      idregistrations_detail: rd.idregistrations_detail,
      sesi: rd.eventDetail?.sesi || "-",
      date: rd.eventDetail?.date || null,
      time_start: rd.eventDetail?.time_start || null,
      time_end: rd.eventDetail?.time_end || null,
      description: rd.eventDetail?.description || null,
    }));
    res.json(result);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Gagal mengambil sesi untuk registrasi" });
  }
});

// Endpoint: Jumlah event per bulan untuk panitia (coordinator)
router.get("/count/by-month", async (req, res) => {
  try {
    // Ambil id user login dari query atau header (misal: req.query.coordinator atau dari token)
    let coordinator = req.query.coordinator;
    if (!coordinator && req.headers.authorization) {
      // Jika pakai JWT, decode token untuk dapatkan id user
      const token = req.headers.authorization.split(" ")[1];
      const jwt = require("jsonwebtoken");
      const decoded = jwt.decode(token);
      if (decoded && decoded.id) coordinator = decoded.id;
    }
    if (!coordinator)
      return res.status(400).json({ message: "Coordinator id diperlukan" });

    // Ambil semua event milik user login (coordinator)
    const events = await Event.findAll({
      where: { coordinator },
      attributes: ["created_at"],
    });
    // Hitung per bulan
    const counts = Array(12).fill(0);
    events.forEach((e) => {
      if (e.created_at) {
        const d = new Date(e.created_at);
        if (!isNaN(d)) counts[d.getMonth()]++;
      }
    });
    res.json(counts);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

// Endpoint: Pie chart kategori event per user login (coordinator)
router.get("/count/by-category", async (req, res) => {
  try {
    let coordinator = req.query.coordinator;
    if (!coordinator && req.headers.authorization) {
      const token = req.headers.authorization.split(" ")[1];
      const jwt = require("jsonwebtoken");
      const decoded = jwt.decode(token);
      if (decoded && decoded.id) coordinator = decoded.id;
    }
    if (!coordinator)
      return res.status(400).json({ message: "Coordinator id diperlukan" });

    // Ambil event milik user login beserta kategori
    const events = await Event.findAll({
      where: { coordinator },
      include: [
        {
          model: Category,
          as: "categories",
          through: { attributes: [] },
          attributes: ["idcategory", "name"],
        },
      ],
      attributes: ["idevents"],
    });
    // Hitung jumlah event per kategori
    const categoryCounts = {};
    events.forEach((event) => {
      event.categories.forEach((cat) => {
        if (!categoryCounts[cat.name]) categoryCounts[cat.name] = 0;
        categoryCounts[cat.name]++;
      });
    });
    res.json(categoryCounts);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

module.exports = router;
