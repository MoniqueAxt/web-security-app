<?php
/**
 * index.php
 *
 * @brief   UI HTML webpage that displays the homepage of the web application.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php');  // don't put anything before this (starts $SESSION)

if (empty($_SESSION['login_token'])) {
    try {
        $_SESSION['login_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['login_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$loginToken = $_SESSION['login_token'];

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once('includes/head.php') ?>
</head>
<body>
<input  id="token" type="hidden" value=<?php echo $_SESSION['login_token'] ?>>
<header>
  <?php include_once 'includes/header.php' ?>
</header>
<nav id="nav_links">
    <?php printLinks("index.php"); ?>
</nav>
<main>
    <aside id="login_aside">
        <?php include_once('includes/login_form.php'); ?>
    </aside>
    <section id="content_section">
        <h2>Home page</h2>
        <p>This is a private forum!
            If you are logged in, you will see a link to the userpage
            where you are able to see your profile and post on the forum.

            Enjoy!</p>
        <p id="messageToUser"></p>
    </section>
</main>

<footer>
    <?php include_once('includes/footer.html'); ?>
</footer>
</body>
</html>
<?php /// @endcond ?>