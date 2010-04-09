<?php // Stan 5 декабря 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

include_once 'func_mail.php';		// отправка сообщения
include_once 'func_other.php';		// url_split

$dbi = 0;	// Номер соединения с базой данных
$dbn = '';	// Выбранная база данных (select)

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
  echo "---\n$cmd: \"$params\"";
  switch( $cmd ) {
//////////////////////////////////////////////////
    case 'open':			// параметр - номер записи о сервере
//////////////////////////////////////////////////
      $myhost = $mydbserver[$user][$params]['dbhost'];
      $myuser = $mydbserver[$user][$params]['dbuser'];
      $mypw   = $mydbserver[$user][$params]['password'];
      echo " - устанавливаем соединение с $myhost/$myuser";
      if ( !$dbi = mysql_connect( $myhost, $myuser, $mypw ) ) {
        echo "Соединение прошло безуспешно!\n";
        return -1;
      }; // if
      break;
//////////////////////////////////////////////////
    case 'dblist':			// нет параметров
//////////////////////////////////////////////////
      echo " - выводим список доступных баз данных для $dbi";
      $k = 1;
      $db_list = mysql_list_dbs( $dbi );
      while ( $row = mysql_fetch_object( $db_list ) ) {
        echo "\n$k: " . $row->Database;
        $k++;
      }; // while
      break;
//////////////////////////////////////////////////
    case 'export':			// параметр - имя базы данных
//////////////////////////////////////////////////
      echo " - экспортируем базу данных ($dbi)\n";
      if ( !mysql_select_db( $params ) ) {
        echo "Соединение с базой прошло безуспешно!\n";
        return -2;
      }; // if
      $content = '';
      $result = mysql_list_tables( $params );
      while( $row = mysql_fetch_row( $result ) ) {
        $content .= "#------------------------------------------------------\n";
        $content .= "# Таблица $row[0]\n";
        $content .= "#------------------------------------------------------\n";
        $mytable = $row[0];
        $result2 = mysql_query( "SHOW CREATE TABLE $mytable" );
        $row2 = mysql_fetch_row( $result2 );
        $content .= "# структура:\n";
        $content .= $row2[1] . ";\n# данные:\n";
        $result2 = mysql_query( "SELECT * FROM `$mytable`" );
        $str = '';
        while ( $row2 = mysql_fetch_row( $result2 ) ) {
          $str .= "INSERT INTO `$mytable` VALUES (";
          $comma = 0;
          while ( list( $key, $value ) = each( $row2 ) ) {
            if ( $comma )	$str .= ', ';
            else		$comma = 1;
            $str .= $value === NULL ? 'NULL' : chr(0x27) . mysql_escape_string( $value ) . chr(0x27);
          }; // while
          $str .= ");\n";
        }; // while
        if ( $str )
          $content .= "$str\n";
        else
          $content .= "# пусто!\n\n";
      }; // while
      if ( $content ) {
        $len = strlen( $content );
        if ( mail_content( $mail_to[$user], $content, "$params.sql" ) )
          echo "Файл $params.sql($len) отправлен.";
        mysql_free_result( $result2 );
      } else
        echo "База данных $params пуста.";
      mysql_free_result( $result );
      break;
//////////////////////////////////////////////////
    case 'import':			// параметр - имя базы данных
      include_once 'sql_parse.php';	// Используем библиотеку из PHPBB
//////////////////////////////////////////////////
      echo " - импортируем в базу данных ($dbi)\n";
      if ( !mysql_select_db( $params ) ) {
        echo "Соединение с базой прошло безуспешно!\n";
        return -2;
      }; // if

      $structure = imap_fetchstructure( $mbox, $j );
      if ( $structure->type == 1 ) {	// Если MULTI-PART письмо
        $c = count( $structure->parts );
        for ( $k = 0; $k < $c; $k++ )
          if ( $structure->parts[$k]->ifdparameters ) {
            $obj = $structure->parts[$k]->dparameters[0];
            echo "\n$obj->attribute($k): $obj->value";
            $body = imap_fetchbody( $mbox, $j, (string)( $k + 1 ) );
            switch ( $structure->parts[$k]->encoding ) {
              case 0:  echo ' (7BIT)'; break;	// оставляем как есть
              case 1:  echo ' (8BIT)'; break;	// оставляем как есть
              case 2:  echo ' (BINARY)'; break;	// оставляем как есть
              case 3:  echo ' (BASE64)';		// преобразуем
                $body = base64_decode( $body );
                break;
              case 4:  echo ' (QUOTED-PRINTABLE)';	// преобразуем
                $body = quoted_printable_decode( $body );
                break;
              case 5:  echo ' (OTHER) - пропускаем';
                continue 2;
                break;
              default: echo ' (UNKNOWN) - пропускаем';
                continue 2;
            }; // switch

            // Распаковываем gz-архив
            if ( preg_match( '/(.*)\.([^.]*)$/', $obj->value, $matches ) )
              $ext = $matches[2];
            else
              $ext = '';
            switch ( $ext ) {
              case 'gz':
                // записываем архив
                $filename = TEMP_PATH . '/sql.gz';
                if ( $zp = fopen( $filename, 'w' ) ) {
                  fwrite( $zp, $body );
                  fclose( $zp );
                  // читаем содержимое
                  $body = '';
                  $zp = gzopen( $filename, 'r' );
                  while ( $str = gzread( $zp, 1024 ) )
                    $body .= $str;
                  gzclose( $zp );
                }; // if
                break;
              default:
            }; // switch

            // Взято из phpbb 2.0.21
            $sql_query = $body;
            if ( $sql_query != '' ) {
              $sql_query = remove_remarks( $sql_query );
              $pieces = split_sql_file( $sql_query, ';' );
              $sql_count = count( $pieces );
              for ( $k = 0; $k < $sql_count; $k++ ) {
                $sql = trim( $pieces[$k] );
                if ( !empty( $sql ) and $sql[0] != '#' ) {
                  if ( 0 ) {
                    echo "Executing: $sql\n<br>";
                    flush();
                  }
                  $result = mysql_query( $sql );
                  if ( !$result ) {
                    echo mysql_errno() . ': ' . mysql_error() . "<br />\n$sql";
                    continue 2;
                  }; // if
                }
              }
            }
          }; // if
      } else
        echo 'Это простое письмо';
      break;
//////////////////////////////////////////////////
    case 'select':			// параметр - имя базы данных
//////////////////////////////////////////////////
      echo " - выбор базы данных ($dbi)";
      if ( !mysql_select_db( $params ) ) {
        echo "\nСоединение с базой прошло безуспешно!";
        return -2;
      }; // if
      $dbn = $params;
      break;
//////////////////////////////////////////////////
    case 'delete_table':	// параметр - имя таблицы
// перед использованием нужно вызвать select
//////////////////////////////////////////////////
      echo ' - удаление таблицы';
      if ( $dbn ) {
        if ( !mysql_query( "DROP TABLE $params" ) )
          echo "\nОшибка!";
      } else
        echo "\nБаза данных не выбрана!";
      break;
//////////////////////////////////////////////////
    case 'clear_db':		// нет параметров
// перед использованием нужно вызвать select
//////////////////////////////////////////////////
      echo ' - удаление базы данных';
      if ( $dbn ) {
        $result = mysql_list_tables( $dbn );
        $k = 0;
        while( $row = mysql_fetch_row( $result ) ) {
          echo "\n$row[0] - ";
          if ( mysql_query( "DROP TABLE $row[0]" ) ) {
            echo 'ok!';
            $k++;
         } else
            echo 'Ошибка!';
        }; // while
        echo "\nУдалено таблиц: $k";
      } else
        echo "\nБаза данных не выбрана!";
      break;
//////////////////////////////////////////////////
    default:
//////////////////////////////////////////////////
  }; // switch
  echo "\n";
}; // for

if ( $dbi )
  mysql_close();
?>
