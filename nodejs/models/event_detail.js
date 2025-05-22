const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const Event = require("./event");

const EventDetail = sequelize.define("EventDetail", {
  idevent_detail: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  events_idevents: {
      type: DataTypes.INTEGER,
      references: {
        model: Event,
        key: "idevents",
      },
    },
  sesi: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },  
  date: {
    type: DataTypes.DATEONLY,
    allowNull: false,
  },
  time_start: {
    type: DataTypes.TIME,
    allowNull: false,
  },
  time_end: {
    type: DataTypes.TIME,
    allowNull: false,
  },
  description: {
    type: DataTypes.STRING(300),
    allowNull: false,
  },
}, {
  tableName: "event_detail",
  timestamps: false,
});

module.exports = EventDetail;
