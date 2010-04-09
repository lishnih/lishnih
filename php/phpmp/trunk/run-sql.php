<?php // Stan 22.01.2007

$dbi = mysql_connect( 'localhost', 'root', '54321' );
mysql_select_db( 'neoart10_2006' );

$filename = 'appendix/neoart10_2006.sql';
if ( !is_file( $filename ) )
  exit( 'Файл не найден!' );

include 'common.php';
include 'sql_parse.php';
function getmicrotime ( ) { 
  list( $usec, $sec ) = explode( ' ', microtime() ); 
  return ( (float)$usec + (float)$sec ); 
} // мануал по PHP

echo "Старт...<br />\n";
$sql_query = file_get_contents( $filename );
$time_start = getmicrotime();
// Взято из phpbb 2.0.21
if ( $sql_query != '' ) {
  $sql_query = remove_remarks( $sql_query );
  $pieces = split_sql_file( $sql_query, ';' );
  $sql_count = count($pieces);
  for ( $i = 0; $i < $sql_count; $i++ ) {
    $sql = trim($pieces[$i]);
    if ( !empty( $sql ) and $sql[0] != '#' ) {
      if ( 0 ) {
        echo "Executing: $sql\n<br>";
        flush();
      }
      //$result = $db->sql_query( $sql );
      $result = mysql_query( $sql );
      if( !$result )
        user_error( mysql_errno() . ': ' . mysql_error() . "<br />\n$sql", ERROR );
    }
  }; // for
}; // if
$time_end = getmicrotime();
$time = $time_end - $time_start;
echo "Стоп!<br />\nВсего $time секунд.<br />\n";

//print_r( split_sql_file( $sql_query, ';' ) );
?>
