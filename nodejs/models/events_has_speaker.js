const Event = require("./event");
const Speaker = require("./speaker");

Event.belongsToMany(Speaker, {
  through: "events_has_speaker",
  foreignKey: "events_idevents",
  otherKey: "speaker_idspeaker",
  as: "sp",
});

Speaker.belongsToMany(Event, {
  through: "events_has_speaker",
  foreignKey: "speaker_idspeaker",
  otherKey: "events_idevents",
  as: "events",
});

module.exports = {
  Event,
  Speaker,
};