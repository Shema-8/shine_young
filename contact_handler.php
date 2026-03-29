<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./public/index.html");
    exit();
}

$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    header("Location: ./public/index.html?error=missing#contact");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ./public/index.html?error=email#contact");
    exit();
}

try {
    $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare(
        "INSERT INTO contact_messages (name, email, subject, message, status, created_at)
         VALUES (:name, :email, :subject, :message, 'unread', NOW())"
    );
    $stmt->bindParam(':name',    $name);
    $stmt->bindParam(':email',   $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    header("Location: ./public/index.html?sent=1#contact");
} catch (Exception $e) {
    header("Location: ./public/index.html?error=db#contact");
}
exit();
