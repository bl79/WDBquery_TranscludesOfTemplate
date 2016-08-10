<?php

$_GET['lang'] = 'ru';
$_GET['template'] = 'sfn0';
$_GET['format'] = 'plain'; // 'plain';

if (isset($_GET['lang']) and isset($_GET['template']) and isset($_GET['format'])) {
//	 $dbhost = $_GET['lang'] . 'wiki.labsdb';
//	 $dbuser = 'u14134';
//	 $dbpw = '4mlodHqAsy3xTivW';
//	 $dbtable = $_GET['lang'] . 'wiki_p';
//
//	//$query = "SELECT page_namespace, page_title FROM page WHERE page_namespace = 0 LIMIT 10";
//	// $query = "SELECT page_title FROM page WHERE page_namespace = 0 LIMIT 10";
//	//$query = 'SELECT page_namespace, page_title FROM page JOIN templatelinks ON tl_from = page_id WHERE tl_namespace = 10 AND tl_title = "Sfn0" AND page_namespace = 0';
//	$query = 'SELECT page_title FROM page JOIN templatelinks ON tl_from = page_id WHERE tl_namespace = 10 AND tl_title = "'. $_GET['template'] .'" AND page_namespace = 0';

	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpw = '';
	$dbtable = 'test';
	$query = "SELECT * FROM test";

	// mysql инициация
	$mysql_init = "SET NAMES 'utf8'; SET CHARACTER SET 'utf8'; SET SESSION collation_connection = 'utf8_general_ci'; SET TIME_ZONE = '+03:00'";

	// $con = ssh2_connect();
	$mysqli = new mysqli($dbhost,$dbuser,$dbpw,$dbtable) OR DIE("Не могу создать соединение ");
	$mysqli->query($mysql_init);

	$p = $mysqli->query($query);
		if ($p) {
			$arr_list = array();
			while ($row = $p->fetch_assoc())
				foreach ($row as $k) $arr_list[] = $k;

			switch ($_GET['format']) {
				case 'plain':
					foreach ($arr_list as $k) print $k."\n";
					break;
				case 'html':
					print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'/></head><body><ul>\n";
					foreach ($arr_list as $k) print "<li>".$k."</li>\n";
					print "</ul></body></html>";
					break;
				case 'json':
					while ($row = $p->fetch_assoc())
						foreach ($row as $k) $arr_list[] = $k;
					print json_encode($arr_list);
					break;
			}
		}	else print $mysqli->error;

	$mysqli->close();

}
