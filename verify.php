<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}
include("config.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home</title>
</head>

<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">Logo</a> </p>
        </div>

        <div class="right-links">

            <?php
            require "mail.php";
            require "functions.php";
            $errors = array();


            $id = $_SESSION['id'];
            $query = mysqli_query($con, "SELECT*FROM users WHERE Id=$id");

            while ($result = mysqli_fetch_assoc($query)) {
                $res_Uname = $result['Username'];
                $res_Email = $result['Email'];
                $res_Age = $result['Age'];
                $res_id = $result['Id'];
            }
            if ($_SERVER['REQUEST_METHOD'] == "GET" && !check_verified()) {
                $vars['code'] =  rand(10000, 99999);
                $vars['expires'] = (time() + (60 * 10));
                $vars['email'] = $res_Email;


                $query = "insert into verify (code,expires,email) values (:code,:expires,:email)";
                database_run($query, $vars);

                $message = "your code is " . $vars['code'];
                $subject = "Email verification";
                $recipient = $vars['email'];
                send_mail($recipient, $subject, $message);
            }

            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                if (!check_verified()) {

                    $query = "select * from verify where code = :code && email = :email";
                    $vars = array();
                    $vars['email'] = $res_Email;
                    $vars['code'] = $_POST['code'];

                    $row = database_run($query, $vars);

                    if (is_array($row)) {
                        $row = $row[0];
                        $time = time();

                        if ($row->expires > $time) {

                            $id = $_SESSION['id'];

                            $query = "update users set verified = email where id = '$id' limit 1";

                            database_run($query);
                            $_SESSION['valid'] = $res_Email;

                            header("Location: home.php");
                            die;
                        } else {
                            echo "Code expired";
                        }
                    } else {
                        echo "wrong code";
                    }
                } else {
                    echo "You're already verified";
                }
            }
            ?>
        </div>
    </div>
    <main>

        <div class="container">
            <div class="box form-box">
                <div class="field input">

                    <br>Enter the verification code sent to your email<br>
                </div>
                <div>
                    <?php if (count($errors) > 0) : ?>
                        <?php foreach ($errors as $error) : ?>
                            <?= $error ?> <br>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div><br>
                <form method="post">
                    <div class="field input">

                        <input type="text" name="code" placeholder="Enter your Code"><br>
                    </div>
                    <br>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="Verify">
                    </div>
                </form>

            </div>
        </div>

    </main>
</body>

</html>