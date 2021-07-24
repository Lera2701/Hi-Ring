<?php
require_once 'header.php';
?>
  <h1>Welcome!</h1>
<div id="wrapper">
    <p>click here to <a href="../views/register.php">register</a></p>
	<form id="signin" method="POST" action="" autocomplete="off">
		<input type="text" id="user" name="user" placeholder="username" />
		<input type="password" id="pass" name="pass" placeholder="password" />
		<button id="submit" type="submit">&#xf0da;</button>
        <p class="error" id="error"></p>
	</form>
</div>

<?php
require_once 'footer.php';
?>
