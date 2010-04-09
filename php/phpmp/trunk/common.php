<?php // Stan http://usede.net 9 ������� 2004�.

   define ( 'PHP_COMMON', 1 );
   define ( 'ERROR',   E_USER_ERROR   );
   define ( 'WARNING', E_USER_WARNING );
   define ( 'NOTICE',  E_USER_NOTICE  );

   error_reporting ( E_ALL );
// ������, ������� ����� ���������� �� �����

// define ( 'ERRORS_EMAIL_TO',  'Stan <ngsc-smirnykh@sk2.ru>' );
   define ( 'ERRORS_SEND_FLAG', E_USER_ERROR );
// define ( 'ERRORS_SEND_FLAG', E_WARNING | E_NOTICE | ERROR | WARNING | NOTICE );
// ���� ��������� ����� �� ��������� ������, �� ����� ��������� �� �����
// ���������� ERRORS_EMAIL_TO ���������������� ��������� ����� ������� common.php
// ��������������� ���� ��� �����, ���� �� ������ �������� ������

// print_r( get_defined_constants() );
////////////////////////////////////////////////////////////////////////////////
//////// ���������� �������, ������� ����������� �� ���������� �������� ////////
////////////////////////////////////////////////////////////////////////////////
// ��� ������� ��������� ��� ���� ����� ��� �������� ��������� ���
// ������������ �� ���� ���������� ��������� �� ������� �� ����
// ��� ���������� ���������� ���������� $common_errors �����
if ( defined( 'ERRORS_EMAIL_TO' ) AND defined( 'ERRORS_EMAIL_TO' ) ) {
  function UserExitHandler ( ) {
  global $common_errors;
    if ( $common_errors ) {	// ���� ���� ��� ��������
      // ����������, ����� �� ���������� �����, ������ ��������������� ������
      $need_to_send = 0;
      $message  = "Common ������ 0.23.3 5 ������ 2007�.\n";
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
//////// ������������� ���� ���������� ������ ////////
//////////////////////////////////////////////////////
//        E_ALL              ��� �������������� � ������.
//    1 ! E_ERROR            ����������� ������ ������� ����������.
//    2   E_WARNING          �������������� ������� ����������.
//    4 ! E_PARSE            ������ ����������.
//    8   E_NOTICE           ��������� ������� ���������� (��� ����� ��������������, �������,
//                           ������ �����, ��������������� � ���������� ������� � ��������,
//                           ��������, �������������, �������������������� ����������).
//   16 ! E_CORE_ERROR       ����������� ������ � ������ ������ PHP.           ( PHP 4 )
//   32 ! E_CORE_WARNING     ������������� �������������� �� ����� ������ PHP. ( PHP 4 )
//   64 ! E_COMPILE_ERROR    ����������� ������ ������� ����������.            ( PHP 4 )
//  128 ! E_COMPILE_WARNING  �������������� ������� ����������.                ( PHP 4 )
//  256   E_USER_ERROR       ��������������� ������������� ������.             ( PHP 4 )
//  512   E_USER_WARNING     ��������������� ������������� ��������������.     ( PHP 4 )
// 1024   E_USER_NOTICE      ��������������� ������������� �����������.        ( PHP 4 )
//      ^ - ������, ������� �� �������� ���������������� ������� ����������� ������
function UserErrorHandler ( $errno, $errstr, $errfile, $errline ) {
  global $common_errors;
  // C������� ���� ������� �� ������������
  $errfile = basename( $errfile );
  // ����� ������ ������� � ������ ������
  // ���� ������ ����� ��������� ��� ������������� E_USER_ERROR
  if ( !error_reporting() ) {	// ������� ������ ( @������� ) - �� �������
    $common_errors[] = array( 'ErrNo' => $errno, 'ErrStr' => '@[ ' . $errstr . ' ]',
                              'ErrFile' => $errfile, 'ErrLine' => $errline );
    // ���� error_reporting ���������� � ����, �� E_USER_ERROR (� ������ �������������)
    // �� ����� �������� ���������� - ������ �������������� �������� ����
    if ( $errno & E_USER_ERROR ) exit();
    return 0;	// �� ������� ������ � @��������
  } else
    $common_errors[] = array( 'ErrNo' => $errno, 'ErrStr' => $errstr,
                              'ErrFile' => $errfile, 'ErrLine' => $errline );
  switch ( $errno ) {
  // ��� ������ ����������� �������� ������ ������� ������ ������
  // ������ � ��� �� �������� ��� ��������� WARNING �� NOTICE
    case E_WARNING:
      $errtype = 'E_WARNING'; $marker = '|W|'; $color = 'red';     break;
    case E_NOTICE:
      $errtype = 'E_NOTICE';  $marker = '|N|'; $color = 'yellow';  break;
    case E_USER_WARNING:
      $errtype = 'WARNING';   $marker = '|w|'; $color = 'red';     break;
    case E_USER_NOTICE:
      $errtype = 'NOTICE';    $marker = '|n|'; $color = 'yellow';  break;
    case E_USER_ERROR:		// ��� ��������� ������ - ���������� ������� �����������
      @ob_end_clean();	// ������ ��������� �� ������� �� �����, ����� �������� ������
      print_rt( $common_errors );
      exit(); break;
    default:
      $errtype = 'Unknown code'; $marker = '||'; $color = 'yellow';
      $common_errors[] = array( 'ErrNo' => '', 'ErrStr' => 'Unknown previous error!',
                                'ErrFile' => '', 'ErrLine' => '' );
      break;
  }; // switch

  // ������ ������ ��������� �� ���������
  $style = "style=\"color:$color; background-color:black\"";
  if ( preg_match( '/(\+?)(\{([^|}]*)[|]?(.*)\})?(.*[^^]?)(\^)?/i', $errstr, $matches ) ) {
  // $1 +, $2 {...}, $3 color $4 bgcolor $5 ��������� $6 ^ (���� ��� �� �� ����������)
  // ���� �����������: ������ ������ ������������ "^", � �� ������ � �����, ��� ��������
    //print_ra( $matches );
    if ( $matches[1] == '+' OR defined( 'ALWAYSFULLSTRING' ) )
      $marker = $matches[5];
    if ( $matches[3] && $matches[4] )	// ������ ��� �����
      $style = "style=\"color:$matches[3]; background-color:$matches[4]\"";
    elseif ( $matches[3] )		// ����� Color
      $style = "style=\"color:$matches[3]\"";
    elseif ( $matches[4] )		// ����� BgColor
      $style = "style=\"background-color:$matches[4]\"";
    elseif ( $matches[2] )		// ���� �������� ������
      $style = '';			// �� ��������� �� ����� ����������
    $errstr = $matches[5];
    if ( isset( $matches[6] ) OR defined( 'ALWAYSBRSTRING' ) )	// ���� ���������� ����������, ������ ^ ������
      $marker .= "<br />\n";
  }; // if

  // ������� �� ����� ��������� ������ � ��������������
  if ( error_reporting() & $errno )
    echo "<span $style title=\"" .
         htmlspecialchars( "[ $errtype ]     [ $errstr ]     [ $errfile ]     [ line: $errline ]" ) .
         "\">$marker</span>\n";
  return 1;
}
// ������ ������ ��� �������� ���� �� �������
$common_errors = array();
// ��������� ����������� ���������� ������
$old_error_handler = set_error_handler( 'UserErrorHandler' );

/////////////////////////////////////////
//////// ������� ������ �������� ////////
/////////////////////////////////////////
// ��� ������� ������� print_r
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

// ��� ������� ������� print_r
// $htmlchars = 0 - ������ �� ������ (�� ���������)
// $htmlchars = 1 - ����������� ���� � ������� �����
// $htmlchars = 2 - �������� ����
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
      // ���� ����� �� ������, �� ������� ���������� �����
      echo "<pre>\n$array</pre>\n";
    }; // if
    return 1;
} // function

// ��� ������� ������� ����������������� ������� � ���� �������
// ��������� ������ ������� ���� array( 0 => array(), 1 => array() )
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

// ��� ������� ������� hex-���� �������� ������
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
      echo '�';
    else
      echo $str[$i];
  }; // for
  echo '</pre></table>';
} // function


///////////////////////////////////////////////////
/////////////// ������� �������� �� ///////////////
///////////////////////////////////////////////////
function open_usede_db ( ) {
  $conn = @mysql_connect( USE_DB_HOST, USE_DB_USER, USE_DB_PASSWD )
    or user_error( mysql_errno().': '.mysql_error(), E_USER_ERROR );
  mysql_select_db( USE_DB_NAME, $conn )
    or user_error( mysql_errno().': '.mysql_error(), E_USER_ERROR );
  return $conn;
} // function

ob_start( 'ob_gzhandler' );	// ��������� ������ ��������
/*****************************************************************************
  ������ 0.01 9 ������� 2004�.
  �������� common.php - ������ ��� ��������� ����������� ��������� ���������
  ������ � �������������� � ����������� ����� �� �������� ����
  �������� ������� � ������ ������� ���������

  ������ 0.11 12 ���� 2006�.
  �� �� ��� ����� �������� ��������� ������, ����� ���������, �������
  � ������������ ������� ������ �������� ��� ����

  ������ 0.21 19 �������� 2006�.
  ����������� �� ���� ������� �� �����������. ������� ���� ������, � �������
  ��������� ������������ �����������, ������� ������ ������� (��� ���������
  ����������� :))

  ������ 0.23 - 25 �������� 2006�.
  ����� ������ '+{color|bgcolor}�����^' ��� user_error()
  + - ������ ����������� ��������� �����
  color � bgcolor - ����� ������ ���� ������
  ^ - ���� ����� � ����� ������ (��� ��������� ���������, ������ �� ���� ������
   � ����������), �� ����� ��������� <br />

  ������ 0.23.1 - 24 ������� 2006�.
  ������� print_rt (������ ����� �������� ������� � ����� ����� ��������)

  ������ 0.23.3 - 5 ������ 2007�.
  ������� ���� $htmlchars � print_ra � print_rt

  ������ 0.23.5 - 23,30 ������ 2007�.
  ������� print_sh
  ������ print_rt �������� ������ ������
*****************************************************************************/
?>
