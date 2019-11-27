<?php
define("MAX_WEEKLY_HOURS", 16);
define("SEMESTER_NAMES", array('F' => 'Fall', 'W' => 'Winter', 'S' => 'Spring'));
function getPDO() { // Return PHP Database Object for generic use
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    $myPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $myPdo;
}
function ValidateName($name) {
    if (!isset($name)
            || $name == "") {
        return "Name can not be blank";
    }
    return "";
}
function ValidatePhone($phone) {
    $phoneRegex = "/^[2-9][\d]{2}-[2-9][\d]{2}-[\d]{4}$/";
    if (!isset($phone)
            || $phone == ""
            || !preg_match($phoneRegex, $phone)) {
        return "Incorrect phone number";
    }
    return "";
}
function ValidatePassword($password) {
    $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/";
    if (!isset($password)
            || $password == ""
            || !preg_match($passwordRegex, $password)) {
        return "Password must be at least 6 characters long, contain at least one upper case letter, one lowercase letter and one digit";
    }
    return "";
}
function ValidatePasswordAgain($password, $passwordAgain) {
    if (!isset($password)
            || !isset($passwordAgain)
            || $password != $passwordAgain) {
        return "Passwords do not match";
    }
    return "";
}
function validateRequiredField($input) {
    if (!isset($input)
            || $input == "") {
        return "Required";
    }
    return "";
}
function requireAuth() { // Called to redirect unauthenticated users
    if (!isset($_SESSION["studentId"])) { // If user is not authenticated
        header("Location: Login.php"); // Redirect to login page
        exit();
    }
}
function createStudent($studentId, $name, $phoneNumber, $hashedPassword) { // Inserts new row into Student table
    $sql = "insert into Student (StudentId, Name, Phone, Password) values(:studentId, :name, :phoneNumber, :hashedPassword)";
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId,
                              ':name' => $name,
                              ':phoneNumber' => $phoneNumber,
                              ':hashedPassword' => $hashedPassword));
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
}
function validateLogin($studentId, $hashedPassword) { // Compare login credentials with database records
    $sql = "select * from Student where StudentId = :studentId && Password = :hashedPassword";
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId, ':hashedPassword' => $hashedPassword));
        $row = $pStmt->fetch();
        if ($row) {
            return true;
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return false;
}
function ValidateStudentId($studentId) { // Used on signup page to enforce unique student ID
    $sql = "Select StudentId from Student where StudentId = :studentId";
    if (!isset($studentId)
            || $studentId == "") {
        return "Student ID can not be blank";
    } // Check if studentId already exists in database:
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId));
        $row = $pStmt->fetch();
        if ($row) {
            return "A student with this ID has already signed up";
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return "";
}
function getSemesters() { // Return entire Semester table
    $sql = "select * from Semester";
    $semesterList = array();
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute();
        while ($row = $pStmt->fetch(PDO::FETCH_ASSOC)) {
            $semesterList[] = $row;
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return $semesterList;
}
function getOfferedCourses($studentId, $semesterCode) { // Return info on courses available to given student in given semester
    $sqlCodes = "select CourseCode from CourseOffer where SemesterCode = :semesterCode and CourseCode not in (select CourseCode from Registration where StudentId = :studentId)";
    $sqlCourses = "select * from Course where CourseCode = :courseCode";
    $courseCodes = array();
    $courseInfo = array();
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sqlCodes);
        $pStmt->execute(array(':semesterCode' => $semesterCode, ':studentId' => $studentId));
        while ($row = $pStmt->fetch(PDO::FETCH_ASSOC)) {
            $courseCodes[] = $row['CourseCode'];
        }
        $pStmt = $myPdo->prepare($sqlCourses);
        foreach ($courseCodes as $courseCode) {
            $pStmt->execute(array(':courseCode' => $courseCode));
            $courseInfo[] = $pStmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return $courseInfo;
}
function getStudentName($studentId) { // Return name of given student
    $sql = "select Name from Student where StudentId = :studentId";
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId));
        return $pStmt->fetch(PDO::FETCH_ASSOC)['Name'];
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
}
function getWeeklyHours($studentId, $semesterCode) { // Return registered weekly hours for given student and semester
    $sql = "SELECT sum(WeeklyHours) FROM Course WHERE CourseCode in (select CourseCode from Registration where StudentId = :studentId && SemesterCode = :semesterCode)";
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId, ':semesterCode' => $semesterCode));
        $hours = $pStmt->fetch(PDO::FETCH_NUM)[0];
        return (isset($hours) ? $hours : 0);
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
}
// Register selected courses and return error message, empty string on success
function registerCourses($studentId, $selectedSemester, $selectedCourses) {
    $sqlRegister = "insert into Registration (StudentId, CourseCode, SemesterCode) values(:studentId, :CourseCode, :SemesterCode)";
    $sqlHours = "select sum(WeeklyHours) from Course where CourseCode in (";
    $values = array();
    foreach ($selectedCourses as $ind => $courseCode) {
        $sqlHours .= ($ind != 0 ? ", " : "") . ":" . $courseCode;
        $values[':' . $courseCode] = $courseCode;
    }
    $sqlHours .= ")";
    if (count($selectedCourses) == 0) {
        return "You must select at least one course!";
    }
    $weeklyHours = getWeeklyHours($studentId, $selectedSemester);
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        // Validate weekly hour constraint
        $pStmt = $myPdo->prepare($sqlHours);
        $pStmt->execute($values);
        $newHours = $pStmt->fetch(PDO::FETCH_NUM)[0];
        if ($weeklyHours + $newHours > MAX_WEEKLY_HOURS) {
            return "Your selection exceeds the max weekly hours.";
        }
        // Validation has passed, register selected courses:
        $pStmt = $myPdo->prepare($sqlRegister);
        foreach ($selectedCourses as $course) {
            $pStmt->execute(array(':studentId' => $studentId, ':CourseCode' => $course, ':SemesterCode' => $selectedSemester));
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return "";
}

// Return information on all registered courses for given student
function getRegisteredCourses($studentId) {
    $sql = "select reg.CourseCode as CourseCode, reg.SemesterCode, Course.Title, Course.WeeklyHours FROM `Registration` as reg left join Course on reg.CourseCode = Course.CourseCode WHERE StudentId = :studentId order by reg.SemesterCode";
    $registeredCourses = array();
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':studentId' => $studentId));
        while ($course = $pStmt->fetch(PDO::FETCH_ASSOC)) {
            $registeredCourses[] = array(
                'Year' => '20' . substr($course['SemesterCode'], 0, 2),
                'Term' => SEMESTER_NAMES[substr($course['SemesterCode'], -1, 1)],
                'Code' => $course['CourseCode'],
                'Title' => $course['Title'],
                'Hours' => $course['WeeklyHours']
            );
        }
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
    return $registeredCourses;
}
// Return table row for sum of hours on Current Registration page
function totalHoursRow($hours) {
    return "<tr><td colspan=\"5\" class=\"summaryRow\"><strong>Total Weekly Hours</strong></td><td>{$hours}</td><td></td></tr>";
}
// Remove Registration entities for given student and courses
function unregisterCourses($studentId, $courses) {
    $sql = "delete from Registration where StudentId = :studentId and CourseCode in (";
    if ($courses == null) {
        return "";
    }
    $values = array(':studentId' => $studentId);
    foreach ($courses as $ind => $courseCode) {
        $sql .= ($ind != 0 ? ", " : "") . ":" . $courseCode;
        $values[':' . $courseCode] = $courseCode;
    }
    $sql .= ")";
    try { // Connect to DB with PDO
        $myPdo = getPDO();
        // Execute delete query
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute($values);
    } catch (PDOException $e) {
        //return $e->getMessage();
    }
}
