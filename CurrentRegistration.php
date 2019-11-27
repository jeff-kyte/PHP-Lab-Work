<?php
    include "./Lab5Common/Functions.php";
    session_start();
    requireAuth(); // Only authenticated users may access this page
    $studentId = $_SESSION['studentId'];
    $courses = $_POST['selectedCourses'];
    if (isset($_POST["btnSubmit"])) {
        print unregisterCourses($studentId, $courses);
    }
    $studentName = getStudentName($studentId);
    $registeredCourses = getRegisteredCourses($studentId);
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
        <h1>Current Registrations</h1>
        <p>Hello <?php print $studentName; ?> (not you? change user <a href="Logout.php">here</a>), the following are your current registrations.</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return confirm('The selected registrations will be deleted!')">
            <table>
                <tr>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th></th>
                    <th>Hours</th>
                    <th>Select</th>
                </tr>
                <?php
                    $semesterHours = 0;
                    $semesterTerm = "";
                    foreach($registeredCourses as $course) {
                        if ($semesterTerm != "" && $semesterTerm != $course['Term']) {
                            // Print sum of hours for semester
                            print "<tr><td colspan=\"5\" class=\"summaryRow\"><strong>Total Weekly Hours</strong></td><td>{$semesterHours}</td><td></td></tr>";
                            $semesterHours = 0;
                        }
                        $semesterTerm = $course['Term'];
                ?>
                <tr>
                    <td><?php print $course['Year']; ?></td>
                    <td><?php print $course['Term']; ?></td>
                    <td><?php print $course['Code']; ?></td>
                    <td><?php print $course['Title']; ?></td>
                    <td></td>
                    <td><?php print $course['Hours']; ?></td>
                    <td><input type="checkbox" value="<?php print $course['Code']; ?>" name="selectedCourses[]"></input></td>
                </tr>
                <?php
                        $semesterHours += $course['Hours'];
                    }
                    // Print sum of hours for semester
                    print "<tr><td colspan=\"5\" class=\"summaryRow\"><strong>Total Weekly Hours</strong></td><td>{$semesterHours}</td><td></td></tr>";
                ?>
            </table>
            <input type="submit" value="Delete Selected" name="btnSubmit" />
            <input type="reset" value="Clear"name="clear"/>
        </form>
        <?php include('./Lab5Common/Footer.php'); ?>
    </body>
</html>
