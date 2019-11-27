<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" style="padding: 10px" href="http://www.algonquincollege.com">
                <img src="/AlgCommon/Contents/img/AC.png" alt="Algonquin College" style="max-width:100%; max-height:100%;"/>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="Index.php">Home</a></li>
                <li><a href="CourseSelection.php">Course Selection</a></li>
                <li><a href="CurrentRegistration.php">Current Registration</a></li>
                <?php 
                    if (isset($_SESSION["studentId"])) {
print<<<LogoutNavLink
                        <li><a href="Logout.php">Log out</a></li>
LogoutNavLink;
                    } else {
print<<<LoginNavLink
                        <li><a href="Login.php">Log in</a></li>
LoginNavLink;
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>