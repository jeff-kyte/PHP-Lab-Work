<?php
    include "./Lab5Common/Functions.php";
    session_start();
    requireAuth(); // Only authenticated users may access this page
    $studentId = $_SESSION['studentId'];
    $semesterList = getSemesters();
    $selectedSemester = $_POST['selectSemester'];
    $registrationResult = "";
    if (!isset($selectedSemester)) {
        $selectedSemester = $semesterList[0]['SemesterCode'];
    }
    if (isset($_POST["btnSubmit"])) {
        $registrationResult = registerCourses($studentId, $selectedSemester, $_POST['selectedCourses']);
    }
    
    $offeredCourses = getOfferedCourses($studentId, $selectedSemester);
    $weeklyHours = getWeeklyHours($studentId, $selectedSemester);
    $studentName = getStudentName($studentId);
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
        <h1>Course Selection</h1>
        <p>Welcome <strong><?php print $studentName; ?></strong> (not you? change user <a href="Logout.php">here</a>).</p>
        <p>You have registered <strong><?php print $weeklyHours ?></strong> hours for the selected semester.</p>
        <p>You can register <strong><?php print MAX_WEEKLY_HOURS - $weeklyHours ?></strong> more hours of course(s) for the semester.</p>
        <p>Please note that the courses you have registered will not be displayed in the list.</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <select onchange="this.form.submit()" id="selectSemester" name="selectSemester">
                <?php
                    foreach($semesterList as $semester) { // Populate semester drop down list
                        print "<option value=\"{$semester['SemesterCode']}\""
                            . ($selectedSemester == $semester['SemesterCode'] ? "selected" : "")
                            . ">{$semester['Term']} {$semester['Year']}</option>";
                    }
                ?>
            </select>
            <div>
                <br />
                <span class="error">
                    <?php
                        if ($registrationResult != "") {
                            print $registrationResult;
                        }
                    ?>
                </span>
                <table>
                    <tr>
                        <th>Code</th><th>Course Title</th><th>Hours</th><th>Select</th>
                    </tr>
                    <?php
                        foreach($offeredCourses as $course) {
                     ?>
                    <tr>
                        <td><?php print $course['CourseCode']; ?></td>
                        <td><?php print $course['Title']; ?></td>
                        <td><?php print $course['WeeklyHours']; ?></td>
                        <td><input type="checkbox" value="<?php print $course['CourseCode']; ?>" name="selectedCourses[]"></input></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table>
                <br />
                <input type="submit" value="Submit" name="btnSubmit"/>
                <input type="reset" value="Clear"name="clear"/>
            </div>
        </form>
        <?php include('./Lab5Common/Footer.php'); ?>
    </body>
</html>
