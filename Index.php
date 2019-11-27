<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Online Course Registration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/AlgCommon/Contents/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/AlgCommon/Contents/AlgCss/Site.css" rel="stylesheet" type="text/css"/>
        <link href="/Lab5/Lab5Contents/Site.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include('./Lab5Common/Header.php'); ?>
        
        <h1>Welcome to Algonquin College Online Course Registration</h1>
        <p>If you have never used this before, you have to <a href="NewUser.php">sign up</a> first.</p>
        <p>If you have already signed up, you can <a href="Login.php">log in</a> now.</p>
        
        <?php include('./Lab5Common/Footer.php'); ?>
    </body>
</html>
