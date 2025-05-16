const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");

const Speaker = sequelize.define("Speaker", {
  idspeaker: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  name: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  description: {
    type: DataTypes.STRING(150),
    allowNull: true,
  },
  photo_path: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
}, {
  tableName: "speaker",
  timestamps: false,
});


module.exports = Speaker;