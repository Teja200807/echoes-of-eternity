<?php
// Include database connection
require_once 'includes/db_connect.php';

// Set your new password here
$new_password = "MyNewAdminPassword123"; // <-- Change this to what you want!
$username_to_change = "admin";

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the password in the database
$sql = "UPDATE users SET password = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $username_to_change);

if ($stmt->execute()) {
    echo "<h3>Success!</h3>";
    echo "Password for user <strong>" . $username_to_change . "</strong> has been updated to: <strong>" . $new_password . "</strong>";
    echo "<br><br><a href='login.html'>Click here to login</a>";
} else {
    echo "Error updating password: " . $conn->error;
}

$stmt->close();
$conn->close();
?>