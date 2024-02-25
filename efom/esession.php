<?php
  session_start();

  if (isset($_SESSION['token'])) { 
    $_SESSION['token'] = $_SESSION['token'];
  }
?>