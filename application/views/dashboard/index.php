<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Net Global Solutions Inc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #F4F6FA;
        }

        .navbar {
            background-color: #0A1F44;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar .brand {
            font-weight: 600;
            font-size: 1rem;
        }

        .navbar .user-info {
            font-size: 0.9rem;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            padding: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card h3 {
            margin-top: 0;
            color: #0A1F44;
        }

        .stat-box {
            display: inline-block;
            width: 200px;
            padding: 20px;
            margin-right: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .stat-box h4 {
            margin: 0;
            font-size: 1.5rem;
            color: #1E3A8A;
        }

        .stat-box p {
            margin: 5px 0 0;
            font-size: 0.8rem;
            color: #6b7280;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand">Net Global Solutions Inc - Ticketing System</div>
    <div class="user-info">
        <?= $full_name ?> (<?= $role ?>)
        <a href="<?= site_url('auth/logout') ?>">Logout</a>
    </div>
</div>

<div class="container">

    <div class="card">
        <h3>Dashboard Overview</h3>
        <p>Welcome to the internal support ticketing platform.</p>
    </div>

    <div>
        <div class="stat-box">
            <h4>0</h4>
            <p>Open Tickets</p>
        </div>

        <div class="stat-box">
            <h4>0</h4>
            <p>In Progress</p>
        </div>

        <div class="stat-box">
            <h4>0</h4>
            <p>Resolved Today</p>
        </div>
    </div>

</div>

</body>
</html>