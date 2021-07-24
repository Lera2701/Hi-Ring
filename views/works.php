<?php
require_once 'header.php';
require_once '../models/works.php';
?>
<script src="../controllers/main.js"></script>
<div class="wrapper">
    <div class="header">
        <h2>Hi-Ring</h2>
        <h4 id="out">log out</h4>
        <h4 id="lang">eng</h4>
    </div>
    <div class="main">
        <div class="navs">
            <div class="navNote">Notifications</div>
            <a href="works.php">
                <div class="nav">Works</div>
            </a>

            <?php if ($_SESSION['rights'] === '1'): ?>
                <a href="users.php">
                    <div class="nav">Users</div>
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['rights'] === '2' || $_SESSION['rights'] === '3'): ?>
                <a href="reports.php">
                    <div class="nav">Reports</div>
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['rights'] === '1'): ?>

                    <div class="nav" id="navBackup">Create backup</div>
                    <div class="nav" id="navRestore">Restore database</div>

            <?php endif; ?>
        </div>
        <div class="content">
            <?= $table; ?>
            <?php if($_SESSION['rights'] === '4'): ?>
                <div class="nav" id="addTask">Add task</div>
            <?= $table2; ?>
            <?php endif; ?>
        </div>
    </div>
</div>