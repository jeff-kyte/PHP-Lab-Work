<?php
    include "./Lab5Common/Functions.php";
    session_start();
    $clearPage = false;
    $isSubmitted = false;
    if (isset($_POST["submit"])) {
        $isSubmitted = true;
        $studentId = htmlspecialchars($_POST["studentId"]);
        $phoneNumber = htmlspecialchars($_POST["phoneNumber"]);
        $name = htmlspecialchars($_POST["name"]);
        $password = htmlspecialchars($_POST["password"]);
        $passwordAgain = htmlspecialchars($_POST["passwordAgain"]);
        
        if (ValidateStudentId($studentId) == "" // If form submission is valid:
                && ValidateName($name) == ""
                && ValidatePhone($phoneNumber) == ""
                && ValidatePassword($password) == ""
                && ValidatePasswordAgain($password, $passwordAgain) == "") {
            // Add new student to database
            createStudent($studentId, $name, $phoneNumber, sha1($password));
            // Update session
            $_SESSION["studentId"] = $studentId;
            // Redirect page
            header("Location: CourseSelection.php");
            exit();
        }
    }
?><!DOCTYPE html>
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
        
        <h1>Sign Up</h1>
        <p>All fields are required.</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="inputGroup">
                <label for="studentId">Student ID:</label>
                <input type="text" id="studentId" name="studentId"
                       value="<?php
                            if (isset($studentId)) {
                                print $studentId;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print ValidateStudentId($studentId);
                        }
                    ?>
                </span>
            </div>
            <div class="inputGroup">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name"
                       value="<?php
                            if (isset($name)) {
                                print $name;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print ValidateName($name);
                        }
                    ?>
                </span>
            </div>
            <div class="inputGroup">
                <label for="phoneNumber">Phone Number:<span>(nnn-nnn-nnnn)</span></label>
                <input id="phoneNumber" type="tel" name="phoneNumber"
                       value="<?php
                            if (isset($phoneNumber)) {
                                print $phoneNumber;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print ValidatePhone($phoneNumber);
                        }
                    ?>
                </span>
            </div>
            <div class="inputGroup">
                <label for="password">Password:</label>
                <input id="password" type="password" name="password"
                       value="<?php
                            if (isset($password)) {
                                print $password;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted) {
                            print ValidatePassword($password);
                        }
                    ?>
                </span>
            </div>
            <div class="inputGroup">
                <label for="passwordAgain">Password Again:</label>
                <input id="passwordAgain" type="password" name="passwordAgain"
                       value="<?php
                            if (isset($passwordAgain)) {
                                print $passwordAgain;
                            }?>" />
                <span class="error">
                    <?php
                        if ($isSubmitted && isset($password)) {
                            print ValidatePasswordAgain($password, $passwordAgain);
                        }
                    ?>
                </span>
            </div>
            <input type="submit" value="Submit" name="submit"/>
            <input type="reset" value="Clear" name="clear" id="reset"/>
        </form>
        <?php include('./Lab5Common/Footer.php'); ?>
    </body>
</html>
