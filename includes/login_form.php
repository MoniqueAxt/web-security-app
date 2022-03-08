<?php /// @cond
$username = $_SESSION['uname'] ?? null;
$loggedin = $_SESSION['loggedin'] ?? null;
?>

<!-- LOG-IN SECTION -->
<form id="loginForm" action="../src/login.php" method="POST">
    <div id="login" <?php if ($loggedin): echo "style='display: none'";	endif; ?> >
    <h2>Login</h2>
    <label><b>Username</b></label>
        <label for="uname"></label><input type="text" placeholder="Enter username" name="uname" id="uname"
                                          required maxlength="20" autocomplete="off" >
    <label><b>Password</b></label>
        <label for="psw"></label><input type="password" placeholder="Enter Password" name="psw" id="psw" required maxlength="20" >

    <button type="button" id="loginButton">Login</button>
        <button type="button" id="createAccountButton">Create account</button>
    </div>

    <!-- LOG-OUT SECTION -->
    <div id="logout" <?php if ($loggedin): echo "style='display: block'"; endif; ?> >
    <h2>Logout</h2>
    <?php echo  '<p id="loggedInAsUser"> Logged in as:<br>' . htmlspecialchars($username) .'</p>' ?>
    <button type="button" id="logoutButton">Logout</button>
    </div>
</form>
<?php /// @cond ?>
