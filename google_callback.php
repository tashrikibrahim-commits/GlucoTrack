<?php
require 'config.php';

// Verify state parameter
if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['google_oauth_state'] ?? '')) {
    die("Invalid state parameter. Possible CSRF attack.");
}
unset($_SESSION['google_oauth_state']);

if (!isset($_GET['code'])) {
    header("Location: index.php");
    exit;
}

// Exchange code for access token
$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'code'          => $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($response['access_token'])) {
    header("Location: index.php?error=google_auth_failed");
    exit;
}

// Get user info from Google
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$user_info = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($user_info['id'])) {
    header("Location: index.php?error=google_userinfo_failed");
    exit;
}

$google_id = $user_info['id'];
$email = $user_info['email'] ?? '';
$name = $user_info['name'] ?? $email;
$picture = $user_info['picture'] ?? '';

// Check if user exists by google_id
$stmt = $conn->prepare("SELECT id, full_name, username FROM users WHERE google_id = ?");
$stmt->bind_param("s", $google_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Existing Google user — log them in
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['full_name'] = $row['full_name'];
    $_SESSION['username'] = $row['username'];

    // Update Google photo
    $upd = $conn->prepare("UPDATE users SET google_photo = ? WHERE id = ?");
    $upd->bind_param("si", $picture, $row['id']);
    $upd->execute();
} else {
    // Check if user exists by email/username
    $stmt2 = $conn->prepare("SELECT id, full_name, username FROM users WHERE username = ?");
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows === 1) {
        // Link Google account to existing user
        $row = $result2->fetch_assoc();
        $upd = $conn->prepare("UPDATE users SET google_id = ?, google_photo = ? WHERE id = ?");
        $upd->bind_param("ssi", $google_id, $picture, $row['id']);
        $upd->execute();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['username'] = $row['username'];
    } else {
        // Create new user
        $stmt3 = $conn->prepare("INSERT INTO users (username, full_name, google_id, google_photo, diabetes) VALUES (?, ?, ?, ?, 'No')");
        $stmt3->bind_param("ssss", $email, $name, $google_id, $picture);
        $stmt3->execute();

        $new_id = $conn->insert_id;
        $_SESSION['user_id'] = $new_id;
        $_SESSION['full_name'] = $name;
        $_SESSION['username'] = $email;
    }
}

header("Location: dashboard.php");
exit;
?>
