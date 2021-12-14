<?php

session_start(); 
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APP_NAME</title>
    <link rel="stylesheet" href="public/stylesheets/lib/bootstrap.css">
    <link rel="stylesheet" href="public/stylesheets/lib/semantic.min.css">
    <link rel="stylesheet" href="public/stylesheets/confirm_notif_window.css">
    <link rel="stylesheet" href="public/stylesheets/style.css">
</head>
<body>
  <section class="showcase">
    <header>
      <a href="menu_principal.php">
          <h2 class="logo">APP_NAME</h2>
      </a>
      <div class="toggle"></div>
    </header>
    <!-- <video src="public\stylesheets\background_video\WKU_Flyover.mp4" muted loop autoplay></video> -->
    <div class="overlay"></div>

