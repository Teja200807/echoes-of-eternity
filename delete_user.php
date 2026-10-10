<?php
// Include database connection
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

$id = $_GET['id'];

// Don't allow deleting yourself
if ($id == $_SESSION['user_id']) {
    header("Location: admin/dashboard.php?error=cannot_delete_self");
    exit();
}

// Check if user exists and is not admin
$check_sql = "SELECT role FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    // Don't allow deleting admin users
    if ($user['role'] == 'admin') {
        header("Location: admin/dashboard.php?error=cannot_delete_admin");
        exit();
    }
    
    // Delete the user
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    
    if ($delete_stmt->execute()) {
        header("Location: admin/dashboard.php?success=user_deleted");
    } else {
        header("Location: admin/dashboard.php?error=delete_failed");
    }
    
    $delete_stmt->close();
} else {
    header("Location: admin/dashboard.php?error=user_not_found");
}

$check_stmt->close();
$conn->close();
?>