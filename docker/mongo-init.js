db = db.getSiblingDB('admin');

db.createUser({
  user: "user",
  pwd: "MaudePasse",
  roles: [{ role: "root", db: "admin" }]
});
