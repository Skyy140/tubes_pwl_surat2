// Get current user profile (for admin/user self profile)
exports.getProfile = async (req, res) => {
  try {
    const { id } = req.params;
    const user = await User.findOne({
      where: { idusers: id },
      attributes: ["idusers", "name", "email", "status"],
    });
    if (!user) return res.status(404).json({ message: "User not found" });
    res.json(user);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Update current user profile (for admin/user self profile)
exports.updateProfile = async (req, res) => {
  try {
    const { id } = req.params;
    const { name, password } = req.body;
    const user = await User.findOne({ where: { idusers: id } });
    if (!user) return res.status(404).json({ message: "User not found" });
    if (name) user.name = name;
    if (password && password.length > 0) {
      user.password = await bcrypt.hash(password, 10);
    }
    user.updated_at = new Date();
    await user.save();
    res.json({ message: "Profil berhasil diupdate", user });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};
// Nonaktifkan user keuangan (status jadi 'tidak')
exports.nonaktifkanKeuanganUser = async (req, res) => {
  try {
    const { id } = req.params;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 3 },
    });
    if (!user) return res.status(404).json({ message: "User not found" });
    user.status = "tidak aktif";
    user.updated_at = new Date();
    await user.save();
    res.json({ message: "User dinonaktifkan", user });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Nonaktifkan user panitia (status jadi 'tidak')
exports.nonaktifkanPanitiaUser = async (req, res) => {
  try {
    const { id } = req.params;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 4 },
    });
    if (!user) return res.status(404).json({ message: "User not found" });
    user.status = "tidak aktif";
    user.updated_at = new Date();
    await user.save();
    res.json({ message: "User dinonaktifkan", user });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};
// Get single keuangan user by id
exports.getKeuanganUserById = async (req, res) => {
  try {
    const { id } = req.params;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 3 },
      attributes: [
        "idusers",
        "name",
        "email",
        "status",
        "created_at",
        "updated_at",
      ],
    });
    if (!user) return res.status(404).json({ message: "User not found" });
    res.json(user);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Get single panitia user by id
exports.getPanitiaUserById = async (req, res) => {
  try {
    const { id } = req.params;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 4 },
      attributes: [
        "idusers",
        "name",
        "email",
        "status",
        "created_at",
        "updated_at",
      ],
    });
    if (!user) return res.status(404).json({ message: "User not found" });
    res.json(user);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Update keuangan user by id
exports.updateKeuanganUser = async (req, res) => {
  try {
    const { id } = req.params;
    const { name, email, password, status } = req.body;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 3 },
    });
    if (!user) return res.status(404).json({ message: "User not found" });

    // Cek jika email diubah dan sudah dipakai user lain
    if (email && email !== user.email) {
      const existingUser = await User.findOne({
        where: {
          email,
          idusers: { [require("sequelize").Op.ne]: user.idusers },
        },
      });
      if (existingUser) {
        return res.status(400).json({ message: "Email sudah terdaftar" });
      }
    }
    user.name = name || user.name;
    user.email = email || user.email;
    user.status = status || user.status;
    user.updated_at = new Date();
    if (password) {
      user.password = await bcrypt.hash(password, 10);
    }
    await user.save();
    res.json({ message: "User updated successfully", user });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Update panitia user by id
exports.updatePanitiaUser = async (req, res) => {
  try {
    const { id } = req.params;
    const { name, email, password, status } = req.body;
    const user = await User.findOne({
      where: { idusers: id, roles_idroles: 4 },
    });
    if (!user) return res.status(404).json({ message: "User not found" });

    // Cek jika email diubah dan sudah dipakai user lain
    if (email && email !== user.email) {
      const existingUser = await User.findOne({
        where: {
          email,
          idusers: { [require("sequelize").Op.ne]: user.idusers },
        },
      });
      if (existingUser) {
        return res.status(400).json({ message: "Email sudah terdaftar" });
      }
    }
    user.name = name || user.name;
    user.email = email || user.email;
    user.status = status || user.status;
    user.updated_at = new Date();
    if (password) {
      user.password = await bcrypt.hash(password, 10);
    }
    await user.save();
    res.json({ message: "User updated successfully", user });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};
const User = require("../models/user");

const bcrypt = require("bcrypt");

// Get all users with roles_idroles = 3
exports.getKeuanganUsers = async (req, res) => {
  try {
    const users = await User.findAll({
      where: { roles_idroles: 3 },
      attributes: [
        "idusers",
        "name",
        "email",
        "status",
        "created_at",
        "updated_at",
      ],
    });
    res.json(users);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

exports.getPanitiaUsers = async (req, res) => {
  try {
    const users = await User.findAll({
      where: { roles_idroles: 4 },
      attributes: [
        "idusers",
        "name",
        "email",
        "status",
        "created_at",
        "updated_at",
      ],
    });
    res.json(users);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Create user with role keuangan (roles_idroles = 3)
exports.createKeuanganUser = async (req, res) => {
  try {
    const { name, email, password } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ message: "All fields are required" });
    }

    // Check if email already exists
    const existingUser = await User.findOne({ where: { email } });
    if (existingUser) {
      return res.status(400).json({ message: "Email is already registered" });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);
    const now = new Date();

    const newUser = await User.create({
      name,
      email,
      password: hashedPassword,
      status: "aktif",
      created_at: now,
      updated_at: now,
      roles_idroles: 3,
    });

    res
      .status(201)
      .json({ message: "User created successfully", user: newUser });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

// Create user with role panitia (roles_idroles = 4)
exports.createPanitiaUser = async (req, res) => {
  try {
    const { name, email, password } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ message: "All fields are required" });
    }

    // Check if email already exists
    const existingUser = await User.findOne({ where: { email } });
    if (existingUser) {
      return res.status(400).json({ message: "Email is already registered" });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);
    const now = new Date();

    const newUser = await User.create({
      name,
      email,
      password: hashedPassword,
      status: "aktif",
      created_at: now,
      updated_at: now,
      roles_idroles: 4,
    });

    res
      .status(201)
      .json({ message: "User created successfully", user: newUser });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};
