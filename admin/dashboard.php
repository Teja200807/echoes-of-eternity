<?php
// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

// Get total users count
$count_sql = "SELECT COUNT(*) as total FROM users";
$count_result = $conn->query($count_sql);
$total_users = $count_result->fetch_assoc()['total'];

// Get all users
$users_sql = "SELECT id, username, role, created_at FROM users ORDER BY id DESC";
$users_result = $conn->query($users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Echoes of Eternity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy your admin.html CSS here - the same styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, #4a148c 0%, #311b92 100%);
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
        }
        
        header h3 i {
            color: #ffd700;
        }
        
        header button {
            background: #ff6b35;
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        header button:hover {
            background: #ff8c42;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,107,53,0.4);
        }
        
        .container {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: #ff6b35;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #4a148c;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .table-header h2 {
            color: #4a148c;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-header h2 i {
            color: #ff6b35;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(135deg, #4a148c 0%, #311b92 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .delete-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .delete-btn:hover {
            background: #cc0000;
            transform: scale(1.05);
        }
        
        .admin-badge {
            background: #4caf50;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        .user-badge {
            background: #ff6b35;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .back-to-site {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(45deg, #4a148c, #764ba2);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .back-to-site:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            td, th {
                padding: 10px;
            }
            
            .back-to-site {
                bottom: 20px;
                right: 20px;
                padding: 12px 20px;
                font-size: 0.9rem;
            }
        }
        
        .welcome-message {
            background: #f0f0f0;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <h3>
        <i class="fas fa-crown"></i>
        Admin Dashboard
    </h3>
    <a href="../logout.php" style="text-decoration: none;">
        <button>
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </a>
</header>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-number"><?php echo $total_users; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-map-marked-alt"></i>
            <div class="stat-number">12</div>
            <div class="stat-label">Tourist Places</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-temple"></i>
            <div class="stat-number">6</div>
            <div class="stat-label">Temples</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-mountain"></i>
            <div class="stat-number">6</div>
            <div class="stat-label">Nature Spots</div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>
                <i class="fas fa-user-circle"></i>
                Registered Users
            </h2>
            <div class="welcome-message">
                Welcome, <strong><?php echo $_SESSION['username']; ?></strong> (Admin)
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Registered Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while($user = $users_result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td>
                        <i class="fas fa-user" style="color: #4a148c; margin-right: 8px;"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td>
                        <?php if($user['role'] == 'admin'): ?>
                            <span class="admin-badge">Admin</span>
                        <?php else: ?>
                            <span class="user-badge">User</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if($user['role'] != 'admin'): ?>
                            <a href="../delete_user.php?id=<?php echo $user['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        <?php else: ?>
                            <span style="color: #999; font-size: 0.9rem;">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="../main.html" class="back-to-site">
    <i class="fas fa-arrow-left"></i>
    Back to Main Site
</a>

</body>
</html>
<?php $conn->close(); ?>