<?php // Stan 15 �������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

////////////////////////////////////////////////////////////////
// ��� ����� ������� � ��������� ������ ��������� ������ ���� //
////////////////////////////////////////////////////////////////

if ( !isset( $shell_access ) OR $shell_access != "$user+$pw_user" )
  return -5;

include_once 'func_mail.php';	// mail_content � ����� ������� � � ls
include_once 'func_other.php';	// list_dir � clear_dir

$mailing = 0;		// ����� ����� ����� ���� ��������� �� ����
$content = '';		// ��� ����� ���������� � ��� ����������
for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
  //$params = escapeshellarg( $params );
  echo "---\n$cmd: \"$params\"\n";
  switch( $cmd ) {
    case 'sh':			// ��������� ������� �����
      echo $mcontent = `$params`;
      $content .= $mcontent;
      break;
    case 'mailing':		// ��������� ����������
      $mailing = $params;
      break;
    case 'ls':			// ��������� �� ���� ������ ����������
      if ( is_dir( $params ) ) {
        ob_start();
        list_dir( $params );
        $str = ob_get_contents();
        ob_end_clean();
        if ( $str ) {
          $len = strlen( $str );
          if ( mail_content( $mail_to[$user], $str, 'ls.txt' ) )
            echo "������ ������ ($len) ������ �� $params ���������!\n";
        } else
          echo "$params - �� ���� ���������, ���������� ����� ��� ��� ���� �������\n";
      } else
        echo "$params - �� ����������\n";
      break;
    case 'remove_dir':		// ������� ����� �� ����� ���������� (���������!)
    case 'remove_contents':	// ������� ���������� ����� (���������!)
      if ( is_dir( $params ) ) {
        if ( $params != TEMP_PATH AND $params != USER_DIR ) {
          if ( clear_dir( $params, 1 ) ) {
            if ( $cmd == 'remove_dir' ) {
              echo $mcontent = "������� $params\n";
              rmdir( $params );
            } else
              $mcontent = '';
          } else
            echo $mcontent = "�� ������� ��������� ������� $params\n";
        } else
          echo $mcontent = "$params - ��������� �����, �� �������\n";
      } else
        echo $mcontent = "$params - ��� ����� �����\n";
      $content .= $mcontent;
      break;
    case 'clean_temp_dir':	// �������� ��������� �����
      echo $mcontent = "������� Temp\n";
      $content .= $mcontent;
      clear_dir( TEMP_PATH, 1 );
      break;
    default:
      echo " - ������ �� ������\n";
  }; // switch
}; // for

if ( $mailing AND $content )
  mail_content( $mail_to[$user], $content, 'shell.txt' );
?>
