<?php
/**
* ページング処理
*/
class Pagingclass
{
	function previous_page($nowpage)
	{
		printf("&nbsp<a href='?next_page=%s'>%sページ</a>&nbsp",$nowpage-1,$nowpage-1);
	}
	function following_page($nextnum)
	{
		printf("&nbsp;<a href='?next_page=%s'>%sページ</a>&nbsp;",$nextnum+1,$nextnum+1);
	}
}

?>