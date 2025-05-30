const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const RegistrasiDetail = require("./registrations_detail");

const Attendance = sequelize.define(
  "Attendance",
  {
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
    registrations_detail_idregistrations_detail: {
      type: DataTypes.INTEGER,
      references: {
        model: RegistrasiDetail,
        key: "idregistrations_detail",
      },
    },
  },
  {
    tableName: "attendances",
    timestamps: false,
  }
);

module.exports = Attendance;
