<?php

// В верхний регистр 1-ю букву строки
function mb_firstToUpper($word, $encoding = 'UTF8') {
	return mb_strtoupper(mb_substr($word,0,1,$encoding),$encoding) . mb_substr($word,1,mb_strlen($word),$encoding);
}
// function utf2str($str) {
	// return mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8');
// }	

if (isset($_GET['lang']) and isset($_GET['template']) and isset($_GET['format'])) {

	$dbhost = $_GET['lang'].'wiki.labsdb';
	$dbtable = $_GET['lang'].'wiki_p';
	$config = parse_ini_file('password.ini');
	$tpl_name = str_replace(' ', '_', mb_firstToUpper($_GET['template']));

	if ($_GET['get_timelastedit'])
		$query = "SELECT
			  page.page_title,
			  MAX(revision.rev_timestamp) AS timestamp
			FROM page
			  INNER JOIN templatelinks
				ON page.page_id = templatelinks.tl_from
			  INNER JOIN revision
				ON page.page_id = revision.rev_page
			WHERE templatelinks.tl_namespace = 10
			AND page.page_namespace = 0
			AND templatelinks.tl_title = '". $tpl_name ."' 
			GROUP BY page.page_title
			ORDER BY page.page_title";		
	else
		$query = "SELECT page_title 
				FROM page 
				  JOIN templatelinks ON tl_from = page_id 
				WHERE tl_namespace = 10 
				AND tl_title = '". $tpl_name ."' 
				AND page_namespace = 0";
	
	// mysql инициация
	$mysql_init = "SET NAMES 'utf8'; SET CHARACTER SET 'utf8'; SET SESSION collation_connection = 'utf8_general_ci'; SET TIME_ZONE = '+03:00'";

	// $con = ssh2_connect();
	$mysqli = new mysqli($dbhost,$config['user'],$config['password'],$dbtable) OR DIE("Не могу создать соединение ");
	$mysqli->query($mysql_init);

	$p = $mysqli->query($query);
		if ($p) {
			$arr_list = array();
			// while ($row = $p->fetch_assoc())
				// foreach ($row as $k) $arr_list[] = $k;

			switch ($_GET['format']) {
				case 'html':				
					print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'/></head><body><ul>\n";
					foreach ($arr_list as $k) print "<li>".$k."</li>\n";
					print "</ul></body></html>";
					break;
				case 'plain':
					foreach ($arr_list as $k) print $k."\n";
					break;					
				case 'json':
					while ($row = $p->fetch_assoc())
						// print_r($row);						
						if ($_GET['get_timelastedit'])
							// $arr_list[] = array(utf2str($row[page_title]), $row[timestamp]);
							$arr_list[] = array($row[page_title], $row[timestamp]);
						else
							// $arr_list[] = utf2str($row[page_title]);
							$arr_list[] = $row[page_title];
					
					print json_encode($arr_list);
					break;
			}
		}	else print $mysqli->error;

	$mysqli->close();

} else echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>
<p><br></p>
<table width="70%" align="center"><td>
<h3>Описание / Description</h3>
<p>Возвращает включения шаблона в формате utf-8.<br>
Return transcludes of template in format utf-8.</p>

<h3>Параметры / Parameters GET</h3>
<ul>
<li><b>lang</b> - язык wiki.</li>
<li><b>template</b> - первый символ автоконвертируется в верхний регистр, как заголовки хранятся в базе данных / first letter autoconverting to upper case, as titles stored in databasr.</li>
<li><b>format</b> - одно из следующих значений | a next value:
	<ul>
	<li><b>plain</b> - разделитель|delimeter "\\n" (перевод строки|new line)</li>
	<li><b>html</b> - разделитель|delimeter "&lt;li&gt;&lt;/li&gt;".</li>
	<li><b>json</b></li>
	</ul>
</li>
<li><b>get_timelastedit</b> (json) - взять время последней правки.</li>
</ul>

<h3>Пример / Example</h3>
<blockquote><b>http://tools.wmflabs.org/ruwikisource/WDBquery_transcludes_template?lang=ru&template=Sfn0&format=html</b></blockquote>

<p><br></p>
© <a href="https://ru.wikipedia.org/wiki/User:Vladis13">Vladis13</a>
</td>
</table>
</body></html>';
