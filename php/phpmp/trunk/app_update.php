<?php // Stan 15 сентября 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

if ( !isset( $update_access ) OR $update_access != "$user+$pw_user" )
  return -5;

include_once 'func_mail.php';		// в конце скрипта, save, send_tar
include_once 'Tar.php';			// Используем формат Tar.Gz
$unsent = 0;				// для команды save_tar
$exclude = array();			// для команды exclude и save

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
      echo "$cmd: \"$params\"";
      switch( $cmd ) {
//////////////////////////////////////////////////
        case 'update':		// параметр - имя папки относительно USER_DIR
        case 'extract':		// куда складывать файлы
//////////////////////////////////////////////////
          if ( is_dir( $dir = "$update_dir/$params" ) ) {
            echo " -> $dir";
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
                    case 4:  echo ' (QUOTED-PRINTABLE)';// преобразуем
                      $body = quoted_printable_decode( $body );
                      break;
                    case 5:  echo ' (OTHER) - не обновляем';
                      continue 2;
                      break;
                    default: echo ' (UNKNOWN) - не обновляем';
                      continue 2;
                  }; // switch
                  if ( $fp = fopen( "$dir/$obj->value", 'w' ) ) {
                    fwrite( $fp, $body );
                    fclose( $fp );
                    if ( $cmd == 'extract' ) {
                      $Tar = new Archive_Tar( "$dir/$obj->value", 1 );	// указываем архив
                      $Tar->extract( $dir );				// распаковываем
                      if( !unlink( "$dir/$obj->value" ) ) 		// удаляем архив
                        echo ' <b>Внимание! Архив не удалился!</b>';
                    }; // if
                  }; // if
                }; // if
              echo `ls -l $dir`;
            } else
              echo " - Это простое письмо";
          } else
            echo ' - Директории не существует!';
          break;
//////////////////////////////////////////////////
        case 'exclude':		// параметр - папка/файл-исключения
//////////////////////////////////////////////////
          $exclude = explode( ' ', $params );
          echo " - исключаем в save данный список";
          break;
//////////////////////////////////////////////////
        case 'save':		// параметр - папка/файл для отправки относительно $update_dir
//////////////////////////////////////////////////
          if ( file_exists( $dir = "$update_dir/$params" ) ) {
            // Создаём архив
            $arc_name = TEMP_PATH . '/save.tgz';
            echo " - Сохраняем и отправляем $dir ". ( is_dir( $dir ) ? '(dir)' : '(file)' );
            $Tar = new Archive_Tar( $arc_name, 1 );
            // Если заданы исключения - строим список файлов
            if ( $exclude AND is_dir( $dir ) ) {
              if ( $d = dir( $dir ) ) {
                $list = array();
                while ( false !== ( $entry = $d->read() ) )
                  if ( $entry != '.' AND $entry != '..' AND array_search( $entry, $exclude ) === FALSE )
                    $list[] = "$dir/$entry";
                $d->close();
              }; // if
            } else
              $list = $dir;
            // Добавляем данные в архив
            if ( $Tar->createModify( $list, '', dirname( $dir ) ) ) {
              mail_file( $mail_to[$user], $arc_name, $dir );
              echo "\nРазмер архива: " . filesize( $Tar->_tarname );
            }; // if
          } else
            echo ' - Директории/файла не существует!';
          $exclude = array();	// сбрасываем массив
          break;
//////////////////////////////////////////////////
        case 'save_tar':	// добавляет к архиву каталоги, параметр - то же что и к save
//////////////////////////////////////////////////
          $unsent = 1;		// на случай если не задана команда send_tar
          if ( file_exists( $dir = "$update_dir/$params" ) ) {
            $arc_name = TEMP_PATH.'/save.tar';
            echo " - Сохраняем $dir ". ( is_dir( $dir ) ? '(dir)' : '(file)' );
            if ( !isset( $STar ) )
              $STar = new Archive_Tar( $arc_name );
            $STar->addModify( $dir, '', dirname( $dir ) );
          } else {
            $arc_name = '';
            echo ' - директории/файла не существует!';
          }; // if
          break;
//////////////////////////////////////////////////
        case 'send_tar':	// отправляет архив созданный save_tar, нет параметров
//////////////////////////////////////////////////
          if ( $arc_name ) {
            echo " - Отправляем $arc_name\n";
            mail_file( $mail_to[$user], $arc_name, $dir );
            unlink( $arc_name );
          } else
            echo ' - архив не найден!';
          $unsent = 0;
          break;
//////////////////////////////////////////////////
        default:
//////////////////////////////////////////////////
          echo ' - пропускаем';
      }; // switch
      echo "\n";
}; // for

if ( $unsent AND $arc_name ) {
  echo "Отправляем $arc_name\n";
  mail_file( $mail_to[$user], $arc_name, $dir );
  unlink( $arc_name );
}; // if
?>
