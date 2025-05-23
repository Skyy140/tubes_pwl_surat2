const express = require("express");
const router = express.Router();
const {
  Event,
  Category,
  EventDetail,
  Speaker,
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

// GET event by ID
// router.get("/:id", async (req, res) => {
//   try {
//     const event = await Event.findByPk(req.params.id, {
//       include: [
//         {
//           model: Category,
//           as: "categories",
//           through: { attributes: [] },
//         },
//         {
//           model: Speaker,
//           as: "speakers",
//           through: { attributes: [] },
//         },
//       ],
//     });

//     if (!event) {
//       return res.status(404).json({ message: "Event tidak ditemukan" });
//     }

//     res.json(event);
//   } catch (error) {
//     console.error(error);
//     res.status(500).json({ message: "Terjadi kesalahan saat mengambil event" });
//   }
// });
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
      speakers,
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

    // 3. Insert ke tabel event_detail
    let eventDetailInstances = [];
    if (Array.isArray(details)) {
      for (const det of details) {
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
        eventDetailInstances.push(eventDetail);
      }
    }

    // 4. Insert ke tabel speaker dan relasi ke event_detail_has_speaker
    let speakerIds = [];
    if (Array.isArray(speakers)) {
      for (const spk of speakers) {
        const speaker = await Speaker.create(
          {
            name: spk.name,
            description: spk.description,
            photo_path: spk.photo_path,
          },
          { transaction: t }
        );
        speakerIds.push(speaker.idspeaker);
      }
    }
    // Hubungkan semua speaker ke semua sesi (event_detail)
    if (speakerIds.length && eventDetailInstances.length) {
      for (const eventDetail of eventDetailInstances) {
        await eventDetail.addSpeakers(speakerIds, { transaction: t });
      }
    }

    await t.commit();
    res.status(201).json({ message: "Event berhasil ditambahkan", event });
  } catch (err) {
    await t.rollback();
    res.status(400).json({ message: err.message });
  }
});
module.exports = router;
