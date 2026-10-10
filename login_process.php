<?php
// Include database connection
require_once 'includes/db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        echo "Please fill all fields";
        exit();
    }
    
    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Return appropriate response based on role
            if ($user['role'] == 'admin') {
                echo "admin_success";
            } else {
                echo "user_success";
            }
            
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    // If someone tries to access this file directly
    header("Location: login.html");
    exit();
}
?>