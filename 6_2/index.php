<?php
header('Content-Type: text/html; charset=UTF-8');
function cookie_check()
{
	if (isset($_GET['get_sort_column']))	//getで受け取った選んだカラムを判別
	{
		if ($_GET['get_sort_column'] == $_COOKIE['sort_column_cookie'])
		{
			if ($_COOKIE['sort_order_cookie'] == 'ASC')	//設定の反転
			{
				setcookie('sort_order_cookie','DESC');
			}
			else
			{
				setcookie('sort_order_cookie','ASC');
			}
		}
		else
		{
			setcookie('sort_order_cookie','ASC');
		}
		setcookie('sort_column_cookie',$_GET['get_sort_column']);
		unset($_GET['get_sort_column']);	//連続で判別されないようにgetを削除
		header("Location: ./index.php");
		break;
	}
	if (isset($_COOKIE['sort_column_cookie']))
	{
		$sort_column_check = $_COOKIE['sort_column_cookie'];
	}
	else
	{
		$sort_column_check = `public_group_code`;
	}
	if (isset($_COOKIE['sort_order_cookie']))
	{
		$sort_order = $_COOKIE['sort_order_cookie'];
	}
	else
	{
		setcookie('sort_order_cookie','ASC');
		$sort_order = 'ASC';
	}
	//ページ数
	if (isset($_GET["next_page"]))
	{
		$now_page_num = $_GET["next_page"];
		setcookie('page_num',$_GET["next_page"]);
	}
	elseif (isset($_COOKIE['page_num']))
	{
		$now_page_num = $_COOKIE['page_num'];
	}
	else
	{
		$now_page_num = 1;
		setcookie('page_num',$now_page_num);
	}
	return array($sort_column_check,$sort_order,$now_page_num);
}
$cookie_check_array = cookie_check();
$sort_column = $cookie_check_array[0];
$sort_order_check = $cookie_check_array[1];
$now_page = $cookie_check_array[2];
require "paging.php";
require "db.php";
require "export_csv.php";
if (isset($sort_column))
{
	$sql = "SELECT * FROM `kadai_hasegawa_ziplist` ORDER BY `kadai_hasegawa_ziplist`.`$sort_column` {$sort_order_check}";
}
else
{
	$sql = "SELECT * FROM `kadai_hasegawa_ziplist`";
}
$table_sql = "SHOW FULL COLUMNS FROM `kadai_hasegawa_ziplist`";
$pull_data_sql = "SELECT `double_zip_code`.`show_content`AS`town_double_zip_code`,`multi_address`.`show_content`AS`town_multi_address`,`attach_district`.`show_content`AS`town_attach_district`,`multi_town`.`show_content`AS`zip_code_multi_town`,`kadai_hasegawa_update_check_code_mst`.`show_content`AS`update_check`,`kadai_hasegawa_update_reason_code_mst`.`show_content`AS`update_reason`
FROM `kadai_hasegawa_ziplist`
LEFT JOIN `kadai_hasegawa_town_code_mst` AS `double_zip_code` ON `kadai_hasegawa_ziplist`.`town_double_zip_code` = `double_zip_code`.`code_key_index`
LEFT JOIN `kadai_hasegawa_town_code_mst` AS `multi_address` ON `kadai_hasegawa_ziplist`.`town_multi_address` = `multi_address`.`code_key_index`
LEFT JOIN `kadai_hasegawa_town_code_mst` AS `attach_district` ON `kadai_hasegawa_ziplist`.`town_attach_district` = `attach_district`.`code_key_index`
LEFT JOIN `kadai_hasegawa_town_code_mst` AS `multi_town` ON `kadai_hasegawa_ziplist`.`zip_code_multi_town` = `multi_town`.`code_key_index`
LEFT JOIN `kadai_hasegawa_update_check_code_mst` ON `kadai_hasegawa_ziplist`.`update_check` = `kadai_hasegawa_update_check_code_mst`.`code_key_index`
LEFT JOIN `kadai_hasegawa_update_reason_code_mst` ON `kadai_hasegawa_ziplist`.`update_reason` = `kadai_hasegawa_update_reason_code_mst`.`code_key_index`";
$link = mysql_connect($host, $username, $pass);
$db = mysql_select_db($dbname, $link);
mysql_query('SET NAMES utf8', $link );
$res = mysql_query($sql);
$column_count = mysql_num_fields($res);
$table_data = mysql_query($table_sql);
$delete_data = array(array());
$count_th = 0;
$count_tr = 0;
if (isset($_POST["checkbox_param"]))
{
	var_dump($_POST["checkbox_param"]);
	foreach ($_POST["checkbox_param"] as $value)
	{
		$delete_sql = "DELETE FROM `lesson`.`kadai_hasegawa_ziplist` WHERE `kadai_hasegawa_ziplist`.`zip_code` = '$value'";
		if (!$delete_sql_run = mysql_query($delete_sql))
		{
			die("削除失敗");
		}
	}
	header("Location: ./index.php");
}
$RECORD_NUM = 30;	//表示レコード数の定数
$page_num = ceil(mysql_num_rows($res) / $RECORD_NUM);
$pull_data = mysql_query($pull_data_sql);

if (isset($_POST["csv_dl_flag"]))
{
	if (isset($_POST["all_dl_flag"]))
	{
		$csv_dl_sql = "SELECT * FROM `kadai_hasegawa_ziplist`";
	}
	else
	{
		$min_num = $RECORD_NUM*($now_page-1)+1;
		$max_num = $RECORD_NUM*($now_page);
		$csv_dl_sql = "SELECT * FROM `kadai_hasegawa_ziplist` LIMIT $min_num,$max_num";
	}
	export_csv($csv_dl_sql);
}
?>
<html>
<head>
<title>PHP課題6_2</title>
</head>
<script>
	function delete_check()
	{
		var delete_flag = confirm("チェックしたレコードを削除してもよろしいですか？");
		return delete_flag;
	}
	function check_all()	//チェックボックス一括制御
	{
		var checked_flag = false;
		for (var i = 0; i < document.table_list.elements['checkbox_param[]'].length; i++)
		{
			if (document.table_list.elements['checkbox_param[]'][i].checked == false)
			{
				checked_flag = true;
			}
		}
		for (var i = 0; i < document.table_list.elements['checkbox_param[]'].length; i++)
		{
			document.table_list.elements['checkbox_param[]'][i].checked = checked_flag;
		}
	}
	function csv_dl()
	{
		var dl_flag = confirm("CSVをダウンロードします");
		return dl_flag;
		// var checkbox_status = new Array();
		// var array_count = 0;
		// for (var i = 0; i < document.table_list.elements['checkbox_param[]'].length; i++)
		// {
		// 	if (document.table_list.elements['checkbox_param[]'][i].checked == true)
		// 	{
		// 		checkbox_status[array_count] = document.table_list.elements['checkbox_param[]'][i].value;
		// 		console.log(checkbox_status);
		// 		array_count++;
		// 	}
		// }
	}
</script>
<body>
	<p>PHP課題6_2</p>
	<p>
	<?php
	$pageing = new Pagingclass();
	for ($i=$now_page-3; $i <= $now_page; $i++)	//前のページのリンク生成(最大4つまで)
	{ 
		if (1 < $i)
		{
			$pageing->previous_page($i);
		}
	}
	printf("&nbsp;%sページ&nbsp;",$now_page);
	for ($i=$now_page; $i < $page_num; $i++)//次のページのリンク生成(最大4つまで)
	{ 
		if ($now_page+3 < $i)
		{
			break;
		}
		$pageing->following_page($i);
	}

	?>
	</p>
	<form name="csv_dl_form" method="post">
	全件
	<input type='checkbox' name='all_dl_flag' value=0>
	<input name="csv_dl_flag" type="submit" value="csvダウンロード" action="" onclick="return csv_dl()">
	</form>
	<form name="table_list" method="post">
		<p><input type="submit" value="チェック項目の削除" action="" onclick="return delete_check()">
		<input type="button" name="check_all_button" value="一括チェック" onclick="check_all()"></p>
		<table border=1>
			<?php
			printf("<tr></tr>");
			printf("<th>削除チェック</th>");
			while ($count_th < $column_count)
			{
				$show_table_data = mysql_fetch_assoc($table_data);
				$order_symbol = "";
				if ($sort_column == print_r($show_table_data["Field"],true))
				{
					if ($sort_order_check == "ASC")
					{
						$order_symbol = "▲";
					}
					else
					{
						$order_symbol = "▼";
					}
				}
				printf("<th><a href='?get_sort_column=%s'>%s%s</a></th>",print_r($show_table_data["Field"],true),print_r($show_table_data["Comment"],true),$order_symbol);
				$count_th++;
			}
			$count_th = 0;
			while($row = mysql_fetch_assoc($res) and $row_pull_data = mysql_fetch_assoc($pull_data))
			{
				if ($RECORD_NUM*($now_page-1)-1 < $count_tr)
				{
					printf("<tr></tr>");
					$delete_data[$count_tr] = $row["zip_code"];
					printf("<th><input type='checkbox' name='checkbox_param[]' value=$delete_data[$count_tr]></th>");
					while ($count_th < $column_count)
					{
						$column_name = mysql_field_name($res, $count_th);	//カラム名取得
						$show_data = $row[print_r($column_name,true)];
						if($count_th == 2)
						{
							printf("<th><a href='%s?postal_code=%s'>%s</a></th>","overwrite.php",$show_data,$show_data);
						}
						elseif (9 <= $count_th)
						{
							printf("<th>%s</th>",$row_pull_data[print_r($column_name,true)]);
						}
						else
						{
							printf("<th>%s</th>",$show_data);
						}
						$count_th++;
					}
				}
				$count_tr++;
				$count_th = 0;
				if($RECORD_NUM*$now_page == $count_tr)
				{
					break;
				}
			}
			$count_tr = 0;
			?>
		</table>
	</form>
	<p></p>
</body>
<?php mysql_close($link); ?>
</html>