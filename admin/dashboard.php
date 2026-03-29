<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php?error=207");
    exit();
}

try {
    $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Auto-create tables if missing (safety net)
    $db->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(150) NOT NULL,
        `subject` varchar(200) NOT NULL,
        `message` text NOT NULL,
        `status` enum('unread','read') NOT NULL DEFAULT 'unread',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS `stories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(200) NOT NULL,
        `description` text NOT NULL,
        `link` varchar(500) NOT NULL,
        `image` varchar(300) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS `proverbs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `kinyarwanda` varchar(300) NOT NULL,
        `english` varchar(300) NOT NULL,
        `explanation` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS `media` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(200) NOT NULL,
        `description` text NOT NULL,
        `youtube_url` varchar(500) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

} catch (Exception $e) {
    die("<div style='font:16px sans-serif;padding:40px;color:#c00'>Database connection failed: " . $e->getMessage() . "<br><br>Make sure MySQL is running and the database <b>shine_young</b> exists.</div>");
}

// ─── Determine active section ───────────────────────────────────────────────
$section = $_GET['section'] ?? 'messages';
$allowed = ['messages','stories','proverbs','media'];
if (!in_array($section, $allowed)) $section = 'messages';

$success_msg = '';
$error_msg   = '';

// ─── MESSAGES actions ───────────────────────────────────────────────────────
if ($section === 'messages' && isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action === 'read')   $db->prepare("UPDATE contact_messages SET status='read'   WHERE id=:id")->execute([':id'=>$id]);
    if ($action === 'unread') $db->prepare("UPDATE contact_messages SET status='unread' WHERE id=:id")->execute([':id'=>$id]);
    if ($action === 'delete') $db->prepare("DELETE FROM contact_messages WHERE id=:id")->execute([':id'=>$id]);
    header("Location: dashboard.php?section=messages&filter=" . ($_GET['filter'] ?? 'all'));
    exit();
}

// ─── STORIES actions ────────────────────────────────────────────────────────
if ($section === 'stories') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_action'])) {
        $action = $_POST['story_action'];
        if ($action === 'add') {
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            $link  = trim($_POST['link'] ?? '');
            if ($title && $desc && $link) {
                // Handle image upload
                $imgPath = null;
                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                        $fname = 'story_' . time() . '.' . $ext;
                        $dest  = '../uploads/stories/' . $fname;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                            $imgPath = $fname;
                        }
                    }
                }
                $db->prepare("INSERT INTO stories (title,description,link,image) VALUES(:t,:d,:l,:i)")
                   ->execute([':t'=>$title,':d'=>$desc,':l'=>$link,':i'=>$imgPath]);
                $success_msg = 'Story added successfully!';
            } else { $error_msg = 'Please fill in all required fields.'; }
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $db->prepare("DELETE FROM stories WHERE id=:id")->execute([':id'=>$id]);
            $success_msg = 'Story deleted.';
        }
        if ($action === 'edit') {
            $id    = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            $link  = trim($_POST['link'] ?? '');
            if ($id && $title && $desc && $link) {
                // Handle image upload on edit
                $imgPath = trim($_POST['existing_image'] ?? '');
                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                        $fname = 'story_' . time() . '.' . $ext;
                        $dest  = '../uploads/stories/' . $fname;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                            $imgPath = $fname;
                        }
                    }
                }
                $db->prepare("UPDATE stories SET title=:t,description=:d,link=:l,image=:i WHERE id=:id")
                   ->execute([':t'=>$title,':d'=>$desc,':l'=>$link,':i'=>$imgPath ?: null,':id'=>$id]);
                $success_msg = 'Story updated!';
            }
        }
    }
}

// ─── PROVERBS actions ───────────────────────────────────────────────────────
if ($section === 'proverbs') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proverb_action'])) {
        $action = $_POST['proverb_action'];
        if ($action === 'add') {
            $kin  = trim($_POST['kinyarwanda'] ?? '');
            $eng  = trim($_POST['english'] ?? '');
            $expl = trim($_POST['explanation'] ?? '');
            if ($kin && $eng) {
                $db->prepare("INSERT INTO proverbs (kinyarwanda,english,explanation) VALUES(:k,:e,:x)")
                   ->execute([':k'=>$kin,':e'=>$eng,':x'=>$expl ?: null]);
                $success_msg = 'Proverb added!';
            } else { $error_msg = 'Kinyarwanda and English translation are required.'; }
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $db->prepare("DELETE FROM proverbs WHERE id=:id")->execute([':id'=>$id]);
            $success_msg = 'Proverb deleted.';
        }
        if ($action === 'edit') {
            $id   = (int)($_POST['id'] ?? 0);
            $kin  = trim($_POST['kinyarwanda'] ?? '');
            $eng  = trim($_POST['english'] ?? '');
            $expl = trim($_POST['explanation'] ?? '');
            if ($id && $kin && $eng) {
                $db->prepare("UPDATE proverbs SET kinyarwanda=:k,english=:e,explanation=:x WHERE id=:id")
                   ->execute([':k'=>$kin,':e'=>$eng,':x'=>$expl ?: null,':id'=>$id]);
                $success_msg = 'Proverb updated!';
            }
        }
    }
}

// ─── MEDIA actions ──────────────────────────────────────────────────────────
if ($section === 'media') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['media_action'])) {
        $action = $_POST['media_action'];
        // Convert full YouTube URL to embed URL
        function toEmbed($url) {
            $url = trim($url);
            if (strpos($url, 'youtube.com/embed/') !== false) return $url;
            preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $m);
            if (!empty($m[1])) return 'https://www.youtube.com/embed/' . $m[1];
            return $url;
        }
        if ($action === 'add') {
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            $url   = toEmbed($_POST['youtube_url'] ?? '');
            if ($title && $desc && $url) {
                $db->prepare("INSERT INTO media (title,description,youtube_url) VALUES(:t,:d,:u)")
                   ->execute([':t'=>$title,':d'=>$desc,':u'=>$url]);
                $success_msg = 'Video added!';
            } else { $error_msg = 'Please fill in all required fields.'; }
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $db->prepare("DELETE FROM media WHERE id=:id")->execute([':id'=>$id]);
            $success_msg = 'Video deleted.';
        }
        if ($action === 'edit') {
            $id    = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            $url   = toEmbed($_POST['youtube_url'] ?? '');
            if ($id && $title && $desc && $url) {
                $db->prepare("UPDATE media SET title=:t,description=:d,youtube_url=:u WHERE id=:id")
                   ->execute([':t'=>$title,':d'=>$desc,':u'=>$url,':id'=>$id]);
                $success_msg = 'Video updated!';
            }
        }
    }
}

// ─── Fetch data for current section ─────────────────────────────────────────
// Messages
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$conditions = [];
$params = [];
if ($filter === 'unread') $conditions[] = "status='unread'";
if ($filter === 'read')   $conditions[] = "status='read'";
if ($search !== '') { $conditions[] = "(name LIKE :s OR email LIKE :s OR subject LIKE :s)"; $params[':s'] = "%$search%"; }
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total  = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unread = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
$read   = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE status='read'")->fetchColumn();
$today  = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at)=CURDATE()")->fetchColumn();

$stmt = $db->prepare("SELECT * FROM contact_messages $where ORDER BY created_at DESC");
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_OBJ);

$viewMsg = null;
if (isset($_GET['view']) && $section === 'messages') {
    $vid = (int)$_GET['view'];
    $vstmt = $db->prepare("SELECT * FROM contact_messages WHERE id=:id");
    $vstmt->execute([':id'=>$vid]);
    $viewMsg = $vstmt->fetch(PDO::FETCH_OBJ);
    if ($viewMsg && $viewMsg->status === 'unread') {
        $db->prepare("UPDATE contact_messages SET status='read' WHERE id=:id")->execute([':id'=>$vid]);
        $viewMsg->status = 'read';
        $unread = max(0, $unread - 1);
    }
}

// Stories
$stories = $db->query("SELECT * FROM stories ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
$editStory = null;
if (isset($_GET['edit_story'])) {
    $es = $db->prepare("SELECT * FROM stories WHERE id=:id");
    $es->execute([':id'=>(int)$_GET['edit_story']]);
    $editStory = $es->fetch(PDO::FETCH_OBJ);
}

// Proverbs
$proverbs = $db->query("SELECT * FROM proverbs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
$editProverb = null;
if (isset($_GET['edit_proverb'])) {
    $ep = $db->prepare("SELECT * FROM proverbs WHERE id=:id");
    $ep->execute([':id'=>(int)$_GET['edit_proverb']]);
    $editProverb = $ep->fetch(PDO::FETCH_OBJ);
}

// Media
$mediaList = $db->query("SELECT * FROM media ORDER BY created_at DESC")->fetchAll(PDO::FETCH_OBJ);
$editMedia = null;
if (isset($_GET['edit_media'])) {
    $em = $db->prepare("SELECT * FROM media WHERE id=:id");
    $em->execute([':id'=>(int)$_GET['edit_media']]);
    $editMedia = $em->fetch(PDO::FETCH_OBJ);
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$initial  = strtoupper(substr($username, 0, 1));

// Section labels
$sectionLabels = [
    'messages' => 'Messages Inbox',
    'stories'  => 'Manage Stories',
    'proverbs' => 'Manage Proverbs',
    'media'    => 'Manage Media',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin – SHINE YOUNG</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --gold:#f4c542;--gold-dark:#c9a020;
  --dark:#1e2a38;--dark2:#243347;
  --bg:#f0ede8;--white:#fff;
  --text:#1A202C;--muted:#718096;--border:#E2E8F0;
  --sb-w:230px;
  --success:#10b981;--danger:#ef4444;--info:#3b82f6;
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;}

/* ── SIDEBAR ─────────────────────────────────────── */
.sidebar{width:var(--sb-w);background:var(--dark);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:50;border-right:1px solid rgba(244,197,66,0.1);}
.sb-logo{padding:22px 18px 16px;border-bottom:1px solid rgba(244,197,66,0.1);display:flex;align-items:center;gap:10px;}
.sb-gem{width:36px;height:36px;background:linear-gradient(135deg,var(--gold-dark),var(--gold));border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:var(--dark);flex-shrink:0;}
.sb-name{font-family:'Playfair Display',serif;font-size:15px;color:#fff;font-weight:700;line-height:1.2;}
.sb-name span{color:var(--gold);}
.sb-sub{font-size:9px;color:rgba(255,255,255,0.28);text-transform:uppercase;letter-spacing:.6px;margin-top:2px;}
.sb-nav{flex:1;padding:14px 10px;display:flex;flex-direction:column;gap:2px;overflow-y:auto;}
.sb-lbl{font-size:9px;font-weight:700;color:rgba(255,255,255,0.22);letter-spacing:1px;text-transform:uppercase;padding:14px 10px 5px;}
.sb-link{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;text-decoration:none;color:rgba(255,255,255,0.5);font-size:13px;font-weight:500;transition:.18s;position:relative;}
.sb-link:hover{background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.82);}
.sb-link.active{background:rgba(244,197,66,0.13);color:var(--gold);}
.sb-link.active::before{content:'';position:absolute;left:0;top:18%;bottom:18%;width:3px;background:var(--gold);border-radius:0 3px 3px 0;}
.sb-badge{margin-left:auto;background:var(--gold);color:var(--dark);font-size:9px;font-weight:700;padding:2px 7px;border-radius:100px;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.7;}}
.sb-divider{height:1px;background:rgba(255,255,255,0.06);margin:8px 10px;}
.sb-foot{padding:14px 10px;border-top:1px solid rgba(255,255,255,0.06);}
.sb-user{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:8px;}
.sb-avatar{width:34px;height:34px;background:linear-gradient(135deg,var(--gold-dark),var(--gold));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--dark);flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:rgba(255,255,255,0.82);}
.sb-urole{font-size:10px;color:rgba(255,255,255,0.3);}
.sb-logout{display:flex;align-items:center;gap:9px;padding:8px 12px;border-radius:7px;text-decoration:none;color:rgba(255,255,255,0.3);font-size:12px;margin-top:3px;transition:.18s;}
.sb-logout:hover{color:#fc8181;background:rgba(252,129,129,0.08);}

/* ── MAIN ────────────────────────────────────────── */
.main{margin-left:var(--sb-w);flex:1;display:flex;flex-direction:column;min-width:0;}
.topbar{background:var(--white);border-bottom:1px solid var(--border);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40;box-shadow:0 1px 4px rgba(0,0,0,0.05);}
.topbar-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);}
.topbar-right{display:flex;align-items:center;gap:12px;}
.topbar-time{font-size:12px;color:var(--muted);}
.topbar-back{font-size:12px;color:var(--gold-dark);text-decoration:none;font-weight:600;border:1px solid rgba(201,160,32,.35);padding:7px 14px;border-radius:7px;transition:.2s;}
.topbar-back:hover{background:rgba(244,197,66,.08);}
.content{padding:26px 28px;flex:1;}

/* ── ALERTS ──────────────────────────────────────── */
.alert{display:flex;align-items:center;gap:10px;padding:13px 18px;border-radius:9px;font-size:13px;font-weight:500;margin-bottom:20px;}
.alert-ok {background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;}
.alert-err{background:#fff5f5;border:1px solid #fed7d7;color:#991b1b;}

/* ── STATS ───────────────────────────────────────── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}
.scard{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:18px 20px;display:flex;align-items:center;gap:14px;}
.scard-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.ic-gold{background:rgba(244,197,66,.12);}
.ic-blue{background:rgba(59,130,246,.1);}
.ic-green{background:rgba(16,185,129,.1);}
.ic-purple{background:rgba(139,92,246,.1);}
.scard-val{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--text);line-height:1;}
.scard-lbl{font-size:11px;color:var(--muted);margin-top:4px;font-weight:500;}

/* ── TOOLBAR ─────────────────────────────────────── */
.toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
.filter-tabs{display:flex;gap:5px;}
.ftab{padding:7px 16px;border-radius:7px;font-size:12px;font-weight:600;border:1.5px solid var(--border);background:var(--white);color:var(--muted);text-decoration:none;transition:.18s;}
.ftab:hover{border-color:var(--gold);color:var(--gold-dark);}
.ftab.active{background:var(--dark);color:var(--gold);border-color:var(--dark);}
.search-wrap{display:flex;gap:8px;}
.search-wrap input{padding:8px 14px;border:1.5px solid var(--border);border-radius:7px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:var(--white);outline:none;width:210px;transition:.2s;}
.search-wrap input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(244,197,66,.1);}
.search-btn{padding:8px 16px;background:var(--dark);color:var(--gold);border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;}
.clear-btn{padding:8px 12px;border-radius:7px;font-size:12px;font-weight:600;color:#991b1b;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);text-decoration:none;}

/* ── TABLE ───────────────────────────────────────── */
.table-wrap{background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead{background:#f8f6f0;}
thead th{padding:12px 18px;text-align:left;font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;border-bottom:1px solid var(--border);white-space:nowrap;}
tbody tr{border-bottom:1px solid var(--border);transition:.15s;cursor:pointer;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:#fafaf8;}
tbody tr.is-unread{background:#fffbeb;}
tbody tr.is-unread:hover{background:#fef9e7;}
td{padding:13px 18px;font-size:13px;vertical-align:middle;}
.td-name{font-weight:600;color:var(--text);}
.td-email{color:var(--muted);font-size:11px;margin-top:2px;}
.td-subj{font-weight:500;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;}
.td-date{color:var(--muted);font-size:11px;white-space:nowrap;}
.badge-unread{display:inline-flex;align-items:center;gap:5px;background:#fffbeb;border:1px solid #fcd34d;color:#92400e;font-size:10px;font-weight:700;padding:3px 9px;border-radius:100px;white-space:nowrap;}
.badge-read{display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:10px;font-weight:600;padding:3px 9px;border-radius:100px;white-space:nowrap;}
.badge-unread::before,.badge-read::before{content:'';width:6px;height:6px;border-radius:50%;}
.badge-unread::before{background:#f59e0b;}
.badge-read::before{background:#22c55e;}
.td-actions{display:flex;gap:6px;flex-wrap:wrap;}
.ab{padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid transparent;cursor:pointer;transition:.15s;background:none;font-family:inherit;}
.ab-view  {background:rgba(244,197,66,.1);color:#b8960c;border-color:rgba(244,197,66,.3);}
.ab-read  {background:rgba(16,185,129,.08);color:#065f46;border-color:rgba(16,185,129,.2);}
.ab-unread{background:rgba(245,158,11,.08);color:#92400e;border-color:rgba(245,158,11,.2);}
.ab-del   {background:rgba(239,68,68,.06);color:#991b1b;border-color:rgba(239,68,68,.15);}
.ab-edit  {background:rgba(59,130,246,.08);color:#1e40af;border-color:rgba(59,130,246,.2);}
.ab:hover{opacity:.8;transform:translateY(-1px);}
.empty-state{padding:60px 24px;text-align:center;}
.empty-state p{color:var(--muted);font-size:15px;margin-top:12px;}
.table-footer{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#fafaf8;border-top:1px solid var(--border);font-size:12px;color:var(--muted);}

/* ── CONTENT MANAGEMENT ──────────────────────────── */
.cm-grid{display:grid;grid-template-columns:400px 1fr;gap:22px;align-items:start;}
.cm-card{background:var(--white);border:1px solid var(--border);border-radius:14px;overflow:hidden;}
.cm-card-head{background:var(--dark);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;}
.cm-card-head h3{font-family:'Playfair Display',serif;font-size:16px;color:#fff;font-weight:700;}
.cm-card-body{padding:22px;}
.cm-count{background:var(--gold);color:var(--dark);font-size:11px;font-weight:700;padding:3px 9px;border-radius:100px;}
.field{margin-bottom:16px;}
.field label{display:block;font-size:11px;font-weight:700;color:var(--text);margin-bottom:7px;text-transform:uppercase;letter-spacing:.4px;}
.field input,.field textarea,.field select{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:#fafafa;outline:none;transition:.2s;}
.field input:focus,.field textarea:focus{border-color:var(--gold);background:#fff;box-shadow:0 0 0 3px rgba(244,197,66,.1);}
.field textarea{resize:vertical;min-height:90px;}
.field input[type="file"]{padding:8px 14px;cursor:pointer;}
.field small{display:block;font-size:11px;color:var(--muted);margin-top:5px;}
.btn-add{width:100%;padding:12px;background:linear-gradient(135deg,var(--gold-dark),var(--gold));color:var(--dark);border:none;border-radius:9px;font-family:'Playfair Display',serif;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;letter-spacing:.3px;}
.btn-add:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(244,197,66,.35);}
.btn-cancel{display:inline-block;margin-top:10px;text-align:center;width:100%;padding:10px;border:1.5px solid var(--border);background:#fff;color:var(--muted);border-radius:9px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;}
.btn-cancel:hover{border-color:var(--gold);color:var(--gold-dark);}

/* content list */
.content-list{display:flex;flex-direction:column;gap:12px;}
.content-item{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:16px 18px;display:flex;align-items:flex-start;gap:14px;transition:.18s;}
.content-item:hover{box-shadow:0 4px 16px rgba(0,0,0,0.07);}
.ci-num{width:32px;height:32px;background:rgba(244,197,66,.12);border:1px solid rgba(244,197,66,.25);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--gold-dark);flex-shrink:0;}
.ci-body{flex:1;min-width:0;}
.ci-title{font-weight:700;color:var(--text);font-size:14px;margin-bottom:3px;}
.ci-desc{font-size:12px;color:var(--muted);line-height:1.5;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
.ci-link{font-size:11px;color:var(--gold-dark);margin-top:4px;display:block;word-break:break-all;}
.ci-foot{display:flex;gap:6px;margin-top:10px;}
.ci-img{width:64px;height:48px;object-fit:cover;border-radius:6px;flex-shrink:0;}

/* ── MODAL ───────────────────────────────────────── */
.modal-backdrop{position:fixed;inset:0;background:rgba(30,42,56,.65);z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;}
.modal{background:var(--white);border-radius:16px;width:100%;max-width:600px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.25);}
.modal-head{background:var(--dark);padding:22px 26px;display:flex;align-items:flex-start;justify-content:space-between;}
.modal-head h3{font-family:'Playfair Display',serif;font-size:18px;color:#fff;font-weight:700;margin-bottom:4px;}
.modal-meta{font-size:12px;color:rgba(255,255,255,.4);}
.modal-meta span{color:var(--gold);}
.modal-close{background:rgba(255,255,255,.1);border:none;color:#fff;width:30px;height:30px;border-radius:7px;cursor:pointer;font-size:15px;transition:.2s;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.modal-close:hover{background:rgba(255,255,255,.2);}
.modal-info{display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--border);}
.modal-detail{padding:14px 26px;}
.modal-detail:first-child{border-right:1px solid var(--border);}
.modal-detail label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px;}
.modal-detail span{font-size:14px;color:var(--text);font-weight:500;}
.modal-body{padding:22px 26px;}
.modal-body label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:10px;}
.modal-body p{font-size:14px;color:var(--text);line-height:1.8;white-space:pre-wrap;}
.modal-foot{padding:16px 26px;background:#fafaf8;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end;}

/* ── PROVERB CARD ─────────────────────────────────── */
.prov-kiny{font-style:italic;font-weight:700;color:var(--dark);font-size:14px;margin-bottom:3px;}
.prov-eng{font-size:13px;color:var(--gold-dark);font-weight:600;}
.prov-expl{font-size:12px;color:var(--muted);margin-top:4px;line-height:1.5;}

/* ── VIDEO THUMB ──────────────────────────────────── */
.vid-thumb{width:100px;height:60px;border-radius:7px;overflow:hidden;flex-shrink:0;background:#1e2a38;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.3);font-size:22px;}
.vid-url{font-size:11px;color:var(--muted);margin-top:3px;word-break:break-all;}

/* ── LIVE BADGE ───────────────────────────────────── */
.live-dot{display:inline-flex;align-items:center;gap:5px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#065f46;font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;}
.live-dot::before{content:'';width:7px;height:7px;border-radius:50%;background:#10b981;animation:livepulse 1.2s infinite;}
@keyframes livepulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.5;transform:scale(.8);}}
</style>
</head>
<body>

<!-- ═══ SIDEBAR ═══════════════════════════════════════════════════════════ -->
<aside class="sidebar">
    <div class="sb-logo">
        <div class="sb-gem">✦</div>
        <div>
            <div class="sb-name">SHINE <span>YOUNG</span></div>
            <div class="sb-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-lbl">Overview</div>
        <a href="dashboard.php?section=messages" class="sb-link <?= $section==='messages'?'active':'' ?>">
            📊 Messages Inbox
            <?php if ($unread > 0): ?>
                <span class="sb-badge"><?= $unread ?></span>
            <?php endif; ?>
        </a>

        <div class="sb-lbl">Content Management</div>
        <a href="dashboard.php?section=stories"  class="sb-link <?= $section==='stories' ?'active':'' ?>">📖 Stories <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3)"><?= count($stories) ?></span></a>
        <a href="dashboard.php?section=proverbs" class="sb-link <?= $section==='proverbs'?'active':'' ?>">🪶 Proverbs <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3)"><?= count($proverbs) ?></span></a>
        <a href="dashboard.php?section=media"    class="sb-link <?= $section==='media'   ?'active':'' ?>">🎬 Media <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3)"><?= count($mediaList) ?></span></a>

        <div class="sb-divider"></div>
        <div class="sb-lbl">View Site</div>
        <a href="../public/index.html"          class="sb-link" target="_blank">🏠 Homepage ↗</a>
        <a href="../public/stories.html"        class="sb-link" target="_blank">📖 Stories ↗</a>
        <a href="../public/proverbs.html"       class="sb-link" target="_blank">🪶 Proverbs ↗</a>
        <a href="../public/media.html"          class="sb-link" target="_blank">🎬 Media ↗</a>
        <a href="../public/school-culture.html" class="sb-link" target="_blank">🏫 School Culture ↗</a>
    </nav>

    <div class="sb-foot">
        <div class="sb-user">
            <div class="sb-avatar"><?= $initial ?></div>
            <div>
                <div class="sb-uname"><?= $username ?></div>
                <div class="sb-urole">Administrator</div>
            </div>
        </div>
        <a href="../index.php" class="sb-logout">⎋ Sign Out</a>
    </div>
</aside>

<!-- ═══ MAIN ══════════════════════════════════════════════════════════════ -->
<div class="main">

    <header class="topbar">
        <div class="topbar-title"><?= $sectionLabels[$section] ?></div>
        <div class="topbar-right">
            <?php if ($section === 'messages'): ?>
            <span class="live-dot">Live Updates</span>
            <?php endif; ?>
            <span class="topbar-time" id="clk"></span>
            <a href="../public/index.html" class="topbar-back" target="_blank">← View Site</a>
        </div>
    </header>

    <div class="content">

        <?php if ($success_msg): ?>
        <div class="alert alert-ok">✅ <?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
        <div class="alert alert-err">⚠️ <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php /* ══════════════ MESSAGES SECTION ══════════════ */ ?>
        <?php if ($section === 'messages'): ?>

        <div class="stats-grid">
            <div class="scard"><div class="scard-icon ic-gold">📨</div><div><div class="scard-val"><?= $total ?></div><div class="scard-lbl">Total Messages</div></div></div>
            <div class="scard"><div class="scard-icon ic-blue">🔔</div><div><div class="scard-val"><?= $unread ?></div><div class="scard-lbl">Unread</div></div></div>
            <div class="scard"><div class="scard-icon ic-green">✅</div><div><div class="scard-val"><?= $read ?></div><div class="scard-lbl">Read</div></div></div>
            <div class="scard"><div class="scard-icon ic-purple">📅</div><div><div class="scard-val"><?= $today ?></div><div class="scard-lbl">Today</div></div></div>
        </div>

        <div class="toolbar">
            <div class="filter-tabs">
                <a href="?section=messages&filter=all"    class="ftab <?= $filter==='all'?'active':'' ?>">All (<?= $total ?>)</a>
                <a href="?section=messages&filter=unread" class="ftab <?= $filter==='unread'?'active':'' ?>">Unread (<?= $unread ?>)</a>
                <a href="?section=messages&filter=read"   class="ftab <?= $filter==='read'?'active':'' ?>">Read (<?= $read ?>)</a>
            </div>
            <form class="search-wrap" method="GET">
                <input type="hidden" name="section" value="messages">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <input type="text" name="search" placeholder="Search by name, email or subject…" value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn">Search</button>
                <?php if ($search): ?><a href="?section=messages&filter=<?= $filter ?>" class="clear-btn">✕ Clear</a><?php endif; ?>
            </form>
        </div>

        <div class="table-wrap">
            <?php if (empty($messages)): ?>
            <div class="empty-state"><div style="font-size:40px">📭</div><p>No messages found<?= $search ? " for \"".htmlspecialchars($search)."\"" : '' ?>.</p></div>
            <?php else: ?>
            <table>
                <thead><tr><th>Sender</th><th>Subject</th><th>Status</th><th>Received</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($messages as $m): ?>
                    <tr class="<?= $m->status==='unread'?'is-unread':'' ?>" onclick="window.location='?section=messages&view=<?= $m->id ?>&filter=<?= $filter ?>'">
                        <td>
                            <div class="td-name"><?= htmlspecialchars($m->name) ?></div>
                            <div class="td-email"><?= htmlspecialchars($m->email) ?></div>
                        </td>
                        <td><span class="td-subj"><?= htmlspecialchars($m->subject) ?></span></td>
                        <td><?= $m->status==='unread' ? '<span class="badge-unread">Unread</span>' : '<span class="badge-read">Read</span>' ?></td>
                        <td class="td-date"><?= date('M j, Y · H:i', strtotime($m->created_at)) ?></td>
                        <td onclick="event.stopPropagation()">
                            <div class="td-actions">
                                <a href="?section=messages&view=<?= $m->id ?>&filter=<?= $filter ?>" class="ab ab-view">View</a>
                                <?php if ($m->status==='unread'): ?>
                                    <a href="?section=messages&action=read&id=<?= $m->id ?>&filter=<?= $filter ?>" class="ab ab-read">Mark Read</a>
                                <?php else: ?>
                                    <a href="?section=messages&action=unread&id=<?= $m->id ?>&filter=<?= $filter ?>" class="ab ab-unread">Unread</a>
                                <?php endif; ?>
                                <a href="?section=messages&action=delete&id=<?= $m->id ?>&filter=<?= $filter ?>" class="ab ab-del" onclick="return confirm('Delete permanently?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="table-footer">
                <span>Showing <?= count($messages) ?> of <?= $total ?> messages</span>
                <span id="last-check">Auto-refreshing every 30s</span>
            </div>
            <?php endif; ?>
        </div>

        <?php endif; /* end messages */ ?>

        <?php /* ══════════════ STORIES SECTION ══════════════ */ ?>
        <?php if ($section === 'stories'): ?>

        <div class="cm-grid">
            <!-- ADD / EDIT FORM -->
            <div class="cm-card">
                <div class="cm-card-head">
                    <h3><?= $editStory ? '✏️ Edit Story' : '➕ Add New Story' ?></h3>
                    <?php if (!$editStory): ?><span class="cm-count"><?= count($stories) ?> total</span><?php endif; ?>
                </div>
                <div class="cm-card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($editStory): ?>
                            <input type="hidden" name="story_action" value="edit">
                            <input type="hidden" name="id" value="<?= $editStory->id ?>">
                            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editStory->image ?? '') ?>">
                        <?php else: ?>
                            <input type="hidden" name="story_action" value="add">
                        <?php endif; ?>
                        <div class="field">
                            <label>Story Title *</label>
                            <input type="text" name="title" placeholder="e.g. The Lion & The Hare" value="<?= htmlspecialchars($editStory->title ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>Description *</label>
                            <textarea name="description" placeholder="Short description of the story…" required><?= htmlspecialchars($editStory->description ?? '') ?></textarea>
                        </div>
                        <div class="field">
                            <label>Story Link (URL) *</label>
                            <input type="url" name="link" placeholder="https://bloomlibrary.org/..." value="<?= htmlspecialchars($editStory->link ?? '') ?>" required>
                            <small>Paste the full URL where learners can read this story.</small>
                        </div>
                        <div class="field">
                            <label>Cover Image (optional)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                            <?php if (!empty($editStory->image)): ?>
                                <small>Current: <?= htmlspecialchars($editStory->image) ?></small>
                            <?php else: ?>
                                <small>Accepted: JPG, PNG, GIF, WEBP</small>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn-add"><?= $editStory ? '💾 Save Changes' : '➕ Add Story' ?></button>
                        <?php if ($editStory): ?>
                            <a href="?section=stories" class="btn-cancel">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- STORIES LIST -->
            <div>
                <?php if (empty($stories)): ?>
                <div class="cm-card"><div class="empty-state"><div style="font-size:36px">📖</div><p>No stories yet. Add one!</p></div></div>
                <?php else: ?>
                <div class="content-list">
                    <?php foreach ($stories as $i => $s): ?>
                    <div class="content-item">
                        <?php if (!empty($s->image)): ?>
                            <img src="../uploads/stories/<?= htmlspecialchars($s->image) ?>" class="ci-img" alt="">
                        <?php else: ?>
                            <div class="ci-num"><?= $i+1 ?></div>
                        <?php endif; ?>
                        <div class="ci-body">
                            <div class="ci-title"><?= htmlspecialchars($s->title) ?></div>
                            <div class="ci-desc"><?= htmlspecialchars($s->description) ?></div>
                            <a href="<?= htmlspecialchars($s->link) ?>" class="ci-link" target="_blank">🔗 <?= htmlspecialchars(substr($s->link,0,50)) ?>…</a>
                            <div class="ci-foot">
                                <a href="?section=stories&edit_story=<?= $s->id ?>" class="ab ab-edit">✏️ Edit</a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this story?')">
                                    <input type="hidden" name="story_action" value="delete">
                                    <input type="hidden" name="id" value="<?= $s->id ?>">
                                    <button type="submit" class="ab ab-del">🗑 Delete</button>
                                </form>
                                <a href="<?= htmlspecialchars($s->link) ?>" class="ab ab-view" target="_blank">👁 View</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; /* end stories */ ?>

        <?php /* ══════════════ PROVERBS SECTION ══════════════ */ ?>
        <?php if ($section === 'proverbs'): ?>

        <div class="cm-grid">
            <div class="cm-card">
                <div class="cm-card-head">
                    <h3><?= $editProverb ? '✏️ Edit Proverb' : '➕ Add New Proverb' ?></h3>
                    <?php if (!$editProverb): ?><span class="cm-count"><?= count($proverbs) ?> total</span><?php endif; ?>
                </div>
                <div class="cm-card-body">
                    <form method="POST">
                        <?php if ($editProverb): ?>
                            <input type="hidden" name="proverb_action" value="edit">
                            <input type="hidden" name="id" value="<?= $editProverb->id ?>">
                        <?php else: ?>
                            <input type="hidden" name="proverb_action" value="add">
                        <?php endif; ?>
                        <div class="field">
                            <label>Kinyarwanda Proverb *</label>
                            <input type="text" name="kinyarwanda" placeholder='e.g. "Ak\'imuhana kaza imvura ihise"' value="<?= htmlspecialchars($editProverb->kinyarwanda ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>English Translation *</label>
                            <input type="text" name="english" placeholder="e.g. Help from home comes after the rain…" value="<?= htmlspecialchars($editProverb->english ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>Explanation (optional)</label>
                            <textarea name="explanation" placeholder="Brief explanation of the proverb's meaning…"><?= htmlspecialchars($editProverb->explanation ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn-add"><?= $editProverb ? '💾 Save Changes' : '➕ Add Proverb' ?></button>
                        <?php if ($editProverb): ?>
                            <a href="?section=proverbs" class="btn-cancel">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div>
                <?php if (empty($proverbs)): ?>
                <div class="cm-card"><div class="empty-state"><div style="font-size:36px">🪶</div><p>No proverbs yet. Add one!</p></div></div>
                <?php else: ?>
                <div class="content-list">
                    <?php foreach ($proverbs as $i => $p): ?>
                    <div class="content-item">
                        <div class="ci-num"><?= $i+1 ?></div>
                        <div class="ci-body">
                            <div class="prov-kiny">"<?= htmlspecialchars($p->kinyarwanda) ?>"</div>
                            <div class="prov-eng"><?= htmlspecialchars($p->english) ?></div>
                            <?php if (!empty($p->explanation)): ?>
                                <div class="prov-expl"><?= htmlspecialchars($p->explanation) ?></div>
                            <?php endif; ?>
                            <div class="ci-foot">
                                <a href="?section=proverbs&edit_proverb=<?= $p->id ?>" class="ab ab-edit">✏️ Edit</a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this proverb?')">
                                    <input type="hidden" name="proverb_action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p->id ?>">
                                    <button type="submit" class="ab ab-del">🗑 Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; /* end proverbs */ ?>

        <?php /* ══════════════ MEDIA SECTION ══════════════ */ ?>
        <?php if ($section === 'media'): ?>

        <div class="cm-grid">
            <div class="cm-card">
                <div class="cm-card-head">
                    <h3><?= $editMedia ? '✏️ Edit Video' : '➕ Add New Video' ?></h3>
                    <?php if (!$editMedia): ?><span class="cm-count"><?= count($mediaList) ?> total</span><?php endif; ?>
                </div>
                <div class="cm-card-body">
                    <form method="POST">
                        <?php if ($editMedia): ?>
                            <input type="hidden" name="media_action" value="edit">
                            <input type="hidden" name="id" value="<?= $editMedia->id ?>">
                        <?php else: ?>
                            <input type="hidden" name="media_action" value="add">
                        <?php endif; ?>
                        <div class="field">
                            <label>Video Title *</label>
                            <input type="text" name="title" placeholder="e.g. Intore Dance" value="<?= htmlspecialchars($editMedia->title ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>Description *</label>
                            <textarea name="description" placeholder="Brief description of the video…" required><?= htmlspecialchars($editMedia->description ?? '') ?></textarea>
                        </div>
                        <div class="field">
                            <label>YouTube URL *</label>
                            <input type="text" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..." value="<?= htmlspecialchars($editMedia->youtube_url ?? '') ?>" required>
                            <small>Paste any YouTube link — full URL, short URL, or embed URL. All formats accepted.</small>
                        </div>
                        <button type="submit" class="btn-add"><?= $editMedia ? '💾 Save Changes' : '➕ Add Video' ?></button>
                        <?php if ($editMedia): ?>
                            <a href="?section=media" class="btn-cancel">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div>
                <?php if (empty($mediaList)): ?>
                <div class="cm-card"><div class="empty-state"><div style="font-size:36px">🎬</div><p>No videos yet. Add one!</p></div></div>
                <?php else: ?>
                <div class="content-list">
                    <?php foreach ($mediaList as $i => $mv): ?>
                    <div class="content-item">
                        <div class="vid-thumb">▶</div>
                        <div class="ci-body">
                            <div class="ci-title"><?= htmlspecialchars($mv->title) ?></div>
                            <div class="ci-desc"><?= htmlspecialchars($mv->description) ?></div>
                            <div class="vid-url"><?= htmlspecialchars($mv->youtube_url) ?></div>
                            <div class="ci-foot">
                                <a href="?section=media&edit_media=<?= $mv->id ?>" class="ab ab-edit">✏️ Edit</a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this video?')">
                                    <input type="hidden" name="media_action" value="delete">
                                    <input type="hidden" name="id" value="<?= $mv->id ?>">
                                    <button type="submit" class="ab ab-del">🗑 Delete</button>
                                </form>
                                <a href="<?= htmlspecialchars($mv->youtube_url) ?>" class="ab ab-view" target="_blank">▶ Preview</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; /* end media */ ?>

    </div><!-- /content -->
</div><!-- /main -->

<!-- ═══ MESSAGE MODAL ═════════════════════════════════════════════════════ -->
<?php if ($viewMsg): ?>
<div class="modal-backdrop" onclick="if(event.target===this)window.location='?section=messages&filter=<?= $filter ?>'">
    <div class="modal">
        <div class="modal-head">
            <div>
                <h3><?= htmlspecialchars($viewMsg->subject) ?></h3>
                <div class="modal-meta">From <span><?= htmlspecialchars($viewMsg->name) ?></span> · <?= date('F j, Y \a\t H:i', strtotime($viewMsg->created_at)) ?></div>
            </div>
            <button class="modal-close" onclick="window.location='?section=messages&filter=<?= $filter ?>'">✕</button>
        </div>
        <div class="modal-info">
            <div class="modal-detail"><label>Sender</label><span><?= htmlspecialchars($viewMsg->name) ?></span></div>
            <div class="modal-detail"><label>Email</label><span><?= htmlspecialchars($viewMsg->email) ?></span></div>
        </div>
        <div class="modal-body">
            <label>Message</label>
            <p><?= nl2br(htmlspecialchars($viewMsg->message)) ?></p>
        </div>
        <div class="modal-foot">
            <a href="?section=messages&action=delete&id=<?= $viewMsg->id ?>&filter=<?= $filter ?>" class="ab ab-del" onclick="return confirm('Delete permanently?')">🗑 Delete</a>
            <a href="mailto:<?= htmlspecialchars($viewMsg->email) ?>?subject=Re: <?= urlencode($viewMsg->subject) ?>" class="ab ab-read">✉️ Reply by Email</a>
            <a href="?section=messages&filter=<?= $filter ?>" class="ab ab-view">Close</a>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Clock
function tick() {
    const el = document.getElementById('clk');
    if (el) el.textContent = new Date().toLocaleString('en-RW', {
        weekday:'short', month:'short', day:'numeric',
        hour:'2-digit', minute:'2-digit'
    });
}
tick(); setInterval(tick, 60000);

// Auto-refresh messages every 30 seconds to catch new submissions
<?php if ($section === 'messages'): ?>
let lastCount = <?= $total ?>;
let lastUnread = <?= $unread ?>;

async function checkNewMessages() {
    try {
        const r = await fetch('dashboard.php?section=messages&check=1&_t=' + Date.now());
        const d = await r.json();
        if (d.total > lastCount || d.unread > lastUnread) {
            // Show notification
            const el = document.getElementById('last-check');
            if (el) el.textContent = '🔔 New message received! Refreshing…';
            setTimeout(() => window.location.reload(), 1200);
        } else {
            const el = document.getElementById('last-check');
            if (el) el.textContent = 'Last checked: ' + new Date().toLocaleTimeString();
        }
        lastCount  = d.total;
        lastUnread = d.unread;
    } catch(e) {}
}
setInterval(checkNewMessages, 30000);
<?php endif; ?>
</script>
</body>
</html>
<?php
// ─── JSON endpoint for live message count check ──────────────────────────────
if (isset($_GET['check'])) {
    header('Content-Type: application/json');
    $t = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $u = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
    echo json_encode(['total'=>$t,'unread'=>$u]);
    exit();
}
?>
