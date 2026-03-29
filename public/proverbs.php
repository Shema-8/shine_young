<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php?error=207");
    exit();
}
try {
    $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $proverbs = $db->query("SELECT * FROM proverbs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
} catch(Exception $e) { $proverbs = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proverbs - SHINE YOUNG</title>
<link rel="stylesheet" href="style.css">
<style>
.prov-hero{text-align:center;padding:50px 40px;background:linear-gradient(135deg,#1e2a38,#2d4058);color:white;}
.prov-hero h1{font-family:'Playfair Display',serif;font-size:2.2rem;margin-bottom:12px;}
.prov-hero p{color:rgba(255,255,255,0.65);}
.proverbs-list{max-width:760px;margin:40px auto;padding:0 24px;}
.proverb{background:white;padding:24px 28px;margin:18px 0;border-left:5px solid #f4c542;border-radius:0 12px 12px 0;box-shadow:0 3px 12px rgba(0,0,0,0.06);transition:.25s;}
.proverb:hover{transform:translateX(4px);box-shadow:0 6px 20px rgba(0,0,0,0.1);}
.prov-kiny{font-family:'Playfair Display',serif;font-style:italic;font-size:1.15rem;color:#1e2a38;margin-bottom:8px;}
.prov-eng{color:#c9a020;font-weight:700;font-size:14px;margin-bottom:6px;}
.prov-expl{color:#718096;font-size:13px;line-height:1.65;border-top:1px solid #f0ede8;padding-top:10px;margin-top:10px;}
.explore-link{display:block;text-align:center;margin:30px auto;width:fit-content;padding:12px 24px;background:#1e2a38;color:white;border-radius:8px;text-decoration:none;font-weight:600;}
.empty-msg{text-align:center;padding:60px 20px;color:#718096;}
.empty-msg div{font-size:48px;margin-bottom:12px;}
</style>
</head>
<body>
<nav>
  <h1><li><a href="index.html">SHINE YOUNG</a></li></h1>
  <ul>
    <li><a href="index.html">Home</a></li>
    <li><a href="stories.php">Stories</a></li>
    <li><a href="proverbs.php">Proverbs</a></li>
    <li><a href="media.php">Media</a></li>
    <li><a href="school-culture.html">School Culture</a></li>
    <div id="logout"><a href="http://shineyoung.rf.gd">Logout</a></div>
  </ul>
</nav>

<div class="prov-hero">
  <h1>🪶 Rwandan Proverbs</h1>
  <p>Wisdom passed down through generations.</p>
</div>

<section class="proverbs-list">
<?php if (empty($proverbs)): ?>
  <div class="empty-msg">
    <div>🪶</div>
    <p>No proverbs added yet. Check back soon!</p>
  </div>
<?php else: ?>
  <?php foreach ($proverbs as $p): ?>
  <div class="proverb">
    <div class="prov-kiny">"<?= htmlspecialchars($p->kinyarwanda) ?>"</div>
    <div class="prov-eng">📌 <?= htmlspecialchars($p->english) ?></div>
    <?php if (!empty($p->explanation)): ?>
      <div class="prov-expl"><?= htmlspecialchars($p->explanation) ?></div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
  <a class="explore-link" href="https://jeanpaulmartinon.net/rap/rwanda/rwanda-proverbs/" target="_blank">🌍 Explore More Proverbs</a>
</section>
</body>
</html>
