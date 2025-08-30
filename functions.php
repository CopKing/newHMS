<?php
function pr ($data) {
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}
function safe_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
function prx ($data) {
  echo "<pre>";
  print_r($data);
  echo "</pre>";
  die();
}