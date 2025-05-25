const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");

const Event = sequelize.define("Event", {
  idevents: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  name: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  date_start: {
    type: DataTypes.DATEONLY,
    allowNull: false,
  },
  date_end: {
    type: DataTypes.DATEONLY,
    allowNull: false,
  },
  poster_path: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
  time: {
    type: DataTypes.TIME,
    allowNull: false,
  },
  location: {
    type: DataTypes.STRING(150),
    allowNull: false,
  },
  // speaker: {
  //   type: DataTypes.STRING(100),
  //   allowNull: false,
  // },
  registration_fee: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  max_participants: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  status: {
    type: DataTypes.ENUM("active", "inactive"),
    allowNull: false,
    defaultValue: "active",
  },
  created_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
    allowNull: true,
  },
  updated_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
    allowNull: true,
  },
  description: {
    type: DataTypes.STRING(400),
    defaultValue: "",
  },
  coordinator: {
    type: DataTypes.INTEGER,
    defaultValue: 0,
  },
}, {
  tableName: "events",
  timestamps: false,
});

module.exports = Event;
