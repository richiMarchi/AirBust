<?php
require_once 'macro.php';

function is_session_alive() {

  $t = time();
  $diff = 0;
  if (isset($_SESSION['time'])) {
    $diff = $t - $_SESSION['time'];
  }

  if ($diff > SESSION_VALIDITY_SECONDS) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 3600 * 24,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );
    }
    session_destroy();
    return false;
  } else {
      $_SESSION['time'] = time();
    return true;
  }
}

function sec_session_start() {
  $session_name = 'sec_session_id';
  // True if HTTPS needed.
  $secure = true;
  // Prevent JS from accessing session params.
  $httponly = true;
  // Force session to use cookies only.
  ini_set('session.use_only_cookies', 1);
  $cookieParams = session_get_cookie_params();
  session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
  session_name($session_name);
  session_start();
  // Regenerate a new session_id
  session_regenerate_id();
}
?>