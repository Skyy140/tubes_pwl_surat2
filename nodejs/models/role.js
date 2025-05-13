const { DataTypes } = require("sequelize");
const db = require("../config/db");

const Role = db.define(
  "roles",
  {
    idroles: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING(20),
    },
  },
  {
    timestamps: false,
  }
);

module.exports = Role;
