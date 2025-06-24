const { Sequelize } = require("sequelize");

const sequelize = new Sequelize("event", "root", "", {
  host: "localhost",
  dialect: "mysql",
});

module.exports = sequelize;
