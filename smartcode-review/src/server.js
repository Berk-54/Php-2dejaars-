import express from "express";
import path from "path";
import reviewRoutes from "./routes/reviewRoutes.js";

const app = express();
app.set("view engine", "ejs");
app.set("views", path.resolve("src/views"));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use("/public", express.static(path.resolve("src/public")));

app.use("/", reviewRoutes);

app.listen(3000, () => console.log("Running on http://localhost:3000"));
