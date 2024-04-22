<?php
// session_start();
// include 'components/connect.php'; // Include your database connection file

// if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         // Validate inputs
//         $oldPassword = $_POST['old_password'];
//         $newPassword = $_POST['new_password'];
//         $confirmPassword = $_POST['confirm_password'];

//         if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
//             $error = "All fields are required.";
//         } else if ($newPassword !== $confirmPassword) {
//             $error = "New password and confirm password do not match.";
//         } else {
//             // Hash the passwords
//             $oldPasswordHashed = password_hash($oldPassword, PASSWORD_DEFAULT);
//             $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

//             // Verify old password
//             $userId = $_SESSION['user_id'];
//             $stmt = $conn->prepare("SELECT pwd FROM users WHERE id = :id");
//             $stmt->bindParam(':id', $userId);
//             $stmt->execute();
//             $user = $stmt->fetch(PDO::FETCH_ASSOC);

//             if ($user && password_verify($oldPassword, $user['pwd'])) {
//                 // Update password
//                 $updateStmt = $conn->prepare("UPDATE users SET pwd = :pwd WHERE id = :id");
//                 $updateStmt->bindParam(':password', $newPasswordHashed);
//                 $updateStmt->bindParam(':id', $userId);
//                 $updateStmt->execute();
                
//                 $success = "Password changed successfully.";
//             } else {
//                 $error = "Incorrect old password.";
//             }
//         }
//     } 

// } ?> 
//  else {
//     header("Location: login.php");
//     exit();
// }
// 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <!-- Add your CSS links here -->
</head>
<body>
    <!-- Your HTML form for changing password goes here -->
    <form action="change-p.php" method="POST">
        <!-- Display error message if any -->
        <?php if (isset($error)) { ?>
            <p><?php echo $error; ?></p>
        <?php } ?>
        
        <!-- Display success message if any -->
        <?php if (isset($success)) { ?>
            <p><?php echo $success; ?></p>
        <?php } ?>
        
        <label for="old_password">Old Password:</label><br>
        <input type="password" id="old_password" name="old_password" required><br>
        
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br>
        
        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
