<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

if (!empty($_GET['handle'])) {
  $twitter_user = $_GET['handle'];
} else {
  $twitter_user = NULL;
}

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || 
  empty($_SESSION['access_token']['oauth_token']) || 
  empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET,
  $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
// $content = $connection->get('account/verify_credentials');

if (!is_null($twitter_user)) {
  $result = $connection->get(
    'users/show', array('screen_name' => $twitter_user));
  $statuses_count = 0;
  if (!empty($_GET['count'])) {
    $statuses_count = floatval($_GET['count']);
  } else if (property_exists($result, 'statuses_count')) {
    $statuses_count = $result->statuses_count;
  }
  // There are up to 200 tweets you can retrive one time
  $page_num = ceil($statuses_count / 200.0);
  $hours = array();
  for ($i = 0; $i < 24; $i++) $hours[$i] = 0;
  for ($i = 0; $i < $page_num; $i++) {
    // There are up to 3200 tweets per user you can retrieve
    if ($i >= 16) break;
    $result = $connection->get('statuses/user_timeline', 
      array('screen_name' => $twitter_user, 
            'count' => 200,
            'include_rts' => 1));
    foreach ($result as $tweet) {
      $hour = intval(date('G', strtotime($tweet->created_at)));
      $hours[$hour] += 1;
    }
  }
}

$type = 'html';
if (isset($_GET['type'])) {
  $type = $_GET['type']; 
} 
if ($type == 'xml') {
  header('Content-type: text/xml');
  $xmlstr = '<tweetdensity>';
  foreach ($hours as $hour => $tweet) {
    $xmlstr .= "<data><hour>$hour</hour><count>$tweet</count></data>";
  }
  $xmlstr .= '</tweetdensity>';
  echo $xmlstr;
} else if ($type == 'json') {
  header('Content-type: application/json');
  $tweet_density = array();
  $tweet_density['tweetdensity'] = array();
  $tweet_density['tweetdensity']['data'] = array();
  foreach ($hours as $hour => $tweet) {
    $tweet_density['tweetdensity']['data'][$hour]['hour'] = $hour;
    $tweet_density['tweetdensity']['data'][$hour]['count'] = $tweet;
  }
  echo json_encode($tweet_density);
} else {
  /* Include HTML to display on the page */
  include('html.inc');
}

