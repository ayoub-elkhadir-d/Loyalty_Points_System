<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /shopeasy-loyalty/public/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affordable Rewards - ShopEasy Loyalty</title>
    <link rel="stylesheet" href="/shopeasy-loyalty/public/css/style.css">
    <style>
       
    </style>
</head>
<body>
    <div class="rewards-container">
        <div class="rewards-header">
            <h1>Rewards You Can Afford</h1>
            <p>You have <?php echo number_format($_SESSION['user_points']); ?> points to spend</p>
        </div>
        
        <div class="nav-menu">
            <a href="/shopeasy-loyalty/public/rewards" class="nav-button">
                <i class="fas fa-arrow-left"></i> All Rewards
            </a>
        </div>
        
        <div class="rewards-grid">
            <?php foreach ($rewards as $reward): ?>
                <div class="reward-card">
                 
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>