<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
 
	session_start();

require_once __DIR__ . '/fbsdk/src/Facebook/autoload.php';

$facebook = new Facebook\Facebook(['app_id' => '829705353744955',
  'app_secret' => '22552ebd3b59bea9801dd9415d60042b',
  'default_graph_version' => 'v2.5',]);
 
	$user_id = $facebook->getUser();
 
	if($user_id == 0 || $user_id == "")
	{
		$login_url = $facebook->getLoginUrl(array(
		'redirect_uri'         => "http://apps.facebook.com/rapid-apps/",
		'scope'      => "email,publish_stream,user_hometown,user_location,user_photos,friends_photos,
					user_photo_video_tags,friends_photo_video_tags,user_videos,video_upload,friends_videos"));
 
		echo "<script type='text/javascript'>top.location.href = '$login_url';</script>";
		exit();
	}
 
	//get profile album
	$albums = $facebook->api("/me/albums");
	$album_id = ""; 
	foreach($albums["data"] as $item){
		if($item["type"] == "profile"){
			$album_id = $item["id"];
			break;
		}
	}
 
	//set photo atributes
	$full_image_path = realpath("Koala.jpg");
	$args = array('message' => 'Uploaded by 4rapiddev.com');
	$args['image'] = '@' . $full_image_path;
 
	//upload photo to Facebook
	$data = $facebook->api("/{$album_id}/photos", 'post', $args);
	$pictue = $facebook->api('/'.$data['id']);
 
	$fb_image_link = $pictue['link']."&makeprofile=1";
 
	//redirect to uploaded photo url and change profile picture
	echo "<script type='text/javascript'>top.location.href = '$fb_image_link';</script>";
?>