<?php
require_once "pdo.php";

if (!isset($_SESSION['user']) && !isset($_SESSION['root'])) {
    die("Access denied!");
    return;
}

//Temp: check current user
// if (!isset($_SESSION['user'])) {
//     print("User NULL");
//     var_dump($_SESSION['root']);
// } else if (!isset($_SESSION['root'])) {
//     print("root NULL");
//     var_dump($_SESSION['user']);
// }
//Display users
$sql_show = "SELECT * FROM users ORDER BY u_id";
$stmt_show = $pdo->query($sql_show);
$rows = $stmt_show->fetchAll(PDO::FETCH_ASSOC);

//Add users
if (isset($_POST['uname']) && isset($_POST['passwd']) && isset($_POST['conf_passwd']) && isset($_POST['add']) && isset($_POST['phone']) && isset($_POST['email'])) {
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
            $_SESSION['success'] = "Added user $uname";
            header("Location: users.php");
            return;
        } catch (PDOException $e) {
            $_SESSION['error'] = $e;
            header("Location: users.php");
            return;
        }
    }
}


//Delete user
if (isset($_POST['del']) && isset($_POST['u_id'])) {
    $sql_del = "DELETE FROM users WHERE u_id = :id";
    try {
        $stmt_del = $pdo->prepare($sql_del);
        $stmt_del->execute(array(":id" => $_POST['u_id']));
        header("Location: users.php");
        return;
    } catch (PDOException $e) {
        $_SESSION['error'] = $e;
        header("Location: users.php");
        return;
    }
}

//Edit user
if (isset($_POST['edit'])) {
    $_SESSION['id_edit'] = $_POST['u_id'];
    header("Location: edit.php");
    return;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Users</title>
</head>

<header>
    <h1>Users List</h1>
    <!-- <button type="button" onclick="homePage()">Home</button> -->
    <button type="button"><a class="homeBtn" href="index.php">Home</a></button>
</header>

<body>


    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Phone</th>
            <th>Email</th>
            <?php
            if (isset($_SESSION['root'])) {
                print("<th>Action</th>");
            }
            ?>
        </tr>
        <?php
        foreach ($rows as $row) {
            print('<tr>');
            print('<td>' . htmlentities($row['u_id']) . '</td>');
            print('<td class="uname">' . htmlentities($row['username']) . '</td>');
            print('<td class="passwd"><span>' . htmlentities($row['passwd']) . '</span></td>');
            print('<td class="phone">' . htmlentities($row['phone']) . '</td>');
            print('<td class="email">' . htmlentities($row['email']) . '</td>');
            if (isset($_SESSION['root'])) {
                print('<td>');
                print('<form method="POST">');
                print('<input type="hidden" name="u_id" value="' . $row['u_id'] . '">');
                print('<input type="submit" value="Delete" class="unsetBtn" name="del" onclick="check_del()">');
                print(" / ");
                print('<input type="submit" value="Edit" class="unsetBtn" name="edit">');
                print('</form>');
                print('</td>');
            }
            print('</tr>');
        }
        ?>
    </table>

    <br>
    <button type="button" class="addUserBtn">Add User</button>

    <form style="display: none" class="collItem" method="POST">
        <label for="uname">Username</label>
        <input type="text" class="uname" name="uname"> <br>
        <label for="passwd">Password</label>
        <input class="passwd" type="text" name="passwd"> <br>
        <label for="conf_passwd">Confirm Password</label>
        <input class="conf_passwd" type="text" name="conf_passwd"> <br>
        <label for="phone">Phone</label>
        <input type="text" class="phone" name="phone"> <br>
        <label for="email">Email</label>
        <input class="email" type="text" name="email"> <br>
        <button class="addBtn" type="submit" name="add">Add</button>
    </form>
    <div>
        <?php
        if (isset($_SESSION['error'])) {
            print("<p style='color: red;'>" . $_SESSION['error'] . "</p>");
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            print("<p style='color: green;'>" . $_SESSION['success'] . "</p>");
            unset($_SESSION['success']);
        }
        ?>
    </div>

    <br>


    <script>
    var coll = document.getElementsByClassName("addUserBtn");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            // if (content.style.maxHeight) {
            //     content.style.maxHeight = null;
            // } else {
            //     content.style.maxHeight = content.scrollHeight + "px";
            // }
            if (content.style.display == "none") {
                content.style.display = "block";
            } else {
                content.style.display = "none";
            }
        });
    }

    //confirm delete


    function homePage() {
        location.href = "index.php";
    }
    </script>
</body>

</html>