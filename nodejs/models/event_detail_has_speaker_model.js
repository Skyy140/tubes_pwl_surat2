const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");

const EventDetailHasSpeaker = sequelize.define(
  "event_detail_has_speaker",
  {
    event_detail_idevent_detail: {
      type: DataTypes.INTEGER,
      primaryKey: true,
    },
    speaker_idspeaker: {
      type: DataTypes.INTEGER,
      primaryKey: true,
    },
  },
  {
    tableName: "event_detail_has_speaker",
    timestamps: false,
  }
);

module.exports = EventDetailHasSpeaker;
