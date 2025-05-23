const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");

const EventsHasCategory = sequelize.define(
  "events_has_category",
  {
    events_idevents: {
      type: DataTypes.INTEGER,
      primaryKey: true,
    },
    category_idcategory: {
      type: DataTypes.INTEGER,
      primaryKey: true,
    },
  },
  {
    tableName: "events_has_category",
    timestamps: false,
  }
);

module.exports = EventsHasCategory;
