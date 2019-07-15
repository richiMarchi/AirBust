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
  $stmt = $conn->prepare("SELECT * FROM reservation");
  if (!$stmt->execute()) {
    $data = array(
      'successful' => false,
      'msg' => 'SERVER_ERROR'
    );
    echo json_encode($data);
  } else {
    $res = $stmt->get_result();
    $reservations = [];
    while ($row = $res->fetch_assoc()) {
      array_push($reservations, $row);
    }

    $data = array(
      'successful' => true,
      'userReq' => isset($_SESSION['email']) ? $_SESSION['email'] : 'none',
      'rows' => ROWS,
      'seats_per_row' => SEATS_PER_ROW,
      'reservations' => $reservations
    );
    echo json_encode($data);
  }
}
?>