<?php
// Include database connection
require_once 'includes/db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo "Please fill all fields";
        exit();
    }
    
    if ($password !== $confirm_password) {
        echo "Passwords do not match";
        exit();
    }
    
    if (strlen($password) < 6) {
        echo "Password must be at least 6 characters";
        exit();
    }
    
    // Check if username already exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "Username already exists";
        $check_stmt->close();
        $conn->close();
        exit();
    }
    $check_stmt->close();
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user (default role is 'user')
    $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ss", $username, $hashed_password);
    
    if ($insert_stmt->execute()) {
        echo "success";
    } else {
        echo "Registration failed: " . $conn->error;
    }
    
    $insert_stmt->close();
    $conn->close();
    
} else {
    header("Location: register.html");
    exit();
}
?>