<?php // Stan 15 сентября 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

////////////////////////////////////////////////////////////////
// Для этого скрипта в сообщении всегда указываем полный путь //
////////////////////////////////////////////////////////////////

if ( !isset( $shell_access ) OR $shell_access != "$user+$pw_user" )
  return -5;

include_once 'func_mail.php';	// mail_content в конце скрипта и в ls
include_once 'func_other.php';	// list_dir и clear_dir

$mailing = 0;		// Вывод шелла может быть отправлен на мыло
$content = '';		// для этого буферизуем в эту переменную
for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
  //$params = escapeshellarg( $params );
  echo "---\n$cmd: \"$params\"\n";
  switch( $cmd ) {
    case 'sh':			// Выполнить команду шелла
      echo $mcontent = `$params`;
      $content .= $mcontent;
      break;
    case 'mailing':		// изменение переменной
      $mailing = $params;
      break;
    case 'ls':			// отправить на мыло дерево директорий
      if ( is_dir( $params ) ) {
        ob_start();
        list_dir( $params );
        $str = ob_get_contents();
        ob_end_clean();
        if ( $str ) {
          $len = strlen( $str );
          if ( mail_content( $mail_to[$user], $str, 'ls.txt' ) )
            echo "Полный список ($len) файлов по $params отправлен!\n";
        } else
          echo "$params - по всей видимости, директория пуста или нет прав доступа\n";
      } else
        echo "$params - не директория\n";
      break;
    case 'remove_dir':		// УДАЛИТЬ ПАПКУ со всеми вложениями (осторожно!)
    case 'remove_contents':	// УДАЛИТЬ СОДЕРЖИМОЕ папки (осторожно!)
      if ( is_dir( $params ) ) {
        if ( $params != TEMP_PATH AND $params != USER_DIR ) {
          if ( clear_dir( $params, 1 ) ) {
            if ( $cmd == 'remove_dir' ) {
              echo $mcontent = "удаляем $params\n";
              rmdir( $params );
            } else
              $mcontent = '';
          } else
            echo $mcontent = "не удалось полностью удалить $params\n";
        } else
          echo $mcontent = "$params - системная папка, не удаляем\n";
      } else
        echo $mcontent = "$params - нет такой папки\n";
      $content .= $mcontent;
      break;
    case 'clean_temp_dir':	// очистить временную папку
      echo $mcontent = "очищаем Temp\n";
      $content .= $mcontent;
      clear_dir( TEMP_PATH, 1 );
      break;
    default:
      echo " - ничего не делаем\n";
  }; // switch
}; // for

if ( $mailing AND $content )
  mail_content( $mail_to[$user], $content, 'shell.txt' );
?>
