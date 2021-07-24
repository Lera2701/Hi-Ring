<?php
require_once 'header.php';
require_once '../models/main.php';
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
            <div class="navNote" data-id="<?= $_SESSION['id']; ?>">Сповіщення</div>

            <?php if ($role !== 'undefined'): ?>
            <a href="works.php">
                <div class="nav">Роботи</div>
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="users.php">
                    <div class="nav">Користувачі</div>
                </a>
            <?php endif; ?>

            <?php if ($role === 'companyDirector' || $role === 'firmDirector'): ?>
                <a href="reports.php">
                    <div class="nav">Звіти</div>
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <div class="nav" id="navBackup">Створити резервну копію</div>
                <div class="nav" id="navRestore">Відновити з резервної копії</div>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php if($role === 'undefined'): ?>
            <p>Вітаємо у 'Hi-Ring'! Щоб підтвердити акаунт, будь ласка, зв'яжіться з нами за допомогою форми нижче. Ми зателефонуємо Вам і допоможемо вирішити усі питання!</p>
            <form id="callback" method="POST" action="" autocomplete="off">
                <input type="text" id="nameCall" name="nameCall" placeholder="ім'я" />
                <input type="text" id="phoneCall" name="phoneCall" placeholder="телефон" />
                <input type="email" id="emailCall" name="emailCall" placeholder="пошта" />
                <button id="submitCallback" type="submit" >&#xf0da;</button>
                <p class="error" id="error"></p>
            </form>
            <?php elseif($role === 'admin'): ?>
                <?= $table; ?>
            <?php endif; ?>
        </div>
    </div>

</div>