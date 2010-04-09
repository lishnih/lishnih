<?php // Stan http://usede.net 9 августа 2004г.

   define ( 'PHP_COMMON', 1 );
   define ( 'ERROR',   E_USER_ERROR   );
   define ( 'WARNING', E_USER_WARNING );
   define ( 'NOTICE',  E_USER_NOTICE  );

   error_reporting ( E_ALL );
// Ошибки, которые будут выводиться на экран

// define ( 'ERRORS_EMAIL_TO',  'Stan <ngsc-smirnykh@sk2.ru>' );
   define ( 'ERRORS_SEND_FLAG', E_USER_ERROR );
// define ( 'ERRORS_SEND_FLAG', E_WARNING | E_NOTICE | ERROR | WARNING | NOTICE );
// Если произойдёт любая из указанных ошибок, то будет отправлен на отчёт
// Получателя ERRORS_EMAIL_TO предпочтительнее объявлять перед вызовом common.php
// Закомментируйте мыло или флаги, если не хотите получать отчёты

// print_r( get_defined_constants() );
////////////////////////////////////////////////////////////////////////////////
//////// Установить функцию, которая выполняется по завершению процесса ////////
////////////////////////////////////////////////////////////////////////////////
// Эта функция объявлена для того чтобы она пыталась отправить все
// накопившиеся по ходу выполнения сообщения об ошибках на мыло
// при нормальном завершении переменная $common_errors пуста
if ( defined( 'ERRORS_EMAIL_TO' ) AND defined( 'ERRORS_EMAIL_TO' ) ) {
  function UserExitHandler ( ) {
  global $common_errors;
    if ( $common_errors ) {	// Если есть что отсылать
      // Определяем, нужно ли отправлять отчёт, заодно преобразовываем массив
      $need_to_send = 0;
      $message  = "Common Версия 0.23.3 5 апреля 2007г.\n";
      $message .= date( 'd.m.Y H:i O' ) . "\n";
      $message .= "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\n-----\n";
      while ( list( $key, $value ) = each( $common_errors ) ) {
        while ( list( $k2, $v2 ) = each( $value ) ) {
          $message .= "$k2: $v2\n";
          if ( !strcmp( $k2, 'ErrNo' ) AND ( ERRORS_SEND_FLAG & $v2 ) )
            $need_to_send = 1;
        }; // while
        $message .= "-----\n";
      }; // while
      $message .= "\n";
      return $need_to_send ? mail( ERRORS_EMAIL_TO, 'Common Errors', $message ) : 0;
    }; // if
  }
  register_shutdown_function( 'UserExitHandler' );
} // if

//////////////////////////////////////////////////////
//////// Устанавливаем свой обработчик ошибок ////////
//////////////////////////////////////////////////////
//        E_ALL              Все предупреждения и ошибки.
//    1 ! E_ERROR            Критические ошибки времени выполнения.
//    2   E_WARNING          Предупреждения времени выполнения.
//    4 ! E_PARSE            Ошибки трансляции.
//    8   E_NOTICE           Замечания времени выполнения (это такие предупреждения, которые,
//                           скорее всего, свидетельствуют о логических ошибках в сценарии,
//                           например, использовании, неинициализированной переменной).
//   16 ! E_CORE_ERROR       Критические ошибки в момент старта PHP.           ( PHP 4 )
//   32 ! E_CORE_WARNING     Некритические предупреждения во время старта PHP. ( PHP 4 )
//   64 ! E_COMPILE_ERROR    Критические ошибки времени трансляции.            ( PHP 4 )
//  128 ! E_COMPILE_WARNING  Предупреждения времени трансляции.                ( PHP 4 )
//  256   E_USER_ERROR       Сгенерированные пользователем ошибки.             ( PHP 4 )
//  512   E_USER_WARNING     Сгенерированные пользователем предупреждения.     ( PHP 4 )
// 1024   E_USER_NOTICE      Сгенерированные пользователем уведомления.        ( PHP 4 )
//      ^ - ошибки, которые не вызывают пользовательскую функцию обработчика ошибок
function UserErrorHandler ( $errno, $errstr, $errfile, $errline ) {
  global $common_errors;
  // Cкрываем путь скрипта от пользователя
  $errfile = basename( $errfile );
  // Любую ошибку заносим в массив ошибок
  // Этот массив будет выводится при возникновении E_USER_ERROR
  if ( !error_reporting() ) {	// скрытая ошибка ( @команда ) - не выводим
    $common_errors[] = array( 'ErrNo' => $errno, 'ErrStr' => '@[ ' . $errstr . ' ]',
                              'ErrFile' => $errfile, 'ErrLine' => $errline );
    // Если error_reporting установить в ноль, то E_USER_ERROR (в случае возникновения)
    // не будет получать управление - делаем дополнительную проверку сами
    if ( $errno & E_USER_ERROR ) exit();
    return 0;	// не выводим ошибки в @командах
  } else
    $common_errors[] = array( 'ErrNo' => $errno, 'ErrStr' => $errstr,
                              'ErrFile' => $errfile, 'ErrLine' => $errline );
  switch ( $errno ) {
  // Все четыре последующие варианта просто выведут маркер ошибки
  // Просто я ещё не придумал чем различить WARNING от NOTICE
    case E_WARNING:
      $errtype = 'E_WARNING'; $marker = '|W|'; $color = 'red';     break;
    case E_NOTICE:
      $errtype = 'E_NOTICE';  $marker = '|N|'; $color = 'yellow';  break;
    case E_USER_WARNING:
      $errtype = 'WARNING';   $marker = '|w|'; $color = 'red';     break;
    case E_USER_NOTICE:
      $errtype = 'NOTICE';    $marker = '|n|'; $color = 'yellow';  break;
    case E_USER_ERROR:		// Это фатальная ошибка - выполнение скрипта прекратится
      @ob_end_clean();	// Ничего выводится на броузер не будет, кроме описания ошибки
      print_rt( $common_errors );
      exit(); break;
    default:
      $errtype = 'Unknown code'; $marker = '||'; $color = 'yellow';
      $common_errors[] = array( 'ErrNo' => '', 'ErrStr' => 'Unknown previous error!',
                                'ErrFile' => '', 'ErrLine' => '' );
      break;
  }; // switch

  // Формат вывода сообщения по умолчанию
  $style = "style=\"color:$color; background-color:black\"";
  if ( preg_match( '/(\+?)(\{([^|}]*)[|]?(.*)\})?(.*[^^]?)(\^)?/i', $errstr, $matches ) ) {
  // $1 +, $2 {...}, $3 color $4 bgcolor $5 Сообщение $6 ^ (если нет то не существует)
  // Одна недоработка: нельзя вообще использовать "^", а не только в конце, как задумано
    //print_ra( $matches );
    if ( $matches[1] == '+' OR defined( 'ALWAYSFULLSTRING' ) )
      $marker = $matches[5];
    if ( $matches[3] && $matches[4] )	// Заданы оба цвета
      $style = "style=\"color:$matches[3]; background-color:$matches[4]\"";
    elseif ( $matches[3] )		// Задан Color
      $style = "style=\"color:$matches[3]\"";
    elseif ( $matches[4] )		// Задан BgColor
      $style = "style=\"background-color:$matches[4]\"";
    elseif ( $matches[2] )		// Если скобочки пустые
      $style = '';			// то сообщение не будет выделяться
    $errstr = $matches[5];
    if ( isset( $matches[6] ) OR defined( 'ALWAYSBRSTRING' ) )	// Если переменная существует, значит ^ найден
      $marker .= "<br />\n";
  }; // if

  // Выводим на экран требуемые ошибки и предупреждения
  if ( error_reporting() & $errno )
    echo "<span $style title=\"" .
         htmlspecialchars( "[ $errtype ]     [ $errstr ]     [ $errfile ]     [ line: $errline ]" ) .
         "\">$marker</span>\n";
  return 1;
}
// Создаём массив для хранения инфы об ошибках
$common_errors = array();
// сохраняем стандартный обработчик ошибок
$old_error_handler = set_error_handler( 'UserErrorHandler' );

/////////////////////////////////////////
//////// Функция вывода массивов ////////
/////////////////////////////////////////
// Эта функция подобна print_r
function print_rb ( $array, $style = '' ) {
  $rb = array();
  for ( $i = 0; $i < func_num_args(); $i++ ) {
    echo "<br />\n=== $i: ===";
    print_ra( func_get_arg( $i ) );
    $rb[$i] = func_get_arg( $i );
    echo "\n======";
  }; // for
  return $rb;
} // function

// Эта функция подобна print_r
// $htmlchars = 0 - ничего не делаем (по умолчанию)
// $htmlchars = 1 - преобразуем теги в видимый текст
// $htmlchars = 2 - вырезаем теги
function print_ra ( $array, $htmlchars = 0, $style = '' ) {
    if ( is_object( $array ) OR is_array( $array ) ) {
      echo $style ? "\n<table $style>\n" : "\n<table border=1>\n";
      while ( list( $key, $val ) = each( $array ) )
        if ( is_array( $val ) OR is_object( $val ) ) {
          echo "<tr><td $style>$key<td $style>";
          print_ra( $val, $htmlchars, $style );
        } else {
          switch ( $htmlchars ) {
            case 1:	$val = htmlspecialchars( $val );
			break;
            case 2:	$val = strip_tags( $val );
			break;
            default:	if ( $val === False )
                          $val = '<i>False</i>';
          }; // switch
          echo "<tr><td $style>$key<td $style><pre>$val</pre>\n";
        }; // if
      echo "</table>\n";
    } else {
      switch ( $htmlchars ) {
        case 1:		$array = htmlspecialchars( $array );
			break;
        case 2:		$array = strip_tags( $array );
			break;
        default:	if ( $array === False )
                          $array = '<i>False</i>';
      }; // switch
      // Если задан не массив, то выводим обрамлённый текст
      echo "<pre>\n$array</pre>\n";
    }; // if
    return 1;
} // function

// Эта функция выводит структурированные массивы в виде таблицы
// принимает только массивы вида array( 0 => array(), 1 => array() )
function print_rt ( $array, $htmlchars = 0, $style = '' ) {
  if ( is_array( $array ) ) {
    if ( !is_array( current( $array ) ) )
      $array = array( 0 => $array );
    echo $style ? "\n<table $style>\n" : "\n<table border=1>\n<tr>\n";
    $a_keys = array_keys( current( $array ) );
    echo " <th $style>#\n";
    while ( list( $field, $fieldname ) = each( $a_keys ) )
      echo " <th $style>$fieldname\n";
    while ( list( $field, $one ) = each( $array ) ) {
      if ( is_array( $one ) ) {
        echo "<tr>\n <td $style><i>$field</i>\n";
        while ( list( $field, $fieldname ) = each( $one ) )
          if ( is_array( $fieldname ) OR is_object( $fieldname ) ) {
            echo ' <td $style>';
            print_ra( $fieldname, $htmlchars, $style );
          } else {
            switch ( $htmlchars ) {
              case 1:	$fieldname = htmlspecialchars( $fieldname );
			break;
              case 2:	$fieldname = strip_tags( $fieldname );
			break;
              default:
            }; // switch
            if ( strlen( $fieldname ) > 20 )
              $fieldname = substr( $fieldname, 0, 20 ) . '<span title="' . $fieldname . '">...</span>';
            echo " <td $style>$fieldname\n";
          }; // if
      } else // if
        echo "<tr>\n <td $style>" . ( $one ? $one : '<i>empty</i>' ) . "\n";
    }; // while
    echo "</table>\n";
    return 1;
  } else // if
    return 0;
} // function

// Эта функция выводит hex-коды символов строки
function print_sh ( $str, $c = 16 ) {
  echo '<table border="1"><tr><td><pre>';
  for ( $i = 0; $i < strlen( $str ); $i++ ) {
    if ( $i AND $i % $c == 0 )
      echo "\n";
    echo sprintf( '%02X', ord( $str[$i] ) ) . ' ';
  }; // for
  echo '</pre><td><pre>';
  for ( $i = 0; $i < strlen( $str ); $i++ ) {
    if ( $i AND $i % $c == 0 )
      echo "\n";
    if ( ord( $str[$i] ) < 32 )
      echo '°';
    else
      echo $str[$i];
  }; // for
  echo '</pre></table>';
} // function


///////////////////////////////////////////////////
/////////////// Функция открытия БД ///////////////
///////////////////////////////////////////////////
function open_usede_db ( ) {
  $conn = @mysql_connect( USE_DB_HOST, USE_DB_USER, USE_DB_PASSWD )
    or user_error( mysql_errno().': '.mysql_error(), E_USER_ERROR );
  mysql_select_db( USE_DB_NAME, $conn )
    or user_error( mysql_errno().': '.mysql_error(), E_USER_ERROR );
  return $conn;
} // function

ob_start( 'ob_gzhandler' );	// Добавляем сжатие страницы
/*****************************************************************************
  Версия 0.01 9 августа 2004г.
  Набросал common.php - скрипт для улучшение отображения выводимых скриптами
  ошибок и предупреждений и отправление логов на заданное мыло
  Стараюсь вносить в модуль минимум изменений

  Версия 0.11 12 июня 2006г.
  За всё это время исправил некоторые ошибки, убрал офорление, добавил
  к существующей функции вывода массивов ещё одну

  Версия 0.21 19 сентября 2006г.
  Отправление на мыло никогда не проверялось. Занялся этой частью, и добавил
  несколько возможностей логирования, добавил сжатие страниц (это оказалось
  элементарно :))

  Версия 0.23 - 25 сентября 2006г.
  Введён формат '+{color|bgcolor}Текст^' для user_error()
  + - Вместо минимаркера выведется Текст
  color и bgcolor - можно задать цвет текста
  ^ - Если стоит в конце строки (тут недоделка маленькая, ищется во всей строке
   и обрезается), то будет добавлено <br />

  Версия 0.23.1 - 24 октября 2006г.
  Улучшил print_rt (теперь может выводить массивы с одним рядом значений)

  Версия 0.23.3 - 5 апреля 2007г.
  Добавил флаг $htmlchars в print_ra и print_rt

  Версия 0.23.5 - 23,30 апреля 2007г.
  Добавил print_sh
  теперь print_rt укрощяет длиные строки
*****************************************************************************/
?>
