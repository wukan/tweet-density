<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

//$twitter_user = 'alunwk';
$twitter_user = 'reconditesea';
if (!empty($_GET['twitter_user'])) {
  $twitter_user = $_GET['twitter_user'];
}

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/verify_credentials');

/* Some example calls */
$result = $connection->get('users/show', array('screen_name' => $twitter_user));
$statuses_count = $result->statuses_count;
// There are up to 200 tweets you can retrive one time
$page_num = ceil($statuses_count / 200.0);
$hours = array();

for ($i = 0; $i < 24; $i++) $hours[$i] = 0;
for ($i = 0; $i < $page_num; $i++) {
  // There are up to 3200 tweets per user you can retrieve
  if ($i >= 16) break;
  $result = $connection->get('statuses/user_timeline', 
    array('screen_name' => $twitter_user, 'count' => 200, 'include_rts' => 1));
  foreach ($result as $tweet) {
    $hour = intval(date('G', strtotime($tweet->created_at)));
    $hours[$hour] += 1;
  }
}

$content = $hours;

/* Include HTML to display on the page */
include('html.inc');
