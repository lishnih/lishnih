<?php // Stan 12 �������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

include_once 'func_mail.php';	// mail_content � mail_file
include_once 'func_other.php';	// clear_dir

// ��������� ���������� �� ���������
$compress = 0;		// �������� ��������� ����� ����� ���������?
$level    = 0;		// ������� ������� �����������? (���� �� �����������)
$subject  = '';		// ���� ������ ��� ����������� html-��������
$report   = '';		// ��������� � ����������� html � load
// ������, ��� ������� ����� ����� ����������� �� ����� � ������������ ��������. ����� �����
// ����� ����� �������� ������� (���� ���������� ������ ����� �� ����� � ����� ������� ������)

// ������ ��� ������ ��������� �����
$dir = TEMP_PATH . '/dload';		// ��������� ����� ���� ����� ���������� ��������� �����
$dir_size = 0;				// ����� ������� ����� ������ ������
echo "��������� ����� $dir - ";
if ( !file_exists( $dir ) ) {		// ��� �������� �� ��, ��� ����� ���� ������ �����!!
  if ( mkdir( $dir ) )
    echo "�������\n";
  else {
    echo "������: �� ���� ������� ��������� �����!\n";
    $dir = '';
  }; // if
} else {				// !!!!!!!!!!!!!! �������� !!!!!!!!!!!!!!
  clear_dir( $dir );			// ���� ����� ����, �� ��� ����� �������!
  echo "�������\n";
}; // if

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
      echo "---\n$cmd: \"$params\"";
      switch( $cmd ) {
//////////////////////////////////////////////////
        case 'google':		// ����� � �����
//////////////////////////////////////////////////
          $params = str_replace( ' ', '+', $params );
          $params = str_replace( '&', '+', $params );
          $params = "http://www.google.com/search?q=$params&sourceid=opera&num=0&ie=cp1251&oe=cp1251";
//////////////////////////////////////////////////
        case 'echo':		// ������ ������� url
//////////////////////////////////////////////////
          if ( $content = file_get_contents( $params ) )
            echo " - ������: " . strlen( $content ) . "\n<table border=1>\n<tr><td>\n$content\n</table>";
          break;
//////////////////////////////////////////////////
        case 'file':		// �������� ����� �� URL � �������� �� ����
        case 'load':		// �������� ����� �� URL � ��������� �� ��������� �����
        case 'html':		// �������� html � ���������� � ��������� �� ��������� �����
//////////////////////////////////////////////////
          echo ' - ����: ';
          $content = file_get_contents( $params );
          if ( $content ) {
            list( , , $filename, $ext ) = url_split( $params );
            if ( !$filename )		$filename = 'index';	// ���, ���� �� ������
            if ( !$ext )		$ext = 'html';		// ����������, ���� �� ������
            else			$ext = strtolower( $ext );
            if ( $ext == 'php' )	$ext = 'php.html';	// php -> php.html
            if ( $ext == 'php3' )	$ext = 'php3.html';	// ��� �����������
            if ( $ext == 'pl' )		$ext = 'pl.html';	// pl -> pl.html
            if ( $ext == 'asp' )	$ext = 'asp.html';	// asp -> asp.html
            if ( $ext == 'aspx' )	$ext = 'aspx.html';	// aspx -> aspx.html
            if ( $ext == 'cgi' )	$ext = 'cgi.html';	// cgi -> cgi.html
            if ( $ext == 'shtml' )	$ext = 'html';		// shtml -> html
            $len = strlen( $content );
            echo "$filename.$ext($len)";
            if ( $len > MAX_FILE_SIZE ) {	// ���� ��������� ���� ����� - �������� � Temp
              $fp = fopen( TEMP_PATH."/$filename$len.$ext", 'w' );
              fwrite( $fp, $content );
              fclose( $fp );
              echo ' ��������� � temp!';
              continue;
            }; // if
            if ( $cmd == 'file' ) {		// ���� �������� ����, �� ����� ����������
              mail_content( $mail_to[$user], $content, "$filename.$ext", $subject ? $subject : $params );
              echo " ���������!\n";
              get_real_url( $params, 1 );
              break;
            }; // if
            // ������ html-���� � ������ ����� ����������, ����������
            if ( !$subject AND preg_match( '/.*html?/', $ext ) )
              $subject = urldecode( $params );
            if ( file_exists( "$dir/$filename.$ext" ) )	// ���� � ����� ���� ���� � ����� ������
              $filename = $filename.$len;		// �� �������� �� �������
            if ( $fp = fopen( "$dir/$filename.$ext", 'w' ) ) {
              fwrite( $fp, $content );
              fclose( $fp );
              echo ' �������';
              $dir_size += $len;			// ������� ������
              $report .= htmlspecialchars( urldecode( $params ) ) . " -> $filename.$ext\n";	// ������� � �����
            } else
              echo ' �� �������';
            if ( $cmd == 'load' )		// ���� �������� load, �� ����������� �����
              break;
//////////////////////////////////////////////////
            $rurl = get_real_url( $params );
            echo "\n�������� ��� $rurl ";
            list( $host, $path, , , $proto ) = url_split( $rurl );
            $pictures = array();	// ����� ���������� ��������, ����� �� ���� ����������

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
                // ����� ����������� \" � \' (�������� � ��������), ���������� ��������� ���
                if ( substr( $val, 0, 2 ) == '\\"' OR substr( $val, 0, 2 ) == '\\\'' )
                  $val = substr( stripslashes( $val ), 1, -1 );
                // ������� ������� \n � \r
                $val = str_replace( "\n", '', $val );
                $val = str_replace( "\r", '', $val );
                if ( $val AND !isset( $pictures[$val] ) AND !strstr( $val, '"' ) AND !strstr( $val, '\'' ) ) {
                  $pictures[$val] = basename( $val );	// ����������� ���
                  if ( preg_match( '/^ *(https?:\/\/.*)$/m', $val ) ) {
                    $link = $val;
                    list( , , $pname, $pext ) = url_split( $val );
                    $pictures[$val] = $pext ? "$pname.$pext" : $pname;
                  } elseif ( preg_match( '/^\//', $val, $link ) )
                    $link = "$proto://$host$val";
                  else
                    $link = "$proto://$host/$path/$val";
                  if ( $link AND $picture = file_get_contents( $link ) ) {	// ��������� ��������
                    $len = strlen( $picture );
                    if ( file_exists( "$dir/{$pictures[$val]}" ) )	// ���� � ����� ���� ���� � ����� ������
                      $pictures[$val] = $len.$pictures[$val];		// �� �������� �� �������
                    if ( $fp = fopen( "$dir/{$pictures[$val]}", 'w' ) ) {
                      $dir_size += $len;				// ������� ������
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
                fwrite( $fp, $content );	// ���������� � �����������
                fclose( $fp );
              }; // if
            }; // if
//////////////////////////////////////////////////
          } else
            echo '����!';
          break;
//////////////////////////////////////////////////
        case 'head':		// �������� header �� $params
//////////////////////////////////////////////////
          echo "\n";
          $str = get_real_url( $params, 1 );
          if ( $str != $params )
            echo "�����: \"$str\"";
          break;
//////////////////////////////////////////////////
        default:		// ���� �� ������� - ��������� ����������
//////////////////////////////////////////////////
        switch( $cmd ) {
          case 'compress':
          case 'level':
          case 'subject':
            echo ' - ��������� ����������';
            ${$cmd} = $params;
            break;
          default:
            echo ' - ������ �� ������';
        }; // switch
      }; // switch
      echo "\n";
}; // for

// ���������� ��, ��� �������� �� ��������� �����
if ( $dir_size ) {
  echo "��������� ������ ��������: $dir_size\n";
  if ( !$subject )		// ���� html-�������� �� ����
    $subject = $params;
  if ( $compress ) {
    include_once 'Tar.php';		// ���������� ������ Tar.Gz
    $arc_name = TEMP_PATH.'/dload.tgz';
    $Tar = new Archive_Tar( $arc_name, 1 );
    if ( $Tar->createModify( $dir, '', $dir ) )
      mail_file( $mail_to[$user], $arc_name, $subject );
  } else
      mail_file( $mail_to[$user], $dir, $subject, $report );
}; // if
?>
