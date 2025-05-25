const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const EventDetail = require("./event_detail");
const Registrasi = require("./registrations");

const RegistrasiDetail = sequelize.define("RegistrasiDetail", {
  idregistrations_detail: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  registrations_idregistrations: {
      type: DataTypes.INTEGER,
      references: {
        model: Registrasi,
        key: "idregistrations",
      },
  },
  event_detail_idevent_detail: {
      type: DataTypes.INTEGER,
      references: {
        model: EventDetail,
        key: "idevent_detail",
      },
  },
  
}, {
  tableName: "registrations_detail",
  timestamps: false,
});

module.exports = RegistrasiDetail;
