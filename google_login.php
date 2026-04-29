<?php
require 'config.php';

// Generate CSRF state token
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

$params = http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'email profile openid',
    'access_type'   => 'online',
    'prompt'        => 'consent',
    'state'         => $state
]);

header("Location: https://accounts.google.com/o/oauth2/v2/auth?$params");
exit;
?>
