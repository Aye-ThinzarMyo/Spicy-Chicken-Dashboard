<?php 
session_start();
include 'components/connect.php';

if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $op = validate($_POST['old_password']);
    $np = validate($_POST['new_password']);
    $c_np = validate($_POST['confirm_password']);
    
    if(empty($op)){
        header("Location: change-password.php?error=Old Password is required");
        exit();
    } else if(empty($np)){
        header("Location: change-password.php?error=New Password is required");
        exit();
    } else if($np !== $c_np){
        header("Location: change-password.php?error=The confirmation password does not match");
        exit();
    } else {
        // Hash the passwords
        $op_hashed = password_hash($op, PASSWORD_DEFAULT);
        $np_hashed = password_hash($np, PASSWORD_DEFAULT);
        $id = $_SESSION['id'];

        $stmt = $conn->prepare("SELECT pwd FROM users WHERE id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($op, $user['pwd'])){
            $updateStmt = $conn->prepare("UPDATE users SET pwd=:pwd WHERE id=:id");
            $updateStmt->bindParam(':pwd', $np_hashed);
            $updateStmt->bindParam(':id', $id);
            if ($updateStmt->execute()) {
                header("Location: change-password.php?success=Your password has been changed successfully");
                exit();
            } else {
                header("Location: change-password.php?error=Error updating password");
                exit();
            }
        } else {
            header("Location: change-password.php?error=Incorrect old password");
            exit();
        }
    }
} else {
    header("Location: change-password.php");
    exit();
}
?>
