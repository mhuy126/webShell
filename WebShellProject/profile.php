<?php
require_once "pdo.php";
// @import url('/style.css');

if (isset($_POST['cancel'])) {
    header("Location: profile.php");
    return;
}

//Display user's current info
$sql_show = "SELECT * FROM users WHERE username=:u";
$stmt = $pdo->prepare($sql_show);
if (isset($_SESSION['user'])) {
    $stmt->execute(array(":u" => $_SESSION['user']));
} else {
    $stmt->execute(array(":u" => $_SESSION['root']));
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "Bad value";
    header("Location: profile.php");
    return;
}
$cur_id = htmlentities($row['u_id']);
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
        header("Location: profile.php");
        return;
    } else if ($conf_passwd != $passwd) {
        $_SESSION['error'] = "Confirm password does not match the password";
        header("Location: profile.php");
        return;
    } else if (!ctype_digit($phone)) {
        $_SESSION['error'] = "Phone is invalid";
        header("Location: profile.php");
        return;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email is invalid";
        header("Location: profile.php");
        return;
    } else {
        try {
            $sql_add = "INSERT INTO users (username, passwd, phone, email) VALUES (:u, :p, :phone, :e)";
            $passwd_hash = hash("sha256", $passwd);
            $stmt_add = $pdo->prepare($sql_add);
            $add = $stmt_add->execute(array(
                ":u" => $uname,
                ":p" => $passwd_hash,
                ":phone" => $phone,
                ":e" => $email
            ));
            if ($add) {
                $sql_delete_cur_user = "DELETE FROM users WHERE u_id=:u";
                $stmt_delete_cur_user = $pdo->prepare($sql_delete_cur_user);
                $stmt_delete_cur_user->execute(array(":u" => $cur_id));
                $_SESSION['success'] = "Edited user $uname";
                header("Location: profile.php");
                return;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = $e;
            header("Location: profile.php");
            return;
        }
    }
}

//Display avatar
// $sql_avatar = "SELECT avatar FROM avatars WHERE u_id=:id";
// $stmt_avatar = $pdo->prepare($sql_avatar);
// try {
//     $stmt_avatar->execute(array(":id" => $cur_id));
//     $row_avatar = $stmt_avatar->fetch(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     $_SESSION['error'] = $e;
//     header("Location: profile.php");
//     return;
// }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Profile</title>
</head>
<header>
    <h1>User Profile</h1>
    <button type="button"><a class="homeBtn" href="index.php">Home</a></button>
</header>

<body>
    <div class="profile">
        <div class="avatar">
            <h3>Avatar</h3>
            <?php
            // if ($row_avatar) { 
            ?>
            <!-- <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row_avatar['avatar']) ?>" -->
            alt="Empty"> <br>
            <?php
            // } 
            ?>
            <!-- else { ?> -->
            <img src="/uploads/tempAvatar.jpg" alt=" avatar"> <br>
            <form method="POST" action="uploadAvatar.php" enctype="multipart/form-data">
                <input type="file" name="avatar">
                <button type="submit" name="upload">Upload Avatar</button>
                <!-- <input type="submit" value="Upload"> -->
            </form>

        </div>
        <div class="info">
            <strong>User ID:</strong>
            <?php print(htmlentities($cur_id)) ?><br>
            <strong>Username:</strong>
            <?php print(htmlentities($cur_uname)) ?><br>
            <strong>Pasword:</strong>
            <?php print("****************") ?> <br>
            <strong>Phone:</strong>
            <?php print(htmlentities($cur_phone)) ?> <br>
            <strong>Email:</strong>
            <?php print(htmlentities($cur_email)) ?><br>
            <button type="button" onClick="openEdit()" class="edit_btn">Edit</button>
        </div>
        <div class="editProfile" style="display:none">
            <form method="POST">
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
                <input type="hidden" name="u_id" value="<?= $cur_id ?>">
                <button type="submit" value="Submit" name="submit">Submit</button>
                <button type="submit" value="Cancel" name="cancel">Cancel</button>
            </form>
        </div>
    </div>
    <?php
    if (isset($_SESSION['error'])) {
        print("<p style='color: red;'>" . $_SESSION['error'] . "</p>");
        unset($_SESSION['error']);
    } else if (isset($_SESSION['success'])) {
        print("<p style='color: green;'>" . $_SESSION['success'] . "</p>");
        unset($_SESSION['success']);
    }
    ?>


    <!-- --------------------------- -->
    <script>
    var editForm = document.getElementsByClassName("editProfile")[0];
    var info = document.getElementsByClassName("info")[0];

    function openEdit() {
        if (editForm.style.display == "none") {
            editForm.style.display = "flex";
            info.style.display = "none";
        } else {
            editForm.style.display = "none";
            info.style.display = "flex";
        }
    }
    </script>
</body>

</html>