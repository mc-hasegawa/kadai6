<?php

function export_csv($csv_dl_sql)
{
	$file_path = "export.csv";
	$export_csv_title = array('public_group_code', 'zip_code_old', 'zip_code', 'prefecture_kana', 'city_kana', 'town_kana', 'prefecture', 'city', 'town', 'town_double_zip_code', 'town_multi_address', 'town_attach_district', 'zip_code_multi_town', 'update_check', 'update_reason');
	$res_export = mysql_query($csv_dl_sql);
	if(touch($file_path))
	{
		$file = new SplFileObject($file_path,"w"); 
		foreach($export_csv_title as $key => $val)
		{
			$export_header[] = mb_convert_encoding($val,'SJIS-win','UTF-8');
		}
		$file->fputcsv($export_header);
		while($row_export = mysql_fetch_assoc($res_export))
		{
			$export_arr = "";
			foreach($row_export as $key => $val)
			{
				$export_arr[] = mb_convert_encoding($val,'SJIS-win','UTF-8');
			}		
			$file->fputcsv($export_arr);
		}
	}
	header("Content-Disposition: attachment; filename=$file_path");
	header("Content-Transfer-Encoding: binary");
	readfile($file_path);
	exit;
}
?>