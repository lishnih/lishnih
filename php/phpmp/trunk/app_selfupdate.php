<?php // Stan 15 �������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

if ( !isset( $supdate_access ) OR $supdate_access != "$user+$pw_user" )
  return -5;

$dir = LOCAL_DIR;
$structure = imap_fetchstructure( $mbox, $j );
if ( $structure->type == 1 ) {	// ���� MULTI-PART ������
  $c = count( $structure->parts );
  for ( $k = 0; $k < $c; $k++ )
    if ( $structure->parts[$k]->ifdparameters ) {
      $obj = $structure->parts[$k]->dparameters[0];
      echo "\n$obj->attribute($k): $obj->value";
      $body = imap_fetchbody( $mbox, $j, (string)( $k + 1 ) );
      switch ( $structure->parts[$k]->encoding ) {
        case 0:  echo ' (7BIT)'; break;		// ��������� ��� ����
        case 1:  echo ' (8BIT)'; break;		// ��������� ��� ����
        case 2:  echo ' (BINARY)'; break;	// ��������� ��� ����
        case 3:  echo ' (BASE64)';		// �����������
          $body = base64_decode( $body );
          break;
        case 4:  echo ' (QUOTED-PRINTABLE)';	// �����������
          $body = quoted_printable_decode( $body );
          break;
        case 5:  echo ' (OTHER) - �� ���������';
          continue 2;
          break;
        default: echo ' (UNKNOWN) - �� ���������';
          continue 2;
      }; // switch
      if ( $fp = fopen( "$dir/$obj->value", 'w' ) ) {
        fwrite( $fp, $body );
        fclose( $fp );
      } else
        echo " - ������ ��� �������� �����";
    }; // if
} else
  echo " - ��� ������� ������";
echo "\n";
echo `ls -l $dir`;
?>
