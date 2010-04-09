<?php // Stan 15 �������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

if ( !isset( $update_access ) OR $update_access != "$user+$pw_user" )
  return -5;

include_once 'func_mail.php';		// � ����� �������, save, send_tar
include_once 'Tar.php';			// ���������� ������ Tar.Gz
$unsent = 0;				// ��� ������� save_tar
$exclude = array();			// ��� ������� exclude � save

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
      echo "$cmd: \"$params\"";
      switch( $cmd ) {
//////////////////////////////////////////////////
        case 'update':		// �������� - ��� ����� ������������ USER_DIR
        case 'extract':		// ���� ���������� �����
//////////////////////////////////////////////////
          if ( is_dir( $dir = "$update_dir/$params" ) ) {
            echo " -> $dir";
            $structure = imap_fetchstructure( $mbox, $j );
            if ( $structure->type == 1 ) {	// ���� MULTI-PART ������
              $c = count( $structure->parts );
              for ( $k = 0; $k < $c; $k++ )
                if ( $structure->parts[$k]->ifdparameters ) {
                  $obj = $structure->parts[$k]->dparameters[0];
                  echo "\n$obj->attribute($k): $obj->value";
                  $body = imap_fetchbody( $mbox, $j, (string)( $k + 1 ) );
                  switch ( $structure->parts[$k]->encoding ) {
                    case 0:  echo ' (7BIT)'; break;	// ��������� ��� ����
                    case 1:  echo ' (8BIT)'; break;	// ��������� ��� ����
                    case 2:  echo ' (BINARY)'; break;	// ��������� ��� ����
                    case 3:  echo ' (BASE64)';		// �����������
                      $body = base64_decode( $body );
                      break;
                    case 4:  echo ' (QUOTED-PRINTABLE)';// �����������
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
                    if ( $cmd == 'extract' ) {
                      $Tar = new Archive_Tar( "$dir/$obj->value", 1 );	// ��������� �����
                      $Tar->extract( $dir );				// �������������
                      if( !unlink( "$dir/$obj->value" ) ) 		// ������� �����
                        echo ' <b>��������! ����� �� ��������!</b>';
                    }; // if
                  }; // if
                }; // if
              echo `ls -l $dir`;
            } else
              echo " - ��� ������� ������";
          } else
            echo ' - ���������� �� ����������!';
          break;
//////////////////////////////////////////////////
        case 'exclude':		// �������� - �����/����-����������
//////////////////////////////////////////////////
          $exclude = explode( ' ', $params );
          echo " - ��������� � save ������ ������";
          break;
//////////////////////////////////////////////////
        case 'save':		// �������� - �����/���� ��� �������� ������������ $update_dir
//////////////////////////////////////////////////
          if ( file_exists( $dir = "$update_dir/$params" ) ) {
            // ������ �����
            $arc_name = TEMP_PATH . '/save.tgz';
            echo " - ��������� � ���������� $dir ". ( is_dir( $dir ) ? '(dir)' : '(file)' );
            $Tar = new Archive_Tar( $arc_name, 1 );
            // ���� ������ ���������� - ������ ������ ������
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
            // ��������� ������ � �����
            if ( $Tar->createModify( $list, '', dirname( $dir ) ) ) {
              mail_file( $mail_to[$user], $arc_name, $dir );
              echo "\n������ ������: " . filesize( $Tar->_tarname );
            }; // if
          } else
            echo ' - ����������/����� �� ����������!';
          $exclude = array();	// ���������� ������
          break;
//////////////////////////////////////////////////
        case 'save_tar':	// ��������� � ������ ��������, �������� - �� �� ��� � � save
//////////////////////////////////////////////////
          $unsent = 1;		// �� ������ ���� �� ������ ������� send_tar
          if ( file_exists( $dir = "$update_dir/$params" ) ) {
            $arc_name = TEMP_PATH.'/save.tar';
            echo " - ��������� $dir ". ( is_dir( $dir ) ? '(dir)' : '(file)' );
            if ( !isset( $STar ) )
              $STar = new Archive_Tar( $arc_name );
            $STar->addModify( $dir, '', dirname( $dir ) );
          } else {
            $arc_name = '';
            echo ' - ����������/����� �� ����������!';
          }; // if
          break;
//////////////////////////////////////////////////
        case 'send_tar':	// ���������� ����� ��������� save_tar, ��� ����������
//////////////////////////////////////////////////
          if ( $arc_name ) {
            echo " - ���������� $arc_name\n";
            mail_file( $mail_to[$user], $arc_name, $dir );
            unlink( $arc_name );
          } else
            echo ' - ����� �� ������!';
          $unsent = 0;
          break;
//////////////////////////////////////////////////
        default:
//////////////////////////////////////////////////
          echo ' - ����������';
      }; // switch
      echo "\n";
}; // for

if ( $unsent AND $arc_name ) {
  echo "���������� $arc_name\n";
  mail_file( $mail_to[$user], $arc_name, $dir );
  unlink( $arc_name );
}; // if
?>
