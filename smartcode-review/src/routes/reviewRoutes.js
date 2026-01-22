import express from "express";
import multer from "multer";

const router = express.Router();
const upload = multer({ dest: "uploads/" });

router.get("/", (req, res) => {
  res.render("index", { error: null });
});

router.post("/review", upload.single("codefile"), (req, res) => {
  res.send("Upload ontvangen – AI analyse placeholder");
});

export default router;
