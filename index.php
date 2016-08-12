<?php

echo str_replace(' ', '_', ucfirst('вершины Каменного Пояса'));


if (isset($_GET['lang']) and isset($_GET['template']) and isset($_GET['format'])) {

	$dbhost = $_GET['lang'].'wiki.labsdb';
	$dbtable = $_GET['lang'].'wiki_p';
	$config = parse_ini_file('password.ini');
	
	$_GET['template'] = str_replace(' ', '_', ucfirst($_GET['template']));
	
	$query = 'SELECT page_title FROM page JOIN templatelinks ON tl_from = page_id WHERE tl_namespace = 10 AND tl_title = "'. $_GET['template'] .'" AND page_namespace = 0';

	// mysql инициация
	$mysql_init = "SET NAMES 'utf8'; SET CHARACTER SET 'utf8'; SET SESSION collation_connection = 'utf8_general_ci'; SET TIME_ZONE = '+03:00'";

	// $con = ssh2_connect();
	$mysqli = new mysqli($dbhost,$config['user'],$config['password'],$dbtable) OR DIE("Не могу создать соединение ");
	$mysqli->query($mysql_init);

	$p = $mysqli->query($query);
		if ($p) {
			$arr_list = array();
			while ($row = $p->fetch_assoc())
				foreach ($row as $k) $arr_list[] = $k;

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
						foreach ($row as $k) $arr_list[] = $k;
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
<li><b>template</b> - возможно надо указать точно регистр первой буквы, иначе страниц не найдётся. / perhaps should to certain case of first letter of template for search pages.</li>
<li><b>format</b> - одно из следующих значений | a next value:
	<ul>
	<li><b>plain</b> - разделитель|delimeter "\\n" (перевод строки|new line)</li>
	<li><b>html</b> - разделитель|delimeter "&lt;li&gt;&lt;/li&gt;".</li>
	<li><b>json</b></li>
	</ul>
</li>
</ul>

<h3>Пример / Example</h3>
<blockquote><b>http://tools.wmflabs.org/ruwikisource/WDBquery_transcludes_template?lang=ru&template=Sfn0&format=html</b></blockquote>

<p><br></p>
© <a href="https://ru.wikipedia.org/wiki/User:Vladis13">Vladis13</a>
</td>
</table>
</body></html>';
