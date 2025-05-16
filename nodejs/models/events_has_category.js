const Event = require("./event");
const Category = require("./category");

Event.belongsToMany(Category, {
  through: "events_has_category",
  foreignKey: "events_idevents",
  otherKey: "category_idcategory",
  as: "categories",
});

Category.belongsToMany(Event, {
  through: "events_has_category",
  foreignKey: "category_idcategory",
  otherKey: "events_idevents",
  as: "events",
});

module.exports = {
  Event,
  Category,
};