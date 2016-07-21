<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require "db.php";
$search_value = $_GET["postal_code"];
$show_table_data_array = array();

$sql = "SELECT * FROM `kadai_hasegawa_ziplist`";
$table_sql = "SHOW FULL COLUMNS FROM `kadai_hasegawa_ziplist`";
$search_sql = "SELECT * FROM `kadai_hasegawa_ziplist` WHERE `zip_code` LIKE '%$search_value%'";
//プルダウン情報
$pull_data_sql = "SELECT `double_zip_code`.`show_content`AS`town_double_zip_code`,`multi_address`.`show_content`AS`town_multi_address`,`attach_district`.`show_content`AS`town_attach_district`,`multi_town`.`show_content`AS`zip_code_multi_town`,`kadai_hasegawa_update_check_code_mst`.`show_content`AS`update_check`,`kadai_hasegawa_update_reason_code_mst`.`show_content`AS`update_reason` FROM `kadai_hasegawa_ziplist` LEFT JOIN `kadai_hasegawa_town_code_mst` AS `double_zip_code` ON `kadai_hasegawa_ziplist`.`town_double_zip_code` = `double_zip_code`.`code_key_index` LEFT JOIN `kadai_hasegawa_town_code_mst` AS `multi_address` ON `kadai_hasegawa_ziplist`.`town_multi_address` = `multi_address`.`code_key_index` LEFT JOIN `kadai_hasegawa_town_code_mst` AS `attach_district` ON `kadai_hasegawa_ziplist`.`town_attach_district` = `attach_district`.`code_key_index` LEFT JOIN `kadai_hasegawa_town_code_mst` AS `multi_town` ON `kadai_hasegawa_ziplist`.`zip_code_multi_town` = `multi_town`.`code_key_index` LEFT JOIN `kadai_hasegawa_update_check_code_mst` ON `kadai_hasegawa_ziplist`.`update_check` = `kadai_hasegawa_update_check_code_mst`.`code_key_index` LEFT JOIN `kadai_hasegawa_update_reason_code_mst` ON `kadai_hasegawa_ziplist`.`update_reason` = `kadai_hasegawa_update_reason_code_mst`.`code_key_index` WHERE `zip_code` LIKE '%$search_value%'";

if (!$link = mysql_connect($host, $username, $pass))
{
	die("接続失敗");
}
$db = mysql_select_db($dbname, $link);
mysql_query('SET NAMES utf8', $link );

$pull_data = mysql_query($pull_data_sql);

$res = mysql_query($sql);
$column_count = mysql_num_fields($res);
$table_data = mysql_query($table_sql);
$count_th = 0;
$search_result = mysql_query($search_sql);
$input_array = array("","","","","","","","","",0,0,0,0,0,0);
$input_count = 0;
$first_flag = true;
$error_flag = false;
if (isset($_POST["input_public_group_code"],$_POST["input_zip_code_old"],$_POST["input_zip_code"],$_POST["input_prefecture_kana"],$_POST["input_city_kana"],$_POST["input_town_kana"],$_POST["input_prefecture"],$_POST["input_city"],$_POST["input_town"],$_POST["input_town_double_zip_code"],$_POST["input_town_multi_address"],$_POST["input_town_attach_district"],$_POST["input_zip_code_multi_town"],$_POST["input_update_check"],$_POST["input_update_reason"]))
{
	// "初回ではない"
	$first_flag = false;
	foreach ($_POST as $value)
	{
		if (isset($value))
		{
			$input_array[$input_count] = $value;
		}
		$input_count++;
	}
}
else
{
	// "初回"
}
?>
<html>
<head>
<meta charset="UTF-8">
<title>PHP課題6_3 上書き</title>
</head>
<body>
<p>PHP課題6_3 上書き</p>
<table border=1>
	<?php
	printf("<tr></tr>");
	while ($count_th < $column_count)
	{
		$show_table_data = mysql_fetch_assoc($table_data);
		$show_table_data_array[$count_th] = print_r($show_table_data["Comment"],true);
		printf("<th>%s</th>", $show_table_data_array[$count_th]);
		$count_th++;
	}
	$count_th = 0;
	while($search_result_row = mysql_fetch_assoc($search_result) and $pull_data_row = mysql_fetch_assoc($pull_data))
	{
		printf("<tr></tr>");
		while ($count_th < $column_count)
		{
			$column_name = mysql_field_name($search_result, $count_th);
			if ($first_flag)
			{
				$input_array[$count_th] = $search_result_row[print_r($column_name,true)];
			}
			if (9 <= $count_th)
			{
				printf("<th>%s</th>", $pull_data_row[print_r($column_name,true)]);
			}
			else
			{
				printf("<th>%s</th>", $input_array[$count_th]);
			}
			$count_th++;
		}
		$count_th = 0;
	}
	?>
</table>
<p>上書き設定入力</p>
<form name="form_post" action="" method="post">
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[0] == "")
		{
			echo "<font color='#ff0000'>全国地方公共団体コードが未入力です</font><br>";
		}
		elseif (!preg_match('/[0-9]/', $input_array[0]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角数字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>1.全国地方公共団体コード<br><?php echo $input_array[0]; ?><input type="hidden" name="input_public_group_code" value="<?php echo $input_array[0]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[1] == "")
		{
			echo "<font color='#ff0000'>旧郵便番号が未入力です</font><br>";
		}
		elseif (!preg_match('/[0-9]/', $input_array[1]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角数字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>2.旧郵便番号<br><?php echo $input_array[1]; ?><input type="hidden" name="input_zip_code_old" value="<?php echo $input_array[1]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[2] == "")
		{
			echo "<font color='#ff0000'>郵便番号が未入力です</font><br>";
		}
		elseif (!preg_match('/[0-9]/', $input_array[2]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角数字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>3.郵便番号<br><?php echo $input_array[2]; ?><input type="hidden" name="input_zip_code" value="<?php echo $input_array[2]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[3] == "")
		{
			echo "<font color='#ff0000'>都道府県名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[ｦ-ﾟｰ ()]+$/u', $input_array[3]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角カナ以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>4.都道府県名(半角カタカナ)<br><input type="text" name="input_prefecture_kana" value="<?php echo $input_array[3]; ?>"></label>
	</p>
	<p>
	<?php if (!$first_flag)
	{
		if ($input_array[4] == "")
		{
			echo "<font color='#ff0000'>市区町村名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[ｦ-ﾟｰ ()]+$/u', $input_array[4]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角カナ以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>5.市区町村名(半角カタカナ)<br><input type="text" name="input_city_kana" value="<?php echo $input_array[4]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[5] == "")
		{
			echo "<font color='#ff0000'>町域名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[ｦ-ﾟｰ ()]+$/u', $input_array[5]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>半角カナ以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>6.町域名(半角カタカナ)<br><input type="text" name="input_town_kana" value="<?php echo $input_array[5]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[6] == "")
		{
			echo "<font color='#ff0000'>都道府県名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[一-龠]+$/u', $input_array[6]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>漢字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>7.都道府県名(漢字)<br><input type="text" name="input_prefecture" value="<?php echo $input_array[6]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[7] == "")
		{
			echo "<font color='#ff0000'>市区町村名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[^ -~｡-ﾟ\x00-\x1f\t]+$/u', $input_array[7]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>全角文字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>8.市区町村名<br><input type="text" name="input_city" value="<?php echo $input_array[7]; ?>"></label>
	</p>
	<p>
	<?php
	if (!$first_flag)
	{
		if ($input_array[8] == "")
		{
			echo "<font color='#ff0000'>町域名が未入力です</font><br>";
		}
		elseif (!preg_match('/^[^ -~｡-ﾟ\x00-\x1f\t]+$/u', $input_array[8]))
		{
			$error_flag = true;
			echo "<font color='#ff0000'>全角文字以外の内容が入力されています</font><br>";
		}
	}
	?>
	<label>9.町域名<br><input type="text" name="input_town" value="<?php echo $input_array[8]; ?>"></label>
	</p>
	<p>
	<label>10.一町域で複数の郵便番号か<br><select name="input_town_double_zip_code">
		<option value=0 <?php if ($input_array[9] == 0) {print("selected");} ?>>該当せず</option>
		<option value=1 <?php if ($input_array[9] == 1) {print("selected");} ?>>該当</option>
	</select>
	</p>
	<p>
	<label>11.小字毎に番地が起番されている町域か<br><select name="input_town_multi_address">
		<option value=0 <?php if ($input_array[10] == 0) {print("selected");} ?>>該当せず</option>
		<option value=1 <?php if ($input_array[10] == 1) {print("selected");} ?>>該当</option>
	</select>
	</p>
	<p>
	<label>12.丁目を有する町域名か<br><select name="input_town_attach_district">
		<option value=0 <?php if ($input_array[11] == 0) {print("selected");} ?>>該当せず</option>
		<option value=1 <?php if ($input_array[11] == 1) {print("selected");} ?>>該当</option>
	</select>
	</p>
	<p>
	<label>13.一郵便番号で複数の町域か<br><select name="input_zip_code_multi_town">
		<option value=0 <?php if ($input_array[12] == 0) {print("selected");} ?>>該当せず</option>
		<option value=1 <?php if ($input_array[12] == 1) {print("selected");} ?>>該当</option>
	</select>
	</p>
	<p>
	<label>14.更新確認<br><select name="input_update_check">
		<option value=0 <?php if ($input_array[13] == 0) {print("selected");} ?>>変更なし</option>
		<option value=1 <?php if ($input_array[13] == 1) {print("selected");} ?>>変更あり</option>
		<option value=2 <?php if ($input_array[13] == 2) {print("selected");} ?>>廃止(廃止データのみ使用)</option>
	</select></label>
	</p>
	<p>
	<label>15.更新理由<br><select name="input_update_reason">
		<option value=0 <?php if ($input_array[14] == 0) {print("selected");} ?>>変更なし</option>
		<option value=1 <?php if ($input_array[14] == 1) {print("selected");} ?>>市政・区政・町政・分区・政令指定都市施行</option>
		<option value=2 <?php if ($input_array[14] == 2) {print("selected");} ?>>住居表示の実施</option>
		<option value=3 <?php if ($input_array[14] == 3) {print("selected");} ?>>区画整理</option>
		<option value=4 <?php if ($input_array[14] == 4) {print("selected");} ?>>郵便区調整等</option>
		<option value=5 <?php if ($input_array[14] == 5) {print("selected");} ?>>訂正</option>
		<option value=6 <?php if ($input_array[14] == 6) {print("selected");} ?>>廃止(廃止データのみ使用)</option>
	</select></label>
	</p>
	<p>
	<input type="submit" value="確認へ">
	<input type="submit" value="戻る" onClick="form.action='index.php';return true">
	</p>
</form>
</body>
</html>
<?php
if (!$first_flag)
{
	if($_POST["input_public_group_code"] !== ''&&$_POST["input_zip_code_old"] !== ''&&$_POST["input_zip_code"] !== ''&&$_POST["input_prefecture_kana"] !== ''&&$_POST["input_city_kana"] !== ''&&$_POST["input_town_kana"] !== ''&&$_POST["input_prefecture"] !== ''&&$_POST["input_city"] !== ''&&$_POST["input_town"] !== ''&&$_POST["input_town_double_zip_code"] !== ''&&$_POST["input_town_multi_address"] !== ''&&$_POST["input_town_attach_district"] !== ''&&$_POST["input_zip_code_multi_town"] !== ''&&$_POST["input_update_check"] !== ''&&$_POST["input_update_reason"] !== ''&&$error_flag == false)
	{
		$_SESSION["input_param"] = $input_array;
		header("Location:checkpage.php");
	}
}
?>