<?php
require_once 'session.php';

if ($_SERVER['HTTPS'] != "on") {
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
}

sec_session_start();

if (isset($_SESSION['email'])) {
  if (is_session_alive()) {
    echo '<script>alert(\'You are already logged in\');window.location.href = "index.php";</script>';
  } else {
    echo '<script>alert(\'Session timed out, please log in\');</script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirBust Authentication</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/authentication.css">
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="authentication.js" defer></script>
  <script>
    if (!navigator.cookieEnabled) {
      document.write('<style>header, nav, section{display: none}</style>' +
        '<p>Impossible to use the website without cookies enabled.</p>');
    }
  </script>
  <noscript>
    <style>header, nav, section{display: none}</style>
  </noscript>
</head>
<body>
<noscript>
  <p>Impossible to use the website without javascript enabled.</p>
</noscript>
  <header>
    <h1>AirBust Authentication</h1>
  </header>
  <nav>
    <ul>
      <li><button onclick="homepage()">Homepage</button></li>
    </ul>
  </nav>
  <section>
    <h4>If you are already a member Sign in, Register otherwise!</h4>
    <form method="post" action="authentication.php" onsubmit="return validate()">
      <input id="login" type="radio" name="action" value="login" checked>Sign in
      <input id="register" type="radio" name="action" value="register">Register<br/><br/>
      <label for="email">Username: </label>
      <input id="email" type="email" name="email" title="User email" placeholder="john.doe@gmail.com" required><br/><br/>
        <label for="password">Password: </label>
        <input id="password" type="password" name="password"
               title="User password"
               placeholder="********" required><br/><br/>
      <div id="repeatPasswordDiv"></div>
      <input id="submit" type="submit" name="login" value="Sign in">
    </form>
  </section>
  <?php
  if (isset($_GET['error'])) {
    if ($_GET['error'] == 'InvalidEmail') {
      echo '<script>alert(\'Email is not a valid one\')</script>';
    } else if ($_GET['error'] == 'InvalidPassword') {
      echo '<script>alert(\'Password does not match the requested pattern\')</script>';
    } else if ($_GET['error'] == 'PasswordsMismatch') {
      echo '<script>alert(\'The two passwords do not match\')</script>';
    } else if ($_GET['error'] == 'SuccessfullyRegistered') {
      echo '<script>alert(\'Successfully registered\')</script>';
    } else if ($_GET['error'] == 'UserAlreadyRegistered') {
      echo '<script>alert(\'This email already belong to another registered account\')</script>';
    } else if ($_GET['error'] == 'UnregisteredUser') {
      echo '<script>alert(\'This is an unregistered email\')</script>';
    } else if ($_GET['error'] == 'FakeCredentials') {
      echo '<script>alert(\'Incorrect password\')</script>';
    } else if ($_GET['error'] == 'SessionTimeout') {
      echo '<script>alert(\'Session Timed out. Please log in again\')</script>';
    } else if ($_GET['error'] == 'ThreateningInput') {
      echo '<script>alert(\'Threatening input.\')</script>';
    } else if ($_GET['error'] == 'ServerError') {
      echo '<script>alert(\'Server Error.\')</script>';
    }
  }
  ?>
</body>
</html>