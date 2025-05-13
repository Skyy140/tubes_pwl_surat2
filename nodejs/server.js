const express = require("express");
const cors = require("cors");
const app = express();

const sequelize = require("./config/db"); // koneksi DB
const eventsRouter = require("./routes/event"); // router event
const authRouter = require("./routes/auth"); // router login (baru)

const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors()); // aktifkan CORS agar bisa diakses dari frontend
app.use(express.json()); // untuk parsing JSON body

// Routes
app.use("/api/events", eventsRouter);
app.use("/api/auth", authRouter); // route login

// Test DB Connection & Sync
sequelize
  .authenticate()
  .then(() => {
    console.log(
      "Connection to the database has been established successfully."
    );
    return sequelize.sync(); // sinkronisasi model dengan DB
  })
  .then(() => {
    // Start server
    app.listen(PORT, () => {
      console.log(`Server is running on http://localhost:${PORT}`);
    });
  })
  .catch((err) => {
    console.error("Unable to connect to the database:", err);
  });
