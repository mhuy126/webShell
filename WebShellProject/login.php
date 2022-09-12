<?php
require_once "pdo.php";

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['username']) && isset($_POST['passwd']) && isset($_POST['login'])) {
    $username = $_POST['username'];
    $tmp_passwd = $_POST['passwd'];
    unset($_SESSION['user']);
    unset($_SESSION['root']);

    if (strlen($username) < 1 || strlen($tmp_passwd) < 1) {
        $_SESSION['error'] = "Both username and password are required";
        header("Location: login.php");
        return;
    } else if ($username == "root") {
        $passwd_root = hash("sha256", $tmp_passwd);
        $sql_root = "SELECT passwd FROM webshell.users WHERE username = :ur AND passwd=:pwdr";
        $stmt_root = $pdo->prepare($sql_root);
        $stmt_root->execute(array(
            ":ur" => $username,
            ":pwdr" => $passwd_root
        ));
        $root_user = $stmt_root->rowCount();
        if ($root_user) {
            $_SESSION['root'] = $username;
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Login Failed";
            header("Location: login.php");
            return;
        }
    } else {
        $passwd = hash("sha256", $tmp_passwd);
        $query = "SELECT passwd FROM webshell.users WHERE username = :u AND passwd = :p";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(
            ":u" => $username,
            ":p" => $passwd
        ));
        //Normal user
        $valid_user = $stmt->rowCount();
        if ($valid_user) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Login Failed";
            header("Location: login.php");
            return;
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>
    <form class="loginForm" method="POST">
        <label for="uname">Username</label>
        <input type="text" class="uname" name="username"> <br>
        <label for="passwd">Password</label>
        <input class="passwd" type="text" name="passwd"> <br>
        <div class="loginBtn">
            <button name="login">Login</button>
            <button name="cancel">Back</button>
        </div>
    </form>

    <?php
    if (isset($_SESSION['error'])) {
        print("<p style='color: red;'>" . $_SESSION['error'] . "</p>");
        unset($_SESSION['error']);
    }
    ?>

</body>

</html>