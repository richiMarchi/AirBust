<?php
require_once 'db_connection.php';
require_once 'session.php';

if ($_SERVER['HTTPS'] != "on") {
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
}

sec_session_start();

if (!isset($_SESSION['email'])) {
  echo false;
} else {
  if (isset($_SESSION['email']) && !is_session_alive()) {
    $data = array(
      'successful' => false,
      'msg' => 'SESSION_TIMEOUT'
    );
    echo json_encode($data);
  } else {

    $seatNoTags = strip_tags($_POST['seat']);
    $actionNoTags = strip_tags($_POST['action']);

    $seat = htmlentities($seatNoTags);
    $action = htmlentities($actionNoTags);

    if ($seat != $_POST['seat'] || $action != $_POST['action']) {
      $data = array(
        'successful' => false,
        'msg' => 'THREATENING_INPUT'
      );
      echo json_encode($data);
    } else {

      if ($_POST['action'] === "delete") {
        $stmt = $conn->prepare("DELETE FROM reservation WHERE seat = ? AND user = ?");
        $stmt->bind_param("ss", $_POST['seat'], $_SESSION['email']);
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
            'msg' => 'UNRESERVED'
          );
          echo json_encode($data);
        }
      } else {
        $stmt = $conn->prepare("SELECT * FROM reservation WHERE seat = ? FOR UPDATE");
        $stmt->bind_param("s", $_POST['seat']);
        if (!$stmt->execute()) {
          $data = array(
            'successful' => false,
            'msg' => 'SERVER_ERROR'
          );
          echo json_encode($data);
        } else {
          $res = $stmt->get_result();
          if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['status'] == 0) {
              $stmt = $conn->prepare("UPDATE reservation SET user = ? WHERE seat = ?");
              $stmt->bind_param('ss', $_SESSION['email'], $_POST['seat']);
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
                  'msg' => "RESERVE_STEAL"
                );
                echo json_encode($data);
              }
            } else {
              $conn->commit();
              $data = array(
                'successful' => false,
                'msg' => "RESERVE_FAIL"
              );
              echo json_encode($data);
            }
          } else {
            $line = (int)(substr($_POST['seat'], 1, strlen($_POST['seat']) - 1));
            $column = ord((substr($_POST['seat'], 0, 1)));
            if (!preg_match('/[A-Z]\d+/', $_POST['seat'])
              || (0 > $line)
              || (ROWS < $line)
              || (ord('A') > $column)
              || (ord('A') + SEATS_PER_ROW - 1 < $column)) {
              $conn->commit();
              $data = array(
                'successful' => false,
                'msg' => "RANGE_ERROR"
              );
              echo json_encode($data);
            } else {
              $stmt = $conn->prepare("INSERT INTO reservation(seat, user, status) VALUES (?,?,?)");
              $status = 0;
              $stmt->bind_param('ssi', $_POST['seat'], $_SESSION['email'], $status);
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
                  'msg' => "RESERVE_OK"
                );
                echo json_encode($data);
              }
            }
          }
        }
      }
    }
  }
}
?>