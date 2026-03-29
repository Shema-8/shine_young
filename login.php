<?php
session_start();
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $db = new PDO("mysql:host=sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $db->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $username);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_OBJ);

        if (!empty($data) && $data->password == md5($password)) {
            $_SESSION['login'] = $data->id;
            $_SESSION['username'] = $data->username;
            $_SESSION['names'] = $data->names;
            header("Location:./public/index.html");
        } else {
            header("Location:./index.php?error=107");
        }
    } catch (Exception $e) {
        header("Location:./index.php?error=109");
    }
} else {
    header("Location:./index.php");
}