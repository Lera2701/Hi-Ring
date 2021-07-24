<?php
require_once 'header.php';
require_once '../models/main.php';
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
            <div class="navNote" data-id="<?= $_SESSION['id']; ?>">Notifications</div>

            <?php if ($role !== 'undefined'): ?>
            <a href="works.php">
                <div class="nav">Works</div>
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="users.php">
                    <div class="nav">Users</div>
                </a>
            <?php endif; ?>

            <?php if ($role === 'companyDirector' || $role === 'firmDirector'): ?>
                <a href="reports.php">
                    <div class="nav">Reports</div>
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                    <div class="nav" id="navBackup">Create backup</div>
                    <div class="nav" id="navRestore">Restore database</div>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php if($role === 'undefined'): ?>
            <p>Welcome to 'Hi-Ring'! To confirm your account, please, leave us a request by following form. We`ll contact you and solve all problems!</p>
            <form id="callback" method="POST" action="" autocomplete="off">
                <input type="text" id="nameCall" name="nameCall" placeholder="name" />
                <input type="text" id="phoneCall" name="phoneCall" placeholder="phone" />
                <input type="email" id="emailCall" name="emailCall" placeholder="email" />
                <button id="submitCallback" type="submit" >&#xf0da;</button>
                <p class="error" id="error"></p>
            </form>
            <?php elseif($role === 'admin'): ?>
                <?= $table; ?>
            <?php endif; ?>
        </div>
    </div>

</div>