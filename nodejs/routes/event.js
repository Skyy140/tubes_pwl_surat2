const express = require("express");
const router = express.Router();
const { Event, Category, EventDetail, Speaker } = require("../models"); 

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
          model: Speaker,
          as: "speakers",
          through: { attributes: [] },
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
      attributes: ['idcategory', 'name']
    });
    res.json(categories);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
});

module.exports = router;
