const { DataTypes } = require("sequelize");
const sequelize = require("../config/db");
const Registrasi = require("./registrations");

const Payment = sequelize.define("Payment", {
  idpayments: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  status: {
    type: DataTypes.ENUM("disetujui", "ditolak"),
    allowNull: false,
  },
  note: {
    type: DataTypes.STRING(300),
    defaultValue: "",
  },
  payment_proof_path: {
    type: DataTypes.STRING(255),
    defaultValue: "",
  },
  created_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  updated_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  verified_by: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  registrations_idregistrations: {
      type: DataTypes.INTEGER,
      references: {
        model: Registrasi,
        key: "idregistrations",
      },
  },
  
}, {
  tableName: "payments",
  timestamps: false,
});

module.exports = Payment;
