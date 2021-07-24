<?php
require_once 'header.php';
?>
    <h1>Вітаємо!</h1>
    <div id="wrapper">
        <p>натисни сюди для авторизації <a href="login.php">увійти</a></p>
        <form id="signin" method="POST" action="" autocomplete="off">
            <input type="text" id="userReg" name="userReg" placeholder="логін" />
            <input type="password" id="passReg" name="passReg" placeholder="пароль" />
            <input type="password" id="passReg1" name="passReg1" placeholder="повторення паролю" />
            <button id="submitReg" type="submit">&#xf0da;</button>
            <p class="error" id="error"></p>
        </form>
    </div>

<?php
require_once 'footer.php';
?>