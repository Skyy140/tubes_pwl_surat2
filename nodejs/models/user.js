const { DataTypes } = require("sequelize");
const db = require("../config/db");
const Role = require("./role");

const User = db.define(
  "users",
  {
    idusers: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: DataTypes.STRING(100),
    },
    email: {
      type: DataTypes.STRING(100),
      unique: true,
    },
    password: {
      type: DataTypes.STRING(255),
    },
    status: {
      type: DataTypes.ENUM("aktif", "tidak"),
      allowNull: false,
      defaultValue: "aktif",
    },
    created_at: {
      type: DataTypes.DATE,
    },
    updated_at: {
      type: DataTypes.DATE,
    },
    roles_idroles: {
      type: DataTypes.INTEGER,
      references: {
        model: Role,
        key: "idroles",
      },
    },
  },
  {
    timestamps: false,
  }
);

module.exports = User;
