<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<link rel="stylesheet" href="style.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
	<script dafer src="js/main.js"></script>
	<title>Const</title>
</head>
<body>
	
</body>
</html>
<?php 

### Constructor_EXCEL start

if(isset($_POST['exp_all'])) {
	$tmp_id = $_POST['temp_excel']; 

	$sql = db_array(mysql_query("SELECT * FROM `example`"));
	$sqlLength = count($sql) + 1;
	$file = 'export_';

	include 'temp/tempExcel.php';

	echo "<a  href='export/".$filename.".xlsx' target='_blank' style='color: #ff0000;'>Download</a>";
}


$tmp_type = '0';
// порядок в масивах тайтлах дожен совпадать с порядком в массиве данных
$argWidth = [	30,	30,	10,	25,	25,	25,	30, 25, 25,	25,	25,	25,	25,	25,	35, 20, 20, 45, 15, 30, 30, 30, 30];
$argTitle = ['Ім`я', 'Телефон', 'Оцінка NPS', 'Дата  відгуку', 'Текст', 'Коментар', ' Об`єкт', 'Побажання', 'Параметр оцінки 1', 'Параметр оцінки 2', 'Параметр оцінки 3', 'Параметр оцінки 4', 'Параметр оцінки 5', 'Параметр оцінки 6', 'Персонал', 'Переваги', 'Недолiки', 'E-mail', 'Рейтинг', 'Місто', 'Дата народження', 'Дод. поле 1', 'Дод. поле 2']; 

echo '<div id="const_wrap" style="display: none; width: 100%; max-width: 700px">';
	include 'const/excel_constructor.php';
echo '</div>';
echo '<div id="excel_wrap" style="display: none; width: 100%; max-width: 700px"></div>';
?>

<button class="reviews-action__btn" data-fancybox="g_constructor_excel" data-src="#const_wrap">КОНСТРУКТОР</button>
<button class="reviews-action__btn" data-fancybox="g_excel_wrap" data-src="#excel_wrap">EXCEL</button>


