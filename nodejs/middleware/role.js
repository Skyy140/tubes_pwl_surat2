function role(allowedRoles = []) {
  return (req, res, next) => {
    const user = req.user;

    if (!user) {
      return res.status(401).json({ message: "Belum login" });
    }

    if (!allowedRoles.includes(user.role)) {
        return res.status(403).json({ message: "Akses ditolak: role tidak diizinkan" });
    }

    next();
  };
}

module.exports = role;
