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

    // Cek status user
    if (user.status !== "aktif") {
      return res.status(403).json({ message: "Akun sudah tidak aktif." });
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
        roles_idroles: user.roles_idroles,
      },
      JWT_SECRET,
      { expiresIn: "1h" }
    );

    return res.json({
      message: "Login successful",
      token,
      roles_idroles: user.roles_idroles,
    });
  } catch (err) {
    console.error("Login error:", err);
    return res.status(500).json({ message: "Server error" });
  }
});

router.post("/signup", async (req, res) => {
  const { name, email, password } = req.body;

  try {
    // Validasi input
    if (!name || !email || !password) {
      return res.status(400).json({ message: "All fields are required" });
    }

    // Cek apakah email sudah terdaftar
    const existingUser = await User.findOne({ where: { email } });
    if (existingUser) {
      return res.status(400).json({ message: "Email is already registered" });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Cari role member
    const memberRole = await Role.findOne({ where: { name: "member" } });
    if (!memberRole) {
      return res.status(500).json({ message: "Member role not found" });
    }

    // Buat user baru dengan status default "aktif"
    const newUser = await User.create({
      name,
      email,
      password: hashedPassword,
      roles_idroles: memberRole.idroles,
      status: "aktif",
    });

    return res
      .status(201)
      .json({ message: "User registered successfully", user: newUser });
  } catch (err) {
    console.error("Sign-up error:", err);
    return res.status(500).json({ message: "Server error" });
  }
});

module.exports = router;
