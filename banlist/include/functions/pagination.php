<?php
/*
* pagination.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function pagination($array) 
{
    $countTotal = count($array['query']);

	$page = $_GET['page'] ? abs((int)$_GET['page']) : 1;  //1
	$page_total = floor($countTotal / $array['page_num'] +1); // 4
	$page_count = $page*$array['page_num']-$array['page_num']; // 0
	
	$pagination = '<nav class="text-center"><ul class="pagination">';
	if($page > 1) $pagination .= '<li><a href="'.$array['url'].'&page='.($page-1).'">Назад</a></li>';
	for($i = max(1, $page - 2); $i <= min($page + 2, $page_total); $i++)
		if($i==$page)
			$pagination .= '<li class="disabled"><a>'.$i.'</a></li>';
		else
			$pagination .= '<li>'.('<a href="'.$array['url'].'&page='.$i.'">'.$i.'</a>').'</li>';
	$pagination .= ''.($page<$page_total?'<li><a href="'.$array['url'].'&page='.($page+1).'">Далее</a></li>':'<li class="disabled"><a>Далее</a></li>').'';
	
	if($page_total > 1)
	if($page == $page_total)
		$pagination .= '<li><a href="'.$array['url'].'&page=1">В начало</a></li>';
	else
		$pagination .= '<li><a href="'.$array['url'].'&page='.$page_total.'">В конец</a></li>';
	$pagination .= '</ul></nav>';
	
	return array('query'=>$array['query'], 'pages'=>$pagination, 'count'=>$page_count);
}
?>