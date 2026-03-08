<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Net Global Solutions Inc - Admin Panel</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>

<div class="admin-wrapper">

    <!-- TOP HEADER -->
    <header class="top-header">
        <div class="company-name">
            Net Global Solutions Inc
        </div>

        <div class="user-info">
            <?= $full_name ?> (<?= $role ?>)
            <a href="<?= site_url('auth/logout') ?>">Logout</a>
        </div>
    </header>

    <div class="layout">