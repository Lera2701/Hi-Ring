<?php
require_once 'header.php';
?>
  <h1>Вітаємо!</h1>
<div id="wrapper">
    <p>натисни сюди для реєстрації <a href="../views/register.php">реєстрація</a></p>
	<form id="signin" method="POST" action="" autocomplete="off">
		<input type="text" id="user" name="user" placeholder="логін" />
		<input type="password" id="pass" name="pass" placeholder="пароль" />
		<button id="submit" type="submit">&#xf0da;</button>
        <p class="error" id="error"></p>
	</form>
</div>

<?php
require_once 'footer.php';
?>
