<?php
require_once 'db_connection.php';
require_once 'session.php';

if ($_SERVER['HTTPS'] != "on") {
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
}

sec_session_start();

if (isset($_SESSION['email']) && !is_session_alive()) {
  $data = array(
    'successful' => false,
    'msg' => 'SESSION_TIMEOUT'
  );
  echo json_encode($data);
} else {

  $seatsNoTags = strip_tags($_POST['seats']);
  $seats = htmlentities($seatsNoTags);

  if ($seats != $_POST['seats']) {
    $data = array(
      'successful' => false,
      'msg' => 'THREATENING_INPUT'
    );
    echo json_encode($data);
  } else {

    $oldStatus = 0;
    $newStatus = 1;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservation WHERE user = ? AND status = ? FOR UPDATE");
    $stmt->bind_param("si", $_SESSION['email'], $oldStatus);
    if (!$stmt->execute()) {
      $data = array(
        'successful' => false,
        'msg' => 'SERVER_ERROR'
      );
      echo json_encode($data);
    } else {
      $stmt->bind_result($res);
      $stmt->store_result();
      $stmt->fetch();
      if ($_POST['seats'] == $res) {
        $stmt = $conn->prepare("UPDATE reservation SET status = ? WHERE status = ? AND user = ?");
        $stmt->bind_param("iis", $newStatus, $oldStatus, $_SESSION['email']);
        if (!$stmt->execute()) {
          $data = array(
            'successful' => false,
            'msg' => 'SERVER_ERROR'
          );
          echo json_encode($data);
        } else {
          $conn->commit();
          $data = array(
            'successful' => true,
            'msg' => 'PURCHASE_OK'
          );
          echo json_encode($data);
        }
      } else {
        $stmt = $conn->prepare("DELETE FROM reservation WHERE status = ? AND user = ?");
        $stmt->bind_param("is", $oldStatus, $_SESSION['email']);
        if (!$stmt->execute()) {
          $data = array(
            'successful' => false,
            'msg' => 'SERVER_ERROR'
          );
          echo json_encode($data);
        } else {
          $conn->commit();
          $data = array(
            'successful' => false,
            'msg' => 'PURCHASE_FAIL'
          );
          echo json_encode($data);
        }
      }
    }
  }
}
?>