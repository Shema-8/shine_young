<?php
session_start();
if (isset($_POST['submit'])) {
    $error = [];

    if (strlen(trim($_POST['fnames'] ?? '')) < 2) $error['fnames'] = true;
    if (strlen(trim($_POST['lnames'] ?? '')) < 2) $error['lnames'] = true;
    if (strlen(trim($_POST['username'] ?? '')) < 4) $error['username'] = true;
    if (strlen(trim($_POST['names'] ?? '')) < 4)    $error['names'] = true;
    if (strlen($_POST['password'] ?? '') < 4)       $error['password'] = true;
    if (($_POST['password'] ?? '') !== ($_POST['password2'] ?? '')) $error['password2'] = true;

    if (!empty($error)) {
        $_SESSION['feedback'] = $_POST;
        $_SESSION['feedback']['error'] = $error;
        header("Location:./signup.php");
        exit();
    }

    $fnames    = trim($_POST['fnames']);
    $lnames    = trim($_POST['lnames']);
    $names     = trim($_POST['names']);        // category / role
    $telephone = trim($_POST['telephone'] ?? '');
    $username  = trim($_POST['username']);
    $password  = md5($_POST['password']);

    try {
        $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check username uniqueness
        $check = $db->prepare("SELECT id FROM users WHERE username = :username");
        $check->bindParam(":username", $username);
        $check->execute();
        if ($check->fetch()) {
            $_SESSION['feedback'] = $_POST;
            $_SESSION['feedback']['error'] = ['username_taken' => true];
            header("Location:./signup.php?error=taken");
            exit();
        }

        $query = $db->prepare(
            "INSERT INTO users(names, username, password) VALUES(:names, :username, :password)"
        );
        $fullnames = $fnames . ' ' . $lnames;
        $query->bindParam(":names",    $fullnames);
        $query->bindParam(":username", $username);
        $query->bindParam(":password", $password);
        $query->execute();

        header("Location:./index.php?success=true");
    } catch (Exception $e) {
        header("Location:./signup.php?error=109");
    }
} else {
    header("Location:./signup.php");
}
