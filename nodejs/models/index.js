const Event = require("./event");
const Category = require("./category");
const Speaker = require("./speaker");

// Relasi Event ↔ Category
Event.belongsToMany(Category, {
  through: "events_has_category",
  foreignKey: "events_idevents",
  otherKey: "category_idcategory",
  as: "categories",
});
Category.belongsToMany(Event, {
  through: "events_has_category",
  foreignKey: "category_idcategory",
  otherKey: "events_idevents",
  as: "events",
});

// Relasi Event ↔ Speaker
Event.belongsToMany(Speaker, {
  through: "events_has_speaker",
  foreignKey: "events_idevents",
  otherKey: "speaker_idspeaker",
  as: "speakers", // gunakan 'speakers' agar sama dengan di include
});
Speaker.belongsToMany(Event, {
  through: "events_has_speaker",
  foreignKey: "speaker_idspeaker",
  otherKey: "events_idevents",
  as: "events",
});

module.exports = {
  Event,
  Category,
  Speaker,
};
