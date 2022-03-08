<?php
/**
 * memberpage.php
 *
 * @brief   UI HTML webpage that displays the member-page of the web application.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once($_SERVER['DOCUMENT_ROOT'] . '/config/util.php'); // don't put anything before this (starts $SESSION)

$loggedin = $_SESSION['loggedin'] ?? null;
/** Re-direct browser if user is not logged-in*/
if (!$loggedin) {
    header("Location: index.php");
    exit;

    /** Otherwise, set the username */
} else {
    $user = "for " . strtoupper($_SESSION['uname']);
}

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="sv-SE">
<head>
<?php include_once('includes/head.php'); ?>
</head>
<body>
<header>
    <?php include_once('includes/header.php') ?>
</header>
<nav id="nav_links">
    <?php printLinks('memberpage.php'); ?>
</nav>
<main>
    <aside>
        <?php include_once('includes/login_form.php'); ?>
    </aside>
    <section>
        <h2>User page <?php echo htmlspecialchars($user) ?></h2>
            This is your userpage.
        <p id="messageToUser"></p>
    </section>
</main>
<footer>
    <?php include_once('includes/footer.html'); ?>
</footer>
</body>
</html>
<?php /// @endcond ?>