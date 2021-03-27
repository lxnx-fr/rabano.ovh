<?php


function signUpInputCheck($code, $uniqueId, $email, $password, $passwordRepeat): string
{
    $errCode = "";
    if (empty($email)) {     // Check if the $email if is filled
        $errCode = $errCode.".e";
    } else {
        if (invalidEmail($email)) // Check if the $email if is invalid
            $errCode = $errCode.".e1";
    }
    if (empty($code)) { // Check if the $code if is filled
        $errCode = $errCode.".c";
    } else {
        if (invalidCode($code)) // Check if the $code if is invalid
            $errCode = $errCode.".c1";
    }
    if (empty($uniqueId)) { // Check if the $uniqueId if is filled
        $errCode = $errCode.".u";
    } else {
        if (invalidUniqueId($uniqueId)) // Check if the $uniqueId if is invalid
            $errCode = $errCode.".u1";
    }
    if (empty($password)) {    // Check if the $email if is filled
        $errCode = $errCode.".p";
    }
    if (empty($passwordRepeat)) {   // Check if the $email if is filled
        $errCode = $errCode.".pr";
    }
    if (!empty($password) && !empty($passwordRepeat) && invalidPassword($password)) {
        $errCode = $errCode.".pi";
    }

    if (!empty($password) && !empty($passwordRepeat) && !invalidPassword($password) && !passwordMatch($password, $passwordRepeat)) {
        $errCode = $errCode.".pm";
    }
    return $errCode;
}


function signUpDbCheck(mysqli $conn, $code, $uniqueId, $email): string
{
    $errCode = "";
    if (!codeExists($conn, $code)) {
        $errCode = $errCode.".c2";
    }
    if (emailExists($conn, $email)) {
        $errCode = $errCode.".e2";
    }
    if (uniqueIdExists($conn, $uniqueId)) {
        $errCode = $errCode.".u2";
    }
    return $errCode;
}



function getErrorMessage( $formType,  $inputType)
{
    if(isset($_GET["x"]) && $_GET["x"] == $formType) {
        if (isset($_GET["reason"])) {
            $errors = $_GET["reason"];
            $errors = substr($errors, 1, strlen($errors));
            foreach (preg_split("/[\.]+/", $errors) as $a) {
                if ($inputType == "email")
                    if (substr($a, 0, strlen('e')) == "e")
                        return createErrorMessage($formType, $a);
                if ($inputType == "username")
                    if (substr($a, 0, strlen('u')) == "u")
                        return createErrorMessage($formType, $a);
                if ($inputType == "code")
                    if (substr($a, 0, strlen('c')) == "c")
                        return createErrorMessage($formType, $a);
                if ($inputType == "pr")
                    if (substr($a, 0, strlen('pr')) == "pr")
                        return createErrorMessage($formType, $a);
                if ($inputType == "p")
                    if (substr($a, 0, strlen('p')) == "p")
                        return createErrorMessage($formType, $a);
            }
        }
    }
}

function createErrorMessage(string $form, string $error): string {
    if ($form == "signup")
        switch ($error) {
            case "pr":
            case "p":
            case "u":
            case "c":
            case "e":
                return "Please fill in this field.";
            case "e1":
                return "Please fill in a valid E-Mail address. </br>(example@domain.com)";
            case "u1":
                return "Please fill in a valid Username. </br>(Length 2-20, A-Za-z0-9;.-_)";
            case "c1":
                return "Please fill in a valid Verification Code. </br>(Length 11, ex. 1x1-1x1-1x1)";
            case "pi":
                return "Please fill in a valid Password. </br>(Length 8-64, ex. A-Za-z0-9+^A-Za-z0-9)";
            case "pm":
                return "The given passwords do not match.";
            case "c2":
                return "The given Verification Code does not exist.";
            case "e2":
                return "The given Email does already exist.";
            case "u2":
                return "The given Username does already exist.";
            default:
                return "none";
        }
}

function invalidEmail($email): bool
{
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

function invalidCode($code): bool
{
    return !preg_match("/^[0-9a-z\-]{11}$/", $code);
}

function invalidPassword($password): bool
{
    return preg_match("/^[a-zA-Z0-9]+[^A-Za-z0-9]{8, 64}$/", $password);
}

function passwordMatch($password, $passwordRepeat): bool
{
    return $password == $passwordRepeat;
}

function invalidUniqueId($uniqueId): bool
{
    return !preg_match("/^[a-zA-Z0-9_.-]{2-20}*$/",$uniqueId);
}




/**
 *      EXISTS FUNCTIONS
 */

function uniqueIdExists(mysqli $conn, $uniqueId): bool
{
    if (!$stmt = $conn->prepare("SELECT * FROM users WHERE usersUniqueId = ?;")) {
        return false;
    }
    $stmt->bind_param("s", $uniqueId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->fetch_assoc()) {
        return true;
    } else {
        return false;
    }
}

function emailExists(mysqli $conn, $email): bool
{
    if (!$stmt = $conn->prepare("SELECT * FROM users WHERE usersEmail = ?;")) {
        return false;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->fetch_assoc()) {
        return true;
    } else {
        return false;
    }
}

function codeExists(mysqli $conn, $code): bool
{
    if (!$stmt = $conn->prepare("SELECT * FROM user_codes WHERE usersCode = ?;")) {
        return false;
    }
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->fetch_assoc()) {
        return true;
    } else {
        return false;
    }
}




/**
 *      CODE FUNCTIONS
 */

function createCode(mysqli $conn): bool
{
    if (!$stmt = $conn->prepare("INSERT INTO user_codes (usersCode) VALUES (?);")) {
        return false;
    }
    $pattern = "xbx-bxb-xbx";
    $split = '-';
    $replace = array('x', 'b');
    $code = createPatternRow($pattern, $split, $replace);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->close();
    return true;
}

function deleteCode(mysqli $conn, $code ): bool
{
    if (!$stmt = $conn->prepare("DELETE FROM user_codes WHERE usersCode = ?;")) {
        return false;
    }
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->close();
    return true;
}

function generateRandom($input, $size): string
{
    $result = "";

        for ($i = 0; $i < $size; $i++) {
            $index = rand(0, strlen($input) - 1);
            $result = $result . $input[$index];
        }

    return $result;
}

function createPatternRow($pattern, $split, $replace): string {
    $key = "";
    $continue = true;
    foreach ($replace as $ri) {
        if ($ri == $split) {
            $continue = false;
            break;
        }
    }
    $size = strlen($pattern);
    if ($continue) {
        for ($i = 0; $i < $size; $i++) {
            $r = $pattern[$i];
            if ($r == $replace[0]) {
                $add = generateRandom("0123456789", 1);
                $key = $key . $add;
                echo "</br>Durchgang ". $i ."von".$size. ": key: ". $key. " toadd: ". $add;
            } else if ($r == $replace[1]) {
                $add = generateRandom("abcdefghijklmnopqrstuvwxyz", 1);
                $key = $key . $add;
                echo "</br>Durchgang ". $i ."von".$size. ": key: ". $key. " toadd: ". $add ;
            } else if ($r == $split) {
                $key = $key . $split;
            }
        }
    }
    return $key;
}




function createUser(mysqli $conn, $code, $uniqueId, $email, $password): bool
{
    if (!$stmt = $conn->prepare("INSERT INTO users (usersUniqueId, usersEmail, usersPassword, usersAdditionalInfo) VALUES (?, ?, ?, ?);")) {
        return false;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $additionalInfo = json_encode(array($code));
    $stmt->bind_param("ssss", $uniqueId, $email, $hashedPassword, $additionalInfo);
    $stmt->execute();
    $stmt->close();
    deleteCode($conn, $code);
    return true;
}

