<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

</head>
<body>
<?
echo "<pre>"; //открываем html-тег для более читаемого вывода
var_dump($_REQUEST); //Выводим глобальную переменную $_REQUEST
echo "</pre>"; //закрываем html-тег 

$arUserInfo = array(
	"name"=> $_REQUEST['user_name'],
	"second_name" => $_REQUEST['user_second_name'],
	"last_name" => $_REQUEST['user_last_name'],
	"city" => $_REQUEST['user_city'],
	"strict" => $_REQUEST['user_strict'],
	"home" => $_REQUEST['user_home'],
	"flat" => $_REQUEST['user_flat'],
	);

$strUserInfo = json_encode($arUserInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); // кодируем переменную $arUserInfo в json формат с двумя флагами 1. JSON_UNESCAPED_UNICODE - не экранирует Unicode в UTF-8, оставляя символы в оригинальном виде. 2. JSON_PRETTY_PRINT - форматирует вывод JSON добавляя отступы и разрывы строк, чтобы он был более читаем
?>

	<form action="" method="POST">
		<strong>Ваше имя<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_name" id="user_name" value=""><br>

		<strong>Ваше отчество<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_second_name" id="user_second_name" value=""><br>

		<strong>Ваша фамилия<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_last_name" id="user_last_name" value=""><br>

		<strong>Ваш город<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_city" id="user_city" value=""><br>

		<strong>Ваша улица<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_strict" id="user_strict" value=""><br>

		<strong>Ваш дом<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_home" id="user_home" value=""><br>

		<strong>Ваша квартира<span class="mf-req">*</span></strong><br>
		<input type="text" name="user_flat" id="user_flat" value=""><br>

		<input type="submit" name="submit" id="submit" value="Отправить">
	</form>
<div id="result">
	<?=$strUserInfo?> <!--Вывод информации-сокращенная форма php echo $strUserInfo; -->
</div>
</body>
</html>