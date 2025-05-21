const express = require("express");
const router = express.Router();
const userController = require("../controllers/userController");

// Route to get users with roles_idroles = 3 (Keuangan)

// Route to get users with roles_idroles = 3 (Keuangan)

// Get all keuangan users
router.get("/keuangan", userController.getKeuanganUsers);
// Get all panitia users
router.get("/panitia", userController.getPanitiaUsers);

// Get single keuangan user by id
router.get("/keuangan/:id", userController.getKeuanganUserById);
// Get single panitia user by id
router.get("/panitia/:id", userController.getPanitiaUserById);

// Create keuangan user
router.post("/keuangan", userController.createKeuanganUser);
// Create panitia user
router.post("/panitia", userController.createPanitiaUser);

// Update keuangan user by id
router.put("/keuangan/:id", userController.updateKeuanganUser);
// Update panitia user by id
router.put("/panitia/:id", userController.updatePanitiaUser);

// Nonaktifkan user keuangan (status jadi 'tidak')
router.patch(
  "/keuangan/nonaktifkan/:id",
  userController.nonaktifkanKeuanganUser
);
// Nonaktifkan user panitia (status jadi 'tidak')
router.patch("/panitia/nonaktifkan/:id", userController.nonaktifkanPanitiaUser);

// Dashboard: Get count of users by role (for frontend chart)
router.get("/role-counts", userController.getUserRoleCounts);

module.exports = router;

// Profile routes (for admin/user self profile)
const profileController = require("../controllers/userController");
// Get current user profile (dummy: user id from query or header, in real app use auth)
router.get("/profile/:id", profileController.getProfile);
// Update current user profile
router.put("/profile/:id", profileController.updateProfile);
