<?php
require_once 'header.php';
require_once '../models/users.php';
?>
    <script src="../controllers/main.js"></script>
<div class="wrapper">
    <div class="header">
        <h2>Hi-Ring</h2>
        <h4 id="out">Вийти</h4>
        <h4 id="lang">укр</h4>
    </div>
    <div class="main">
        <div class="navs">
            <div class="navNote">Сповіщення</div>
            <a href="works.php">
                <div class="nav">Роботи</div>
            </a>
            <a href="users.php">
                <div class="nav">Користувачі</div>
            </a>
            <div class="nav" id="navBackup">Створити резервну копію</div>
            <div class="nav" id="navRestore">Відновити з резервної копії</div>
        </div>
        <div class="content">
            <h2>Користувачі</h2>
            <?= $table; ?>
        </div>
    </div>

</div>
<?php
require_once 'footer.php';?>