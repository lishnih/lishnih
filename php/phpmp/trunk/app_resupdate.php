<?php // Stan 15 сентября 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

if ( !isset( $supdate_access ) OR $supdate_access != "$user+$pw_user" )
  return -5;

$dir = LOCAL_DIR;
$structure = imap_fetchstructure( $mbox, $j );
if ( $structure->type == 1 ) {	// Если MULTI-PART письмо
  $c = count( $structure->parts );
  for ( $k = 0; $k < $c; $k++ )
    if ( $structure->parts[$k]->ifdparameters ) {
      $obj = $structure->parts[$k]->dparameters[0];
      echo "\n$obj->attribute($k): $obj->value";
      $body = imap_fetchbody( $mbox, $j, (string)( $k + 1 ) );
      switch ( $structure->parts[$k]->encoding ) {
        case 0:  echo ' (7BIT)'; break;		// оставляем как есть
        case 1:  echo ' (8BIT)'; break;		// оставляем как есть
        case 2:  echo ' (BINARY)'; break;	// оставляем как есть
        case 3:  echo ' (BASE64)';		// преобразуем
          $body = base64_decode( $body );
          break;
        case 4:  echo ' (QUOTED-PRINTABLE)';	// преобразуем
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
      } else
        echo " - ошибка при создании файла";
    }; // if
} else
  echo " - Это простое письмо";
echo "\n";
echo `ls -l $dir`;
?>
