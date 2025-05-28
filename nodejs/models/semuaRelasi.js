const Event = require("./event");
const Category = require("./category");
const EventDetail = require("./event_detail");
const Speaker = require("./speaker");
const Role = require("./role");
const User = require("./user");
const Registrasi = require("./registrations");
const RegistrasiDetail = require("./registrations_detail");
const Payment = require("./payments");
const Attendance = require("./attendances");
const EventsHasCategory = require("./events_has_category_model");
const EventDetailHasSpeaker = require("./event_detail_has_speaker_model");

Event.belongsToMany(Category, {
  through: {
    model: EventsHasCategory,
    attributes: [],
  },
  foreignKey: "events_idevents",
  otherKey: "category_idcategory",
  as: "categories",
});
Category.belongsToMany(Event, {
  through: {
    model: EventsHasCategory,
    attributes: [],
  },
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
  through: {
    model: EventDetailHasSpeaker,
    timestamps: false,
  },
  foreignKey: "event_detail_idevent_detail",
  otherKey: "speaker_idspeaker",
  as: "speakers",
});
Speaker.belongsToMany(EventDetail, {
  through: {
    model: EventDetailHasSpeaker,
    timestamps: false,
  },
  foreignKey: "speaker_idspeaker",
  otherKey: "event_detail_idevent_detail",
  as: "details",
});

Role.hasMany(User, {
  foreignKey: "roles_idroles",
});
User.belongsTo(Role, {
  foreignKey: "roles_idroles",
});

User.hasMany(Registrasi, {
  foreignKey: "users_idusers",
  // as: "registrasi",
});
Registrasi.belongsTo(User, {
  foreignKey: "users_idusers",
  // as: "user",
});

Event.hasMany(Registrasi, {
  foreignKey: "events_idevents",
  as: "registrasi",
});
Registrasi.belongsTo(Event, {
  foreignKey: "events_idevents",
  as: "events",
});

Registrasi.hasMany(Payment, {
  foreignKey: "registrations_idregistrations",
  as: "payment",
});
Payment.belongsTo(Registrasi, {
  foreignKey: "registrations_idregistrations",
  as: "registrasi",
});

Registrasi.hasMany(RegistrasiDetail, {
  foreignKey: "registrations_idregistrations",
  as: "registrasiDetail",
});
RegistrasiDetail.belongsTo(Registrasi, {
  foreignKey: "registrations_idregistrations",
  // as: "registrasi",
});

EventDetail.hasMany(RegistrasiDetail, {
  foreignKey: "event_detail_idevent_detail",
  // as: "payment",
});
RegistrasiDetail.belongsTo(EventDetail, {
  foreignKey: "event_detail_idevent_detail",
  as: "eventDetail",
});

RegistrasiDetail.hasMany(Attendance, {
  foreignKey: "registrations_detail_idregistrations_detail",
  // as: "attendance",
});
Attendance.belongsTo(RegistrasiDetail, {
  foreignKey: "registrations_detail_idregistrations_detail",
  // as: "registrasi",
});

module.exports = {
  Event,
  Category,
  EventDetail,
  Speaker,
  User,
  Role,
  Registrasi,
  RegistrasiDetail,
  Payment,
  Attendance,
};
