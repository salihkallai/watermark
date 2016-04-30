<?php

session_start();

require_once __DIR__ . '/fbsdk/src/Facebook/autoload.php';

$fb = new Facebook\Facebook(['app_id' => '829705353744955',
  'app_secret' => '22552ebd3b59bea9801dd9415d60042b',
  'default_graph_version' => 'v2.5',]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes', 'publish_actions', 'user_photos']; // optional
$loginUrl = $helper->getLoginUrl('http://localhost:80/login-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>