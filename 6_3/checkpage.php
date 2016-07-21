<?php
header('Content-Type: text/html; charset=UTF-8');
require "db.php";
$post_count = 0;
$link = mysql_connect($host, $username, $pass);
$db = mysql_select_db($dbname, $link);
mysql_query('SET NAMES utf8', $link);
session_start();
$input_param_array = $_SESSION["input_param"];
if ($_POST)
{
	foreach ($input_param_array as $value)
	{
		${"input".$post_count} = $value;
		$post_count++;
	}
	$sql = "REPLACE INTO `kadai_hasegawa_ziplist`(`public_group_code`, `zip_code_old`, `zip_code`, `prefecture_kana`, `city_kana`, `town_kana`, `prefecture`, `city`, `town`, `town_double_zip_code`, `town_multi_address`, `town_attach_district`, `zip_code_multi_town`, `update_check`, `update_reason`) VALUES ('$input0','$input1','$input2','$input3','$input4','$input5','$input6','$input7','$input8','$input9','$input10','$input11','$input12','$input13','$input14')";
	$result_flag = mysql_query($sql);
	var_dump($pull_data);
	if (!$result_flag)
	{
	    die('REPLACEクエリーが失敗しました。'.mysql_error());
	}
	$post_count = 0;
	header("Location: ./index.php");
	session_destroy();
	mysql_close($link);
}
var_dump($input_param_array);
$town_double_zip_code = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_town_code_mst` WHERE `code_key_index` LIKE '$input_param_array[9]'");
$town_multi_address = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_town_code_mst` WHERE `code_key_index` LIKE '$input_param_array[10]'");
$town_attach_district = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_town_code_mst` WHERE `code_key_index` LIKE '$input_param_array[11]'");
$zip_code_multi_town = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_town_code_mst` WHERE `code_key_index` LIKE '$input_param_array[12]'");
$update_check = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_update_check_code_mst` WHERE `code_key_index` LIKE '$input_param_array[13]'");
$update_reason = mysql_query("SELECT `show_content` FROM `kadai_hasegawa_update_check_code_mst` WHERE `code_key_index` LIKE '$input_param_array[14]'");
?>
<html>
<head>
<meta charset="UTF-8">
<title>PHP課題6_3 確認</title>
</head>
<body>
<p>PHP課題6_3 確認</p>
<p>内容確認</p>
<p>1.全国地方公共団体コード<br><?php echo htmlspecialchars($input_param_array[0]); ?></p>
<p>2.旧郵便番号<br><?php echo htmlspecialchars($input_param_array[1]); ?></p>
<p>3.郵便番号<br><?php echo htmlspecialchars($input_param_array[2]); ?></p>
<p>4.都道府県名(半角カタカナ)<br><?php echo htmlspecialchars($input_param_array[3]); ?></p>
<p>5.市区町村名(半角カタカナ)<br><?php echo htmlspecialchars($input_param_array[4]); ?></p>
<p>6.町域名(半角カタカナ)<br><?php echo htmlspecialchars($input_param_array[5]); ?></p>
<p>7.都道府県名(漢字)<br><?php echo htmlspecialchars($input_param_array[6]); ?></p>
<p>8.市区町村名<br><?php echo htmlspecialchars($input_param_array[7]); ?></p>
<p>9.町域名<br><?php echo htmlspecialchars($input_param_array[8]); ?></p>
<p>10.一町域で複数の郵便番号か<br>
<?php
echo mysql_fetch_assoc($town_double_zip_code)[print_r("show_content",true)];
?>
</p>
<p>11.小字毎に番地が起番されている町域か<br>
<?php
echo mysql_fetch_assoc($town_multi_address)[print_r("show_content",true)];
?>
</p>
<p>12.丁目を有する町域名か<br>
<?php
echo mysql_fetch_assoc($town_attach_district)[print_r("show_content",true)];
?>
</p>
<p>13.一郵便番号で複数の町域か<br>
<?php
echo mysql_fetch_assoc($zip_code_multi_town)[print_r("show_content",true)];
?>
</p>
<p>14.更新確認<br>
<?php
echo mysql_fetch_assoc($update_check)[print_r("show_content",true)];
?>
</p>
<p>15.更新理由<br>
<?php 
echo mysql_fetch_assoc($update_reason)[print_r("show_content",true)];
?>
</p>
<form name="form_post" method="post">
	<?php
	foreach ($input_param_array as $value)
	{
		printf("<input type='hidden' name='redirect_param_array[%d]' value='%s'>",$post_count,$value);
		$post_count++;
	}
	?>
	<input type="submit" value="登録">
	<input type="submit" value="戻る" onClick="form.action='overwrite.php';return true">
</form>
</body>
</html>