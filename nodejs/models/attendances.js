const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const Registrasi = require("./registrations");

const Attendance = sequelize.define("Attendance", {
  idattendances: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  status: {
    type: DataTypes.ENUM("attend", "nattend"),
    allowNull: false,
  },
  certificate_path: {
    type: DataTypes.STRING(255),
    defaultValue: "",
  },
  registrations_idregistrations: {
      type: DataTypes.INTEGER,
      references: {
        model: Registrasi,
        key: "idregistrations",
      },
  },
  
}, {
  tableName: "attendances",
  timestamps: false,
});

module.exports = Attendance;
