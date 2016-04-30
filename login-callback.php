<?php
session_start();
require_once __DIR__ . '/fbsdk/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '829705353744955',
  'app_secret' => '22552ebd3b59bea9801dd9415d60042b',
  'default_graph_version' => 'v2.3',
  // . . .
  ]);
  


$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken('http://localhost:80/login-callback.php');
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  // Logged in!
  $_SESSION['facebook_access_token'] = (string) $accessToken;
  //echo "Logged in!<br/>";

  // Now you can redirect to another page and use the
   // access token from $_SESSION['facebook_access_token']
   try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name,email', $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();

$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($user, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        //echo "$key:\n";
    } else {
        //echo "$key => $val\n";
		if($key=="id")
		{
			$id=$val;
		}
		if($key=="name")
		{
			$name=$val;
		}
    }
}

//echo $id;
//echo "<a href='http://graph.facebook.com/$id/picture'>pic</a>";

$URL = "http://graph.facebook.com/$id/picture?type=large";

$headers = get_headers($URL, 1); // make link request and wait for redirection
    if(isset($headers['Location'])) {
      $URL = $headers['Location']; // this gets the new url
    }
    $url_arr = explode ('/',$URL);
    $ct = count($url_arr);
    $name = $url_arr[$ct-1];
    $name_div = explode('.', $name);
    $ct_dot = count($name_div);
    $img_type = $name_div[$ct_dot -1];
    $pos = strrpos($img_type, "&");
    if($pos)
    {
        $pieces = explode("&", $img_type);
        $img_type = $pieces[0];

    }

    $imagename = 'miku.'.$img_type;
    $content = file_get_contents($URL);
    //file_put_contents("$imagename", $content);
	$file = fopen('gh.jpg', 'w+');
    fputs($file, $content);
    fclose($file);


	
	// Load the stamp and the photo to apply the watermark to
$stamp = imagecreatefrompng('facebook.png');
$im = imagecreatefromjpeg('gh.jpg');

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 10;
$marge_bottom = 10;
$sx = imagesx($stamp);
$sy = imagesy($stamp);

// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));


// Output and free memory
//header('Content-type: image/png');

imagepng($im,'bash.png');
imagejpeg($im, 'water mark.jpg');

imagedestroy($im);
//copy($im,"wm.jpg");copy($im,'bash.jpg');
header('Location: http://localhost/changeProfilePic.php');


}


	 
?>