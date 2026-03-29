<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php?error=207");
    exit();
}
try {
    $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stories = $db->query("SELECT * FROM stories ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
} catch(Exception $e) { $stories = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stories - SHINE YOUNG</title>
<link rel="stylesheet" href="style.css">
<style>
.story-hero{text-align:center;padding:50px 40px;background:linear-gradient(135deg,#1e2a38,#2d4058);color:white;}
.story-hero h1{font-family:'Playfair Display',serif;font-size:2.2rem;margin-bottom:12px;}
.story-hero p{color:rgba(255,255,255,0.65);}
.story-list{max-width:900px;margin:40px auto;padding:0 24px;}
.story-item{background:white;padding:24px;margin:18px 0;border-radius:14px;box-shadow:0 4px 14px rgba(0,0,0,0.07);display:flex;gap:20px;align-items:flex-start;transition:.25s;}
.story-item:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.1);}
.story-icon{width:80px;height:80px;background:#f0ede8;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:32px;flex-shrink:0;}
.story-img{width:80px;height:80px;object-fit:cover;border-radius:10px;flex-shrink:0;}
.story-body{flex:1;}
.story-body h2{font-family:'Playfair Display',serif;font-size:1.15rem;color:#1e2a38;margin-bottom:8px;}
.story-body p{color:#718096;font-size:14px;line-height:1.6;}
.read-btn{display:inline-block;margin-top:12px;padding:9px 20px;background:#f4c542;color:#1e2a38;text-decoration:none;border-radius:8px;font-weight:700;font-size:13px;}
.read-btn:hover{background:#e0b123;}
.bloom-link{display:block;text-align:center;margin:30px auto;width:fit-content;padding:12px 24px;background:#1e2a38;color:white;border-radius:8px;text-decoration:none;font-weight:600;}
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

<div class="story-hero">
  <h1>📖 Rwandan Stories</h1>
  <p>Explore wisdom and culture through traditional storytelling.</p>
</div>

<section class="story-list">
<?php if (empty($stories)): ?>
  <div class="empty-msg">
    <div>📚</div>
    <p>No stories added yet. Check back soon!</p>
  </div>
<?php else: ?>
  <?php foreach ($stories as $s): ?>
  <div class="story-item">
    <?php if (!empty($s->image)): ?>
      <img src="../uploads/stories/<?= htmlspecialchars($s->image) ?>" class="story-img" alt="">
    <?php else: ?>
      <div class="story-icon">📖</div>
    <?php endif; ?>
    <div class="story-body">
      <h2><?= htmlspecialchars($s->title) ?></h2>
      <p><?= htmlspecialchars($s->description) ?></p>
      <a class="read-btn" href="<?= htmlspecialchars($s->link) ?>" target="_blank">Read Story →</a>
    </div>
  </div>
  <?php endforeach; ?>
  <a class="bloom-link" href="https://bloomlibrary.org/language:rw" target="_blank">🌍 Explore More on Bloom Library</a>
<?php endif; ?>
</section>
</body>
</html>
