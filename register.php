<?php
require 'config.php';
require_once 'logo.php';
if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit(); }
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); $full_name = trim($_POST['full_name']); $password = trim($_POST['password']);
    $age = (int)$_POST['age']; $gender = $_POST['gender']; $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
    $bloodgroup = $_POST['bloodgroup']; $diabetes = $_POST['diabetes']; $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0 && $_FILES['photo']['size'] > 0) {
        $tmpFile = $_FILES['photo']['tmp_name'];
        $mime = mime_content_type($tmpFile);
        if (in_array($mime, ['image/jpeg','image/png','image/gif','image/webp'])) {
            $photo_path = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($tmpFile));
        }
    }
    $stmt = $conn->prepare("INSERT INTO users (username, full_name, password, age, gender, weight, bloodgroup, diabetes, photo) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssisssss", $username, $full_name, $password, $age, $gender, $weight, $bloodgroup, $diabetes, $photo_path);
    $message = $stmt->execute() ? "success" : "error";
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head><?php include 'header.php'; ?><title>Register — GlucoTrack</title></head>
<body class="flex items-center justify-center min-h-screen py-12 selection:bg-primary/30">
    <div class="fixed inset-0 pointer-events-none"><div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-primary/5 rounded-full blur-3xl"></div></div>
    <div class="w-full max-w-lg px-6 relative z-10">
        <div class="t-card border rounded-xl p-10">
            <div class="flex flex-col items-center mb-8">
                <span class="text-primary mb-3"><?= logoSVG(40) ?></span>
                <h1 class="font-h2 text-h2 t-text tracking-tighter mb-1">GlucoTrack</h1>
                <p class="font-label-caps text-label-caps t-text-muted uppercase tracking-widest">Create Account</p>
            </div>
            <?php if ($message === 'success'): ?>
                <div class="bg-primary-container/10 border border-primary-container/20 text-primary px-4 py-3 rounded-lg mb-6 text-center font-body-sm flex items-center justify-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span>Registration successful! <a href="index.php" class="underline ml-1">Login now →</a></div>
            <?php elseif ($message === 'error'): ?>
                <div class="bg-error-container/20 border border-error-container/30 text-error px-4 py-3 rounded-lg mb-6 text-center font-body-sm">Username already exists.</div>
            <?php endif; ?>
            <a href="google_login.php" class="w-full t-elevated border t-border hover:border-primary/30 t-text font-body-md font-medium py-3 rounded-lg flex items-center justify-center gap-3 transition-all duration-200 mb-6">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Sign up with Google
            </a>
            <div class="flex items-center gap-4 mb-6"><div class="flex-1 h-px t-border border-t"></div><span class="font-label-caps text-label-caps t-text-muted uppercase">or register manually</span><div class="flex-1 h-px t-border border-t"></div></div>
            <form method="POST" enctype="multipart/form-data" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Full Name</label><input type="text" name="full_name" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Username</label><input type="text" name="username" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                </div>
                <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Password</label><input type="password" name="password" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Age</label><input type="number" name="age" value="25" min="1" max="120" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Gender</label><select name="gender" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Weight (kg)</label><input type="number" step="0.1" name="weight" placeholder="68.5" class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Blood Group</label><select name="bloodgroup" class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><option value="">Select</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>O+</option><option>O-</option><option>AB+</option><option>AB-</option></select></div>
                    <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Diabetes</label><select name="diabetes" required class="w-full t-input border rounded-lg px-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><option value="Yes">Yes</option><option value="No">No</option></select></div>
                </div>
                <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Profile Photo (optional)</label><input type="file" name="photo" accept="image/*" class="w-full text-sm t-text-muted file:t-elevated file:border-0 file:rounded-lg file:px-4 file:py-2 file:t-text file:mr-3 file:cursor-pointer"></div>
                <button type="submit" class="w-full bg-primary text-on-primary font-body-md font-semibold py-3 rounded-lg hover:opacity-90 transition-all">Register</button>
            </form>
            <div class="text-center mt-6"><a href="index.php" class="text-primary hover:underline font-body-sm">Already have an account? Login →</a></div>
        </div>
    </div>
</body></html>