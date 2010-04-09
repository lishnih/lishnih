<?php // Stan 12 сентября 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

include_once 'func_mail.php';	// mail_content и mail_file
include_once 'func_other.php';	// clear_dir

// Объявляем переменные по умолчанию
$compress = 0;		// паковать временную папку перед отправкой?
$level    = 0;		// Сколько уровней гиперссылок? (пока не реализовано)
$subject  = '';		// тема письма при отправлении html-запросов
$report   = '';		// миниотчёт о загруженных html и load
// Учтите, что крупные файлы могут разбиваться на части и отправляться отдельно. Такие файлы
// нужно будет собирать вручную (путём добавления частей файла из писем в конец первого письма)

// Создаём или чистим временную папку
$dir = TEMP_PATH . '/dload';		// временная папка куда будем складывать скачанные файлы
$dir_size = 0;				// будем считать общий размер файлов
echo "Временная папка $dir - ";
if ( !file_exists( $dir ) ) {		// Нет проверки на то, что перед нами именно папка!!
  if ( mkdir( $dir ) )
    echo "выбрана\n";
  else {
    echo "Ошибка: не могу создать временную папку!\n";
    $dir = '';
  }; // if
} else {				// !!!!!!!!!!!!!! Внимание !!!!!!!!!!!!!!
  clear_dir( $dir );			// Если папка есть, то она будет очищена!
  echo "очищена\n";
}; // if

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
      echo "---\n$cmd: \"$params\"";
      switch( $cmd ) {
//////////////////////////////////////////////////
        case 'google':		// Поиск в Гугле
//////////////////////////////////////////////////
          $params = str_replace( ' ', '+', $params );
          $params = str_replace( '&', '+', $params );
          $params = "http://www.google.com/search?q=$params&sourceid=opera&num=0&ie=cp1251&oe=cp1251";
//////////////////////////////////////////////////
        case 'echo':		// Просто открыть url
//////////////////////////////////////////////////
          if ( $content = file_get_contents( $params ) )
            echo " - Размер: " . strlen( $content ) . "\n<table border=1>\n<tr><td>\n$content\n</table>";
          break;
//////////////////////////////////////////////////
        case 'file':		// Загрузка файла из URL и отправка на мыло
        case 'load':		// Загрузка файла из URL и помещение во временную папку
        case 'html':		// Загрузка html с картинками и помещение во временную папку
//////////////////////////////////////////////////
          echo ' - Файл: ';
          $content = file_get_contents( $params );
          if ( $content ) {
            list( , , $filename, $ext ) = url_split( $params );
            if ( !$filename )		$filename = 'index';	// имя, если не задано
            if ( !$ext )		$ext = 'html';		// расширение, если не задано
            else			$ext = strtolower( $ext );
            if ( $ext == 'php' )	$ext = 'php.html';	// php -> php.html
            if ( $ext == 'php3' )	$ext = 'php3.html';	// ещё встречается
            if ( $ext == 'pl' )		$ext = 'pl.html';	// pl -> pl.html
            if ( $ext == 'asp' )	$ext = 'asp.html';	// asp -> asp.html
            if ( $ext == 'aspx' )	$ext = 'aspx.html';	// aspx -> aspx.html
            if ( $ext == 'cgi' )	$ext = 'cgi.html';	// cgi -> cgi.html
            if ( $ext == 'shtml' )	$ext = 'html';		// shtml -> html
            $len = strlen( $content );
            echo "$filename.$ext($len)";
            if ( $len > MAX_FILE_SIZE ) {	// Если скачанный файл велик - помещаем в Temp
              $fp = fopen( TEMP_PATH."/$filename$len.$ext", 'w' );
              fwrite( $fp, $content );
              fclose( $fp );
              echo ' перемещён в temp!';
              continue;
            }; // if
            if ( $cmd == 'file' ) {		// Если запрошен файл, то сразу отправляем
              mail_content( $mail_to[$user], $content, "$filename.$ext", $subject ? $subject : $params );
              echo " отправлен!\n";
              get_real_url( $params, 1 );
              break;
            }; // if
            // Первый html-файл в письме будет сообщением, запоминаем
            if ( !$subject AND preg_match( '/.*html?/', $ext ) )
              $subject = urldecode( $params );
            if ( file_exists( "$dir/$filename.$ext" ) )	// если в папке есть файл с таким именем
              $filename = $filename.$len;		// то называем по другому
            if ( $fp = fopen( "$dir/$filename.$ext", 'w' ) ) {
              fwrite( $fp, $content );
              fclose( $fp );
              echo ' записан';
              $dir_size += $len;			// считаем размер
              $report .= htmlspecialchars( urldecode( $params ) ) . " -> $filename.$ext\n";	// заносим в отчёт
            } else
              echo ' не записан';
            if ( $cmd == 'load' )		// Если запрошен load, то заканчиваем здесь
              break;
//////////////////////////////////////////////////
            $rurl = get_real_url( $params );
            echo "\nКартинки для $rurl ";
            list( $host, $path, , , $proto ) = url_split( $rurl );
            $pictures = array();	// будем запоминать картинки, чтобы не было повторений

            $all_pict = array();
            if ( preg_match_all( '/<img[^>]*src *= *"([^"]*)"/i',       $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = $matches[1];
            }; // if
            if ( preg_match_all( '/<img[^>]*src *= *\'([^\']*)\'/i',    $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = array_merge( $all_pict, $matches[1] );
            }; // if
            if ( preg_match_all( '/<img[^>]*src *= *([^ "\'][^ >]*)/i', $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = array_merge( $all_pict, $matches[1] );
            }; // if
            if ( preg_match_all( '/background *= *"([^"]*)"/i',         $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = array_merge( $all_pict, $matches[1] );
            }; // if
            if ( preg_match_all( '/background *= *\'([^\']*)\'/i',      $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = array_merge( $all_pict, $matches[1] );
            }; // if
            if ( preg_match_all( '/background *= *([^ "\'][^ >]*)/i',   $content, $matches ) ) {
              print_r( $matches[1] );
              $all_pict = array_merge( $all_pict, $matches[1] );
            }; // if
            //print_r( $all_pict );

            if ( $all_pict ) {
              while( list( $key, $val ) = each( $all_pict ) ) {
                // могут встречаться \" и \' (картинки в скриптах), попробовал исправить так
                if ( substr( $val, 0, 2 ) == '\\"' OR substr( $val, 0, 2 ) == '\\\'' )
                  $val = substr( stripslashes( $val ), 1, -1 );
                // убираем символы \n и \r
                $val = str_replace( "\n", '', $val );
                $val = str_replace( "\r", '', $val );
                if ( $val AND !isset( $pictures[$val] ) AND !strstr( $val, '"' ) AND !strstr( $val, '\'' ) ) {
                  $pictures[$val] = basename( $val );	// присваеваем имя
                  if ( preg_match( '/^ *(https?:\/\/.*)$/m', $val ) ) {
                    $link = $val;
                    list( , , $pname, $pext ) = url_split( $val );
                    $pictures[$val] = $pext ? "$pname.$pext" : $pname;
                  } elseif ( preg_match( '/^\//', $val, $link ) )
                    $link = "$proto://$host$val";
                  else
                    $link = "$proto://$host/$path/$val";
                  if ( $link AND $picture = file_get_contents( $link ) ) {	// сохраняем картинку
                    $len = strlen( $picture );
                    if ( file_exists( "$dir/{$pictures[$val]}" ) )	// если в папке есть файл с таким именем
                      $pictures[$val] = $len.$pictures[$val];		// то называем по другому
                    if ( $fp = fopen( "$dir/{$pictures[$val]}", 'w' ) ) {
                      $dir_size += $len;				// считаем размер
                      fwrite( $fp, $picture );
                      fclose( $fp );
                      $content = str_replace( $val, "cid:{$pictures[$val]}", $content );
                    }; // if
                  } else
                    echo "[ $link ]\n";
                }; // if
              }; // while
              print_r( $pictures );

              if ( $fp = fopen( "$dir/$filename.$ext", 'w' ) ) {
                fwrite( $fp, $content );	// записываем с изменениями
                fclose( $fp );
              }; // if
            }; // if
//////////////////////////////////////////////////
          } else
            echo 'пуст!';
          break;
//////////////////////////////////////////////////
        case 'head':		// Получить header из $params
//////////////////////////////////////////////////
          echo "\n";
          $str = get_real_url( $params, 1 );
          if ( $str != $params )
            echo "Адрес: \"$str\"";
          break;
//////////////////////////////////////////////////
        default:		// Если не команда - объявляем переменную
//////////////////////////////////////////////////
        switch( $cmd ) {
          case 'compress':
          case 'level':
          case 'subject':
            echo ' - объявляем переменную';
            ${$cmd} = $params;
            break;
          default:
            echo ' - ничего не делаем';
        }; // switch
      }; // switch
      echo "\n";
}; // for

// Отправляем всё, что помещено во временную папку
if ( $dir_size ) {
  echo "Суммарный размер каталога: $dir_size\n";
  if ( !$subject )		// Если html-запросов не было
    $subject = $params;
  if ( $compress ) {
    include_once 'Tar.php';		// Используем формат Tar.Gz
    $arc_name = TEMP_PATH.'/dload.tgz';
    $Tar = new Archive_Tar( $arc_name, 1 );
    if ( $Tar->createModify( $dir, '', $dir ) )
      mail_file( $mail_to[$user], $arc_name, $subject );
  } else
      mail_file( $mail_to[$user], $dir, $subject, $report );
}; // if
?>
