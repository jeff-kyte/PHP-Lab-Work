<?php
    include "./Lab5Common/Functions.php";
    session_start();
    $loginError = "";
    $clearPage = false;
    $isSubmitted = false;
    $credentialsValid = true;
    if (isset($_POST["submit"])) {
        $isSubmitted = true;
        $studentId = htmlspecialchars($_POST["studentId"]);
        $password = htmlspecialchars($_POST["password"]);
        if ($studentId != "" && $password != "") {
            $credentialsValid = validateLogin($studentId, sha1($password));
            if ($credentialsValid) { // Login credentials are valid
                // Update session
                $_SESSION["studentId"] = $studentId;
                // Redirect page
                header("Location: CourseSelection.php");
                exit();
            }
        }
    }
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
        <h1>Log In</h1>
        <p>You need to <a href="NewUser.php">sign up</a> if you are a new user.</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div>
                <?php
                    if (!$credentialsValid) {
print<<<LoginError
                        <span class="error">
                            Incorrect Student ID and/or Password!
                        </span>
                        <br />
LoginError;
                    }
                ?>
                <label for="StudentId">Student ID:</label>
                <input type="text" id="studentId" name="studentId"
                       value="<?php
                            if (isset($studentId)) {
                                print $studentId;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print validateRequiredField($studentId);
                        }
                    ?>
                </span>
                <br />
                <label for="password">Password:</label>
                <input id="password" type="password" name="password"
                       value="<?php
                            if (isset($password)) {
                                print $password;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print validateRequiredField($password);
                        }
                    ?>
                </span>
                <br />
                <input type="submit" value="Submit" name="submit"/>
                <input type="reset" value="Clear"name="clear"/>
            </div>
        </form>
        
        
        <?php include('./Lab5Common/Footer.php'); ?>
    </body>
</html>
