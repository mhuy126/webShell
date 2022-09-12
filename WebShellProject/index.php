<?php
require_once "pdo.php";

if (isset($_POST['logout'])) {
    unset($_SESSION['user']);
    unset($_SESSION['root']);
    header("Location: index.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" />
    <title>Welcome Page</title>
</head>

<body>
    <header>
        <h1>Welcome Page</h1>
        <?php
        if (isset($_SESSION['user'])) { ?>
        <div class="user-bar">
            User: <a href="profile.php"><?php print(htmlentities($_SESSION['user'])) ?></a>
            | <form method="POST">
                <button type="submit" name="logout" class="unsetBtn">Logout</button>
            </form>
        </div>
        <?php } else if (isset($_SESSION['root'])) { ?>
        <div class="user-bar">
            User: <a href="profile.php"><?php print(htmlentities($_SESSION['root'])) ?></a>
            | <form method="POST">
                <button type="submit" name="logout" class="unsetBtn">Logout</button>
            </form>
        </div>
        <?php } else {
            print("Not login");
        }

        ?>
    </header>
    <br>
    <p><strong>Introduction: </strong>This is a web-client which would demonstrate the <strong>WebShell
            Testing</strong> - the program is used for penetration testing and written in <strong>Python</strong>
    </p>
    <br />


    <?php
    if (!isset($_SESSION['user']) && !isset($_SESSION['root'])) {
        print('<p>Please <a href="login.php">Login</a> for more options</p>');
    } else if (isset($_SESSION['root'])) {
        print('<a href="users.php">View users</a>');
        print("<br />");
        print('<a href="products.php">Products</a>');
    } else {
        print('<a href="products.php">Products</a>');
    }

    ?>
    <br />


</body>

</html>