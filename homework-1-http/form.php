<?php
require_once 'config_dadata.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<form action="dadata.php" method="POST">

		<strong>Ваше имя<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_name" value=""><br>

		<strong>Ваше отчество<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_second_name" value=""><br>

		<strong>Ваша фамилия<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_last_name" value=""><br>

		<input type="hidden" name="api_key" value="<?php echo htmlspecialchars(API_KEY);?>">
		<input type="hidden" name="secret_key" value="<?php echo htmlspecialchars(SECRET_KEY);?>">

		<input style="margin-top: 10px" type="submit" name="submit" value="Отправить">
		</form>
</body>
</html>

