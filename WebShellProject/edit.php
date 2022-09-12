<?php
require_once "pdo.php";

if (isset($_POST['cancel'])) {
    header("Location: users.php");
    return;
}

//Display user's current info
$sql_show = "SELECT * FROM users WHERE u_id=:id";
$stmt = $pdo->prepare($sql_show);
$stmt->execute(array(":id" => $_SESSION['id_edit']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "Bad value for id";
    header("Location: users.php");
    return;
}
$cur_uname = htmlentities($row['username']);
$cur_phone = htmlentities($row['phone']);
$cur_email = htmlentities($row['email']);

//Edit user
if (isset($_POST['uname']) && isset($_POST['passwd']) && isset($_POST['conf_passwd']) && isset($_POST['submit']) && isset($_POST['phone']) && isset($_POST['email'])) {
    $uname = $_POST['uname'];
    $passwd = $_POST['passwd'];
    $conf_passwd = $_POST['conf_passwd'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (strlen($uname) < 1 || strlen($passwd) < 1 || strlen($phone) < 1 || strlen($email) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: users.php");
        return;
    } else if ($conf_passwd != $passwd) {
        $_SESSION['error'] = "Confirm password does not match the password";
        header("Location: users.php");
        return;
    } else if (!ctype_digit($phone)) {
        $_SESSION['error'] = "Phone is invalid";
        header("Location: users.php");
        return;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email is invalid";
        header("Location: users.php");
        return;
    } else {
        try {
            $sql_add = "INSERT INTO users (username, passwd, phone, email) VALUES (:u, :p, :phone, :e)";
            $passwd_hash = hash("sha256", $passwd);
            $stmt_add = $pdo->prepare($sql_add);
            $stmt_add->execute(array(
                ":u" => $uname,
                ":p" => $passwd_hash,
                ":phone" => $phone,
                ":e" => $email
            ));
            $_SESSION['success'] = "Edited user $uname";
            header("Location: users.php");
            return;
        } catch (PDOException $e) {
            $_SESSION['error'] = $e;
            header("Location: users.php");
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
    <link rel="stylesheet" href="style.css">
    <title>Edit</title>
</head>
<header>
    <h1>Edit User</h1>
</header>

<body>

    <form class="editUsersForm" method="POST">
        <label for="uname">Username</label>
        <input type="text" class="uname" name="uname" value="<?= $cur_uname ?>"> <br>
        <label for="passwd">Password</label>
        <input class="passwd" type="text" name="passwd"> <br>
        <label for="conf_passwd">Confirm Password</label>
        <input class="conf_passwd" type="text" name="conf_passwd"> <br>
        <label for="phone">Phone</label>
        <input type="text" class="phone" name="phone" value="<?= $cur_phone ?>"> <br>
        <label for="email">Email</label>
        <input class="email" type="text" name="email" value="<?= $cur_email ?>"> <br>
        <input type="hidden" name="u_id" value="<?= $_SESSION['id_edit'] ?>">
        <div class="editBtn">
            <button type="submit" value="Submit" name="submit">Submit</button>
            <button type="submit" value="Cancel" name="cancel">Cancel</button>
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