<?php // Stan 22 ������� 2006�.
// ����������� �������, �� �������� � �����-���� ���������


// �������, ��������� � ������������


// ������� ������ ������ � ����������
// $level - ��� ����������� ����������, ��� ����������� �����������
function list_dir ( $path, $level = 1 ) {
  if ( $d = dir( $path ) ) {
    if ( preg_match( '/^(.*)\/$/', $path ) )	// ������� / �� �����, ���� ����
      $path = substr( $path, 0, -1 );
    while ( false !== ( $entry = $d->read() ) )
      if ( $entry != '.' AND $entry != '..' )
        if ( is_dir( "$path/$entry" ) ) {
          echo sprintf( "%{$level}s", ' ' ) . "[$path/$entry]\n";
          list_dir( "$path/$entry", $level + 1 );
        } else {
          $level1 = 50 - $level;
          echo sprintf( "%{$level}s", ' ' ) . sprintf( "%-{$level1}s", $entry );
          echo sprintf( "%10s", filesize( "$path/$entry" ) ) . "\t" . filetype( "$path/$entry" );
          echo "\t" . fileowner( "$path/$entry" ) . "\n";
        }; // if
    $d->close();
  }; // if
} // function


// ������� �������� ���������� �� ������
// �� ��������� ����� �����
// $echo - ����� �� ����� ��������
function clear_dir ( $path, $echo = 0 ) {	// echo - ���� ����� �����
  if ( is_dir( $path ) AND $d = dir( $path ) ) {
    $empty = 1;		// �� ������ ��������� ��������
    while ( false !== ( $entry = $d->read() ) )
      if ( $entry != '.' AND $entry != '..' )
        if ( is_dir( "$path/$entry" ) ) {
          if ( clear_dir( "$path/$entry", $echo ) )
            if ( $echo ) {
              echo "deleting directory $path/$entry... ";
              echo ( rmdir( "$path/$entry" ) ? 'ok' : 'no' ) . "\n";
            } else
              rmdir( "$path/$entry" );
          else
            $empty = 0;
        } else {
          if ( $echo ) {
            echo "deleting file      $path/$entry... ";
            echo ( unlink( "$path/$entry" ) ? 'ok' : 'no' ) . "\n";
          } else
            unlink( "$path/$entry" );
        }; // if
    $d->close();
    return $empty;
  } else {
    if ( $echo )
      echo "$path - �� ���������� ��� ��� ���� �������\n";
    return 0;
  }; // if
} // function


// �������, ��������� � url-�������


// ��������� URL �� �����
// $url - ������ ������
// $real - ���� 1, �� ��� ���������� ����� ����� ������� ������ ���
//         ���� 0, �� ��� ����� ������� �� ������ ����
function url_split ( $url, $real = 0 ) {
//                        1-         2------------  3---------         4----         5-
//                        proto      host           port               path/fname    params
  if ( preg_match( '/^(?:(.*):\/\/)?([A-z_.\-0-9]+)([0-9]{1,5})?(?:\/([^?]*))?(?:\?(.*))?/', $url, $matches ) ) {
    $proto    = isset( $matches[1] ) ? $matches[1] : '';
    $host     = $matches[2];
    $port     = isset( $matches[3] ) ? $matches[3] : '';
    $path = $filename = '';
    if ( isset( $matches[4] ) ) {
      $path = dirname( $matches[4] );
      $filename = basename( $matches[4] );
      if ( $path == '.' AND $real ) {
        $path = $matches[4];
        $filename = '';
      }; // if
    }; // if
    $params   = isset( $matches[5] ) ? $matches[5] : '';
  } else {
    $host = $path = $params = '';
    $filename = str_replace( '/', '-', $url );
  };
  if ( $filename AND preg_match( '/(.*)\.([^.]*)$/', $filename, $matches ) ) {
    $filename = $matches[1];			// ��� �����
    $ext      = $matches[2];			// � ���������� ���� ����
  } else
    $ext      = '';				// ���� ��� - �����������
//               2      (      4          )     1       3      5
  return array( $host, $path, $filename, $ext, $proto, $port, $params );
} // function


// ��������� �������� URL. ������� �� ���, ��� ����������� HEAD �
// ������������� �� Location, ���� ����� ���������������, �� �����������
// $level - ��� ����������� �������������, �� ��������� ���� ��������
// $str = 'http://www.usede.net/forum';	// ���������, ��� ���� �� ������ / �� �����
// $str = 'http://www.usede.net/usede.net//forum/';	// �� �������� ����
// $str = './install/install.php';	// � ���� ����� ����
function get_real_url ( $url, $echo = 0, $level = 0 ) {
  if ( $level > 5 )
    return $url;
  list( $host, $path, $filename, $ext, $proto, $port ) = url_split( $url );
  // ��������� ��� ���������: http � https
  if ( !$port )
    $port = ( $proto == 'https' ) ? 443 : 80;
  $fp = fsockopen( $host, $port, $errno, $errstr, 10 );
  if ( !$fp ) {
    echo "$errstr ($errno)\n";
    $answer = $url;
  } else {
    // GET, HEAD, OPTIONS, TRACE
    $req = $ext ? "/$path/$filename.$ext" : "/$path/$filename";
    fputs( $fp, "HEAD $req HTTP/1.0\r\nHost: $host\r\n\r\n" );
    $head = '';
    while( !feof( $fp ) )
      $head .= fgets( $fp, 128 );
    fclose ($fp);
    if ( $echo )
      echo "$level:\n$head";
    if ( preg_match( '/^Location: *http:\/\/([^\r\n]*)/m', $head, $matches ) ) {
      $level++;
      $answer = get_real_url( 'http://'.$matches[1], $echo, $level );
    } elseif ( preg_match( '/^Location: *([^\r\n]*)/m', $head, $matches ) ) {
      $level++;
      $answer = get_real_url( "http://$host/$path/{$matches[1]}", $echo, $level );
    } else
      $answer = $url;
  }; // if
  return $answer;
} // function

// ������������ ������ �� $highlight �������
function full_name ( $str_name, $highlight = '' ) {
  $personal = isset( $str_name->personal ) ? mp_decode( $str_name->personal ) : '';
  $mailbox  = isset( $str_name->mailbox  ) ? $str_name->mailbox  : '';
  $host     = isset( $str_name->host     ) ? $str_name->host     : '';

  if ( is_array( $highlight ) )
    while ( list( $key, $value ) = each( $highlight ) ) {
      list( $hl_mailbox, $hl_host, $color ) = $value;
      if ( $hl_mailbox AND $hl_host ) {
        if ( $mailbox == $hl_mailbox AND $host == $hl_host ) {
          $mailbox = "<span style=\"color: $color\"><b>$mailbox";
          $host    = $host . '</b></span>';
        }; // if
      } elseif ( !$hl_host AND $mailbox == $hl_mailbox )
          $mailbox = "<span style=\"color: $color\"><b>$mailbox</b></span>";
      elseif ( !$hl_mailbox AND $host == $hl_host )
          $host    = "<span style=\"color: $color\"><b>$host</b></span>";
    }; // while

  return $personal ? "$personal, $mailbox@$host" : "$mailbox@$host";
} // function
?>
