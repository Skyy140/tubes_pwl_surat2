const express = require("express");
const router = express.Router();
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");
const User = require("../models/user");
const Role = require("../models/role");

const JWT_SECRET = "your_jwt_secret"; // Ganti dengan secret environment jika production

router.post("/login", async (req, res) => {
  const { email, password } = req.body;

  try {
    // Cari user berdasarkan email
    const user = await User.findOne({
      where: { email },
      include: Role,
    });

    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }

    // Bandingkan password
    const valid = await bcrypt.compare(password, user.password);
    if (!valid) {
      return res.status(401).json({ message: "Invalid password" });
    }

    // Buat JWT token
    const token = jwt.sign(
      {
        id: user.idusers,
        email: user.email,
        role: user.role ? user.role.name : null,
      },
      JWT_SECRET,
      { expiresIn: "1h" }
    );

    return res.json({ message: "Login successful", token });
  } catch (err) {
    console.error("Login error:", err);
    return res.status(500).json({ message: "Server error" });
  }
});

module.exports = router;
