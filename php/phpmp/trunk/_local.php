<?php // Stan 10 ������� 2006�.
// ���� ������ ���������� � ������ ������������ �������
// ��� ������������ � � ����� - ��� ������ � �������
// ����� ������� ������ ���������� ���������� �������

// ������ �� ��, ����� ���� ������ �� ��������� ��������
//if ( basename( $PHP_SELF ) == basename( __FILE__ ) )
//  exit( 'Hacking attempt!' );

if ( !defined( 'LOCAL_DIR' ) ) {
  define( 'LOCAL_DIR', dirname( __FILE__ ) );
  //header( 'Content-Type: text/plain' );
  ob_start();	// �������� ����� � �������

  // �������� common.php
  define( 'ALWAYSFULLSTRING', 1 );
  if ( !@include( 'common.php' ) ) {
    error_reporting( E_ALL );
    echo "������ common.php �� ������!\n";
  }; // if

  // ���� � PHP �� ������� ������ IMAP, �� ������ �� ����� �������,
  // �� ��� �������� ������ ����� �������� �������������� ������
  if ( !function_exists( 'imap_open' ) )
    include '_imap_emu.php';
  $noexecmsg = 0;	// ������� ������� ��������� ����������
  // ��������� ���������������� ����
  include 'conf.php';
  include 'func.php';
  //print_r( get_defined_constants() );
} else {
  if ( $imap_alerts = imap_alerts() ) {
    echo "\n��������� IMAP:\n";
    print_r( $imap_alerts );
  }; // if

  $content = ob_get_contents();
  ob_end_clean();
  $content = "<pre>\n$content\n</pre>\n";

  if ( $noexecmsg ) {
    if ( defined( 'LOG_NAME' ) ) {
      $fp = fopen( LOG_NAME, 'a' );
      fwrite( $fp, "$content\n" );
      fclose( $fp );
    }; // if
    // ���� ���������� �� ����� ����� ���������� �� ����
    mail_message( $content, LOG_TO, '����� ' . date( 'd.m.Y H:i:s O' ) );
    // �������� ��� � ���������� ������� �� ������
    sms_message( "���-�� ���������: $nomsg, ����������: $noexecmsg" );
    // ���� ��������������, ����� ������, ��������� ������
    while( list( $key, $val ) = each( $runs ) ) {
      $pos = strpos( $mail_to[$key], LOG_TO );
      if ( $pos === False )
        mail_message( "<pre>\n$run_welcome$val\n</pre>\n", $mail_to[$key], "$key: " . date( 'd.m.Y H:i:s O' ) );
    }; // while
  }; // if

  //if ( $PHP_SELF )	// ���� ������ �� ���-�������, �� ������� � �������
  echo $content;
  exit();
}; // if
?>
