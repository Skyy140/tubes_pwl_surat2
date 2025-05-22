const Event = require("./event");
const Category = require("./category");
const EventDetail = require("./event_detail");
const Speaker = require("./speaker");

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

Event.hasMany(EventDetail, {
  foreignKey: "events_idevents",
  as: "details",
});
EventDetail.belongsTo(Event, {
  foreignKey: "events_idevents",
  as: "event",
});

EventDetail.belongsToMany(Speaker, {
  through: "event_detail_has_speaker",
  foreignKey: "event_detail_idevent_detail",
  otherKey: "speaker_idspeaker",
  as: "speakers",
});
Speaker.belongsToMany(EventDetail, {
  through: "event_detail_has_speaker",
  foreignKey: "speaker_idspeaker",
  otherKey: "event_detail_idevent_detail",
  as: "details",
});



module.exports = {
  Event,
  Category,
  EventDetail,
  Speaker,
};

