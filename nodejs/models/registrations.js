const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const EventDetail = require("./event_detail");
const User = require("./user");

const Registrasi = sequelize.define("Registrasi", {
  idregistrations: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  status: {
    type: DataTypes.ENUM("pending", "complete"),
    allowNull: false,
    defaultValue: "pending",
  },
  registration_date: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  created_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  updated_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  qr_code: {
    type: DataTypes.STRING(255),
    defaultValue: "",
  },
  users_idusers: {
      type: DataTypes.INTEGER,
      references: {
        model: User,
        key: "idusers",
      },
  },
  events_idevents: {
      type: DataTypes.INTEGER,
      references: {
        model: Event,
        key: "idevents",
      },
  },
  
}, {
  tableName: "registrations",
  timestamps: false,
});

module.exports = Registrasi;
