<?php

function import_csv()
{
	$up_file_data = array();
	$file_data_split = array();
	$overlap_count = 0;
	if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/".$_FILES["upfile"]["name"]))
	{
		chmod("files/".$_FILES["upfile"]["name"],0777);
		$file = fopen("files/".$_FILES["upfile"]["name"], "r");
		if($file)	//テキストの文字列化
		{
			while(!feof($file))
			{
				$up_file_data[] = fgetcsv($file);
			}
		}
		fclose($file);
		foreach ($up_file_data as $file_data_value)	//文字列を分割
		{
			if ($file_data_value != "")
			{
				$file_data_split[] = explode(",",$file_data_value[0]);
			}
		}
		foreach ($file_data_split as $file_data_row)
		{
			for ($i=0; $i < 15; $i++)
			{
				$data_check = false;
				switch ($i)
				{
					case $i<=2:
					if ($file_data_row[$i] == "")
					{
						die("データが入っていません");
					}
					elseif (!preg_match('/[0-9]/',$file_data_row[$i]))
					{
						die($file_data_row[$i]." 半角数字以外のデータが入っています");
					}
					else
					{
						$data_check = true;
					}
					break;
					case $i<=5:
					if ($file_data_row[$i] == "")
					{
						die("データが入っていません");
					}
					elseif (!preg_match('/^[ｦ-ﾟｰ ()0-9･]+$/u',$file_data_row[$i]))
					{
						die($file_data_row[$i]." 半角カナ、数字以外のデータが入ってます");
					}
					else
					{
						$data_check = true;
					}
					break;
					case 6:
					if ($file_data_row[$i] == "")
					{
						die("データが入っていません");
					}
					elseif (!preg_match('/^[一-龠]+$/u',$file_data_row[$i]))
					{
						die($file_data_row[$i]." 漢字以外のデータが入っています");
					}
					else
					{
						$data_check = true;
					}
					break;
					case $i<=8:
					if ($file_data_row[$i] == "")
					{
						die("データが入っていません");
					}
					elseif (!preg_match('/^[^ -~｡-ﾟ\x00-\x1f\t]+$/u',$file_data_row[$i]))
					{
						die($file_data_row[$i]." 全角文字以外のデータが入っています");
					}
					else
					{
						$data_check = true;
					}
					break;
					default:
						$data_check = true;
					break;
				}
				if ($data_check)
				{
					${"file_data_single_".$i} = $file_data_row[$i];
				}
			}
			$insert_sql = "INSERT INTO `kadai_hasegawa_ziplist` VALUES ('$file_data_single_0', '$file_data_single_1', '$file_data_single_2', '$file_data_single_3', '$file_data_single_4', '$file_data_single_5', '$file_data_single_6', '$file_data_single_7', '$file_data_single_8', '$file_data_single_9', '$file_data_single_10', '$file_data_single_11', '$file_data_single_12', '$file_data_single_13', '$file_data_single_14')";
			if (!$insert_sql_run = mysql_query($insert_sql))	//重複数チェック
			{
				$overlap_count++;
			}
			// ${"file_data_single".$post_count} = $value;
		}
		if ($overlap_count != 0)
		{
			die($overlap_count."件のデータ重複");
		}
	}
	else
	{
		die("ファイルのアップロードに失敗しました");
	}
}
?>