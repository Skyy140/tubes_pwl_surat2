const { Sequelize } = require("sequelize");

const sequelize = new Sequelize("event", "root", "it12345*", {
  host: "localhost",
  dialect: "mysql",
});

module.exports = sequelize;
