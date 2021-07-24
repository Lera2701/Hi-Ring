<?php
require_once 'header.php';
require_once '../models/users.php';
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
            <a href="users.php">
                <div class="nav">Users</div>
            </a>

            <div class="nav" id="navBackup">Create backup</div>
            <div class="nav" id="navRestore">Restore database</div>

        </div>
        <div class="content">
            <h2>Users</h2>
            <?= $table; ?>
        </div>
    </div>

</div>
<?php
require_once 'footer.php';?>