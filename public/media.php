<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php?error=207");
    exit();
}
try {
    $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mediaList = $db->query("SELECT * FROM media ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
} catch(Exception $e) { $mediaList = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Media - SHINE YOUNG</title>
<link rel="stylesheet" href="style.css">
<style>
.media-hero{text-align:center;padding:50px 40px;background:linear-gradient(135deg,#1e2a38,#2d4058);color:white;}
.media-hero h1{font-family:'Playfair Display',serif;font-size:2.2rem;margin-bottom:12px;}
.media-hero p{color:rgba(255,255,255,0.65);}
.media-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:1100px;margin:40px auto;padding:0 24px;}
.media-card{background:white;border-radius:14px;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,0.08);transition:.25s;}
.media-card:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(0,0,0,0.12);}
.media-card iframe{width:100%;height:200px;border:none;display:block;}
.media-card-body{padding:16px 18px;}
.media-card-body h3{font-family:'Playfair Display',serif;font-size:1rem;color:#1e2a38;margin-bottom:6px;}
.media-card-body p{color:#718096;font-size:13px;line-height:1.55;}
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

<div class="media-hero">
  <h1>🎬 Music & Dance</h1>
  <p>Watch and experience Rwanda's rich cultural performances.</p>
</div>

<?php if (empty($mediaList)): ?>
  <div class="empty-msg" style="margin:60px auto;">
    <div>🎬</div>
    <p>No videos added yet. Check back soon!</p>
  </div>
<?php else: ?>
  <div class="media-grid">
    <?php foreach ($mediaList as $mv): ?>
    <div class="media-card">
      <iframe src="<?= htmlspecialchars($mv->youtube_url) ?>" allowfullscreen></iframe>
      <div class="media-card-body">
        <h3><?= htmlspecialchars($mv->title) ?></h3>
        <p><?= htmlspecialchars($mv->description) ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</body>
</html>
