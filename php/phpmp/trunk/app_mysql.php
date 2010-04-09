<?php // Stan 5 ������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

include_once 'func_mail.php';		// �������� ���������
include_once 'func_other.php';		// url_split

$dbi = 0;	// ����� ���������� � ����� ������
$dbn = '';	// ��������� ���� ������ (select)

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
  echo "---\n$cmd: \"$params\"";
  switch( $cmd ) {
//////////////////////////////////////////////////
    case 'open':			// �������� - ����� ������ � �������
//////////////////////////////////////////////////
      $myhost = $mydbserver[$user][$params]['dbhost'];
      $myuser = $mydbserver[$user][$params]['dbuser'];
      $mypw   = $mydbserver[$user][$params]['password'];
      echo " - ������������� ���������� � $myhost/$myuser";
      if ( !$dbi = mysql_connect( $myhost, $myuser, $mypw ) ) {
        echo "���������� ������ ����������!\n";
        return -1;
      }; // if
      break;
//////////////////////////////////////////////////
    case 'dblist':			// ��� ����������
//////////////////////////////////////////////////
      echo " - ������� ������ ��������� ��� ������ ��� $dbi";
      $k = 1;
      $db_list = mysql_list_dbs( $dbi );
      while ( $row = mysql_fetch_object( $db_list ) ) {
        echo "\n$k: " . $row->Database;
        $k++;
      }; // while
      break;
//////////////////////////////////////////////////
    case 'export':			// �������� - ��� ���� ������
//////////////////////////////////////////////////
      echo " - ������������ ���� ������ ($dbi)\n";
      if ( !mysql_select_db( $params ) ) {
        echo "���������� � ����� ������ ����������!\n";
        return -2;
      }; // if
      $content = '';
      $result = mysql_list_tables( $params );
      while( $row = mysql_fetch_row( $result ) ) {
        $content .= "#------------------------------------------------------\n";
        $content .= "# ������� $row[0]\n";
        $content .= "#------------------------------------------------------\n";
        $mytable = $row[0];
        $result2 = mysql_query( "SHOW CREATE TABLE $mytable" );
        $row2 = mysql_fetch_row( $result2 );
        $content .= "# ���������:\n";
        $content .= $row2[1] . ";\n# ������:\n";
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
          $content .= "# �����!\n\n";
      }; // while
      if ( $content ) {
        $len = strlen( $content );
        if ( mail_content( $mail_to[$user], $content, "$params.sql" ) )
          echo "���� $params.sql($len) ���������.";
        mysql_free_result( $result2 );
      } else
        echo "���� ������ $params �����.";
      mysql_free_result( $result );
      break;
//////////////////////////////////////////////////
    case 'import':			// �������� - ��� ���� ������
      include_once 'sql_parse.php';	// ���������� ���������� �� PHPBB
//////////////////////////////////////////////////
      echo " - ����������� � ���� ������ ($dbi)\n";
      if ( !mysql_select_db( $params ) ) {
        echo "���������� � ����� ������ ����������!\n";
        return -2;
      }; // if

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
              case 4:  echo ' (QUOTED-PRINTABLE)';	// �����������
                $body = quoted_printable_decode( $body );
                break;
              case 5:  echo ' (OTHER) - ����������';
                continue 2;
                break;
              default: echo ' (UNKNOWN) - ����������';
                continue 2;
            }; // switch

            // ������������� gz-�����
            if ( preg_match( '/(.*)\.([^.]*)$/', $obj->value, $matches ) )
              $ext = $matches[2];
            else
              $ext = '';
            switch ( $ext ) {
              case 'gz':
                // ���������� �����
                $filename = TEMP_PATH . '/sql.gz';
                if ( $zp = fopen( $filename, 'w' ) ) {
                  fwrite( $zp, $body );
                  fclose( $zp );
                  // ������ ����������
                  $body = '';
                  $zp = gzopen( $filename, 'r' );
                  while ( $str = gzread( $zp, 1024 ) )
                    $body .= $str;
                  gzclose( $zp );
                }; // if
                break;
              default:
            }; // switch

            // ����� �� phpbb 2.0.21
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
        echo '��� ������� ������';
      break;
//////////////////////////////////////////////////
    case 'select':			// �������� - ��� ���� ������
//////////////////////////////////////////////////
      echo " - ����� ���� ������ ($dbi)";
      if ( !mysql_select_db( $params ) ) {
        echo "\n���������� � ����� ������ ����������!";
        return -2;
      }; // if
      $dbn = $params;
      break;
//////////////////////////////////////////////////
    case 'delete_table':	// �������� - ��� �������
// ����� �������������� ����� ������� select
//////////////////////////////////////////////////
      echo ' - �������� �������';
      if ( $dbn ) {
        if ( !mysql_query( "DROP TABLE $params" ) )
          echo "\n������!";
      } else
        echo "\n���� ������ �� �������!";
      break;
//////////////////////////////////////////////////
    case 'clear_db':		// ��� ����������
// ����� �������������� ����� ������� select
//////////////////////////////////////////////////
      echo ' - �������� ���� ������';
      if ( $dbn ) {
        $result = mysql_list_tables( $dbn );
        $k = 0;
        while( $row = mysql_fetch_row( $result ) ) {
          echo "\n$row[0] - ";
          if ( mysql_query( "DROP TABLE $row[0]" ) ) {
            echo 'ok!';
            $k++;
         } else
            echo '������!';
        }; // while
        echo "\n������� ������: $k";
      } else
        echo "\n���� ������ �� �������!";
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
