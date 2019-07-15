<?php
  require_once 'db_connection.php';
  require_once 'session.php';

if ($_SERVER['HTTPS'] != "on") {
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
}

sec_session_start();

if (isset($_SESSION['email']) && !is_session_alive()) {
  echo '<script>alert(\'Session timed out, please log in\');window.location.href = "index.php";</script>';
}

if (isset($_GET['msg'])) {
  echo '<script>alert(\'' . $_GET['msg'] . '\');</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>AirBust Homepage</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/index.css">
      <script src="lib/jquery-3.4.1.min.js"></script>
      <script src="index.js"></script>
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
      <h1>AirBust</h1>
    </header>
    <nav>
        <ul>
          <li><button onclick="homepage()">Homepage</button></li>
          <?php
            if (!isset($_SESSION['email'])) {
              echo '<li><button onclick="authenticationForm()">Login</button></li>';
            } else {
              echo '<li><button onclick="updateSeats()">Update</button></li><li><button onclick="logout()">Logout</button></li>';
            }
            ?>
          <li><button id='buyButton' onclick="buySeats()">Buy</button></li>
        </ul>
    </nav>
    <section>
      <h2 class="totSeats">Total Seats: </h2><h2 class="totSeats" id="totSeats"><?php echo ROWS * SEATS_PER_ROW ?></h2><br>
      <h2 class="purchSeats">Purchased Seats: </h2><h2 class="purchSeats" id="purchSeats"></h2><br>
      <h2 class="resSeats">Reserved Seats: </h2><h2 class="resSeats" id="resSeats"></h2><br>
      <h2 class="freeSeats">Free Seats: </h2><h2 class="freeSeats" id="freeSeats"></h2>
      <table>
        <?php
        for ($r = 1; $r <= ROWS; $r++) {
          for ($i = ord('A'); $i < ord('A') + SEATS_PER_ROW; $i++) {
            echo '<td><div id="' . chr($i) . $r . '" onclick="seatClicked(this)">
                    <img src="images/seat.png" alt="Seat image">
                    <div class="centered">' . chr($i) . $r . '
                    </div>
                </div></td>';
          }
          echo '</tr>';
        }
        ?>
      </table>
    </section>
  </body>
</html>
