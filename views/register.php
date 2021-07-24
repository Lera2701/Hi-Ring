<?php
require_once 'header.php';
?>
    <h1>Welcome!</h1>
    <div id="wrapper">
        <p>click here to <a href="login.php">log in</a></p>
        <form id="signin" method="POST" action="" autocomplete="off">
            <input type="text" id="userReg" name="userReg" placeholder="username" />
            <input type="password" id="passReg" name="passReg" placeholder="password" />
            <input type="password" id="passReg1" name="passReg1" placeholder="password again" />
            <button id="submitReg" type="submit">&#xf0da;</button>
            <p class="error" id="error"></p>
        </form>
    </div>

<?php
require_once 'footer.php';
?>