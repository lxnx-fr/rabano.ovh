<?php

if (isset($_POST["submit"])) {
    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';
    $code = $_POST["code"];
    $email = $_POST["email"];
    $uniqueId = $_POST["uniqueId"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["passwordRepeat"];
    $errCode = signUpInputCheck($code, $uniqueId, $email, $password, $passwordRepeat);
    if (empty($errCode)) { // If $errCode is empty: every input is filled with details
        $errCode = signUpDbCheck($conn, $code, $uniqueId, $email);
        if (empty($errCode)) {
            createUser($conn, $code, $uniqueId, $email, $password);
            header("Location: ../../index.php?x=signup&reason=success");
        } else {
            header("Location: ../../index.php?x=signup&reason=$errCode");
        }

    } else {
        header("Location: ../../index.php?x=signup&reason=$errCode");
    }
} else {
    header('Location: ../../index.php#signup');
}
