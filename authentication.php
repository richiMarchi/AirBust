<?php
  require_once 'db_connection.php';
  require_once 'session.php';

if ($_SERVER['HTTPS'] != "on") {
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
}

sec_session_start();

  $emailNoTags = strip_tags($_POST['email']);
  $pwNoTags = strip_tags($_POST['password']);
  $repPwNoTags = strip_tags($_POST['repeatPassword']);

  $email = htmlentities($emailNoTags);
  $pw = htmlentities($pwNoTags);
  $repPw = htmlentities($repPwNoTags);

if ($email != $_POST['email'] || $pw != $_POST['password'] || $repPw != $_POST['repeatPassword']) {
  header('HTTP/1.1 307 temporary redirect');
  header('Location: authentication_form.php?error=ThreateningInput');
} else {

  if ($_POST['action'] === "register") {

    if (filter_var($email, FILTER_VALIDATE_EMAIL) != $_POST['email']) {
      header('HTTP/1.1 307 temporary redirect');
      header('Location: authentication_form.php?error=InvalidEmail');
    } else if (!(preg_match('/[a-z]/', $pw)
      && (preg_match('/[A-Z]/', $pw)
        || preg_match('/[0-9]/', $pw)))) {
      header('HTTP/1.1 307 temporary redirect');
      header('Location: authentication_form.php?error=InvalidPassword');
    } else if ($pw !== $repPw) {
      header('HTTP/1.1 307 temporary redirect');
      header('Location: authentication_form.php?error=PasswordsMismatch');
    } else {
      $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));

      $password = hash('sha512', $pw . $random_salt);

      $stmt = $conn->prepare("INSERT INTO user(email, password, salt) VALUES (?,?,?)");
      $stmt->bind_param('sss', $email, $password, $random_salt);
      $stmt->execute();
      $conn->commit();
      if ($stmt->affected_rows > 0) {
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        $_SESSION['email'] = $email;

        header('HTTP/1.1 307 temporary redirect');
        header('Location: index.php?msg=Successfully+registered');
      } else {
        header('HTTP/1.1 307 temporary redirect');
        header('Location: authentication_form.php?error=UserAlreadyRegistered');
      }
    }
  } else {

    $password = hash('sha512', $pw);

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
      header('HTTP/1.1 307 temporary redirect');
      header('Location: authentication_form.php?error=ServerError');
      exit;
    }
    $res = $stmt->get_result();
    if ($stmt->affected_rows > 0) {
      $row = $res->fetch_assoc();
      if ($row['password'] === hash('sha512', $pw . $row['salt'])) {

        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        $_SESSION['email'] = $email;

        header('HTTP/1.1 307 temporary redirect');
        header('Location: index.php?msg=Successfully+logged+in');
      } else {
        header('HTTP/1.1 307 temporary redirect');
        header('Location: authentication_form.php?error=FakeCredentials');
      }
    } else {
      header('HTTP/1.1 307 temporary redirect');
      header('Location: authentication_form.php?error=UnregisteredUser');
    }
  }
}
?>