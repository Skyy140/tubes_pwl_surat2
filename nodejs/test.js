const sequelize = require("./config/db");

sequelize.authenticate()
  .then(() => {
    console.log("Koneksi berhasil!");
  })
  .catch((err) => {
    console.error("Gagal konek ke database:", err);
  });