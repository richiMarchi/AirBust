<?php
require_once 'macro.php';

$conn = new mysqli(HOST, USER, PASSWORD, USER);
$conn->autocommit(false);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>