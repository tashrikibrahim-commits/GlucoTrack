<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id']; $full_name = $_SESSION['full_name']; $message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fn = trim($_POST['full_name']); $age = (int)$_POST['age']; $gender = $_POST['gender'];
    $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
    $bloodgroup = $_POST['bloodgroup']; $diabetes = $_POST['diabetes'];
    $photo_path = $_POST['old_photo'] ?? null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $dir = "uploads/"; if (!is_dir($dir)) mkdir($dir, 0777, true);
        $target = $dir . time() . "_" . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) $photo_path = $target;
    }
    $stmt = $conn->prepare("UPDATE users SET full_name=?, age=?, gender=?, weight=?, bloodgroup=?, diabetes=?, photo=? WHERE id=?");
    $stmt->bind_param("sisdsssl", $fn, $age, $gender, $weight, $bloodgroup, $diabetes, $photo_path, $user_id);
    if ($stmt->execute()) { $_SESSION['full_name'] = $fn; $full_name = $fn; $message = "success"; }
}

$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
$diabetes_status = $user['diabetes'] ?? 'No';
$profileImg = $user['photo'] ?: ($user['google_photo'] ?? ($user['gender']=='Female' ? 'https://i.pravatar.cc/150?u=female' : 'https://i.pravatar.cc/150?u=male'));
$current_page = 'profile';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head><?php include 'header.php'; ?><title>Profile — GlucoTrack</title></head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'sidebar.php'; ?><?php include 'topbar.php'; ?>
    <main class="flex-1 md:ml-64 mt-16 overflow-y-auto p-container-padding">
        <div class="max-w-2xl mx-auto">
            <div class="mb-xl"><h2 class="font-h2 text-h2 t-text mb-2">My Profile</h2><p class="font-body-lg text-body-lg t-text-secondary">Manage your personal information.</p></div>
            <?php if ($message === 'success'): ?>
                <div class="bg-primary-container/10 border border-primary-container/20 text-primary px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span>Profile updated!</div>
            <?php endif; ?>
            <div class="t-card border rounded-xl p-lg">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="old_photo" value="<?= htmlspecialchars($user['photo'] ?? '') ?>">
                    <div class="flex items-center gap-6 pb-6 border-b t-border">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 t-border flex-shrink-0">
                            <img src="<?= htmlspecialchars($profileImg) ?>" class="w-full h-full object-cover" alt="Profile">
                        </div>
                        <div>
                            <input type="file" name="photo" accept="image/*" class="text-sm t-text-muted file:t-elevated file:border-0 file:rounded-lg file:px-4 file:py-2 file:t-text file:mr-3 file:cursor-pointer">
                            <?php if ($user['google_photo']): ?><p class="font-body-sm t-text-muted mt-2 flex items-center gap-1"><svg width="14" height="14" viewBox="0 0 48 48"><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/></svg>Linked with Google</p><?php endif; ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2"><label class="block font-label-caps text-label-caps t-text-muted mb-2">Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                        <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Age</label><input type="number" name="age" value="<?= $user['age'] ?>" required class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                        <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Gender</label><select name="gender" class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><option value="Male" <?= $user['gender']=='Male'?'selected':'' ?>>Male</option><option value="Female" <?= $user['gender']=='Female'?'selected':'' ?>>Female</option><option value="Other" <?= $user['gender']=='Other'?'selected':'' ?>>Other</option></select></div>
                        <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Weight (kg)</label><input type="number" step="0.1" name="weight" value="<?= $user['weight'] ?>" class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></div>
                        <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Blood Group</label><select name="bloodgroup" class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><?php foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?><option value="<?= $bg ?>" <?= $user['bloodgroup']==$bg?'selected':'' ?>><?= $bg ?></option><?php endforeach; ?></select></div>
                        <div><label class="block font-label-caps text-label-caps t-text-muted mb-2">Diabetes</label><select name="diabetes" class="w-full t-input border rounded-lg px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><option value="Yes" <?= $user['diabetes']=='Yes'?'selected':'' ?>>Yes</option><option value="No" <?= $user['diabetes']=='No'?'selected':'' ?>>No</option></select></div>
                    </div>
                    <button type="submit" class="w-full bg-primary text-on-primary font-body-md font-semibold py-3 rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2"><span class="material-symbols-outlined text-sm">save</span>Save Changes</button>
                </form>
            </div>
        </div>
    </main>
</body></html>