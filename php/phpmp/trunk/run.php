<?php
////////////////////////////////////////////////////////////////
//                                                            //
//       ����������������: Stan                               //
//       ���� �������� �������: 14 ���� 2006�.                //
//       �������� ������: http://usede.net                    //
//       mailto:phpscripts(shift-2)usede.net                  //
//                                                            //
////////////////////////////////////////////////////////////////
include '_local.php';	// ������������� ������ � �������
$run_welcome = "MP ������ 0.51 1 ������ 2007�.\n";	// ������� ������
// ������ ���� ������: 0.3 1 ������ 2007�.
$run_welcome .= date( 'd.m.Y H:i:s O' )."\n";		// � ����
echo $run_welcome;

// ������ ��������������� ��������� � ������������
if ( $mbox = imap_open( IMAILBOX, ILOGIN, IPASSWORD ) ) {
  $nomsg = imap_num_msg( $mbox );		// ���-�� ���������
  echo "����� ���������: $nomsg\n";
  for ( $j = 1; $j <= $nomsg; $j++ ) {		// ������������� ��� ������ � �����
    $user = '';					// ���������� ������������
    ob_start();					// ���������� ����� ��� ������� ������������
    echo "======================";		// ������� ������ � ���������
    $h = mp_header( $mbox, $j );
    print_ra( $h );
    if ( $h['Deleted'] )			// ���� ��������� �������
      echo "��������� �������!\n";
    elseif ( !isset( $h['subject'] ) )
      echo "���� �� ������!\n";
    else {
      $module = 'app_' . $h['subject'] . '.php';			// ��� ������
      if ( file_exists( $module ) ) {					// ���� ������ ����������
        list( $message, $charset ) = retrieve_message( $mbox, $j );	// ��������� ���������
        echo "���������: $charset\n";
        $message = mp_explode( $message );				// �����������
        imap_delete( $mbox, $j );		// ����� ������� ������ �� ������, ���� ������ ������� ������
        $user = $message[1][0];
        $pw = trim( $message[2][0] );
        if ( $user = $message[1][0] ) {		// ��������� ������������
          echo "������������ '$user' - ";
          $user_conf = "conf_$user.php";	// ��������� ������������
          if ( file_exists( $user_conf ) ) {	// ���� ��������� ����������
            echo "ok\n";
            if ( !isset( $runs[$user] ) )	// ���������� � ������ $runs
              $runs[$user] = '';
            include $user_conf;			// ��������� �
            if ( $pw == $pw_user ) {		// ��������� ������� �����
              $noexecmsg++;			// ����������� ������ �
              $err_code = include $module;	// ��������� ����������� ������
              if ( $err_code != 1 )
                echo "������ �������� ������: $err_code\n";
            } else
              echo "������ ����� �������!\n";
          } else {
            echo "�� ����������!\n";
            $user = '';
          }; // if
        } else
          echo "��������� $j - ������ ��� �� ������������� �������!\n";
      } else {
        echo "������ '{$h['subject']}' �� ������������!\n";
        if ( defined( 'DEL_ALL' ) )
          imap_delete( $mbox, $j );	// ������� ��� ���������, ���� ���������
      }; // if
    }; // if
    if ( $user )			// ���� ������������ ������� - ��������� ���
      $runs[$user] .= ob_get_contents();
    ob_end_flush();			// ������� � �������
  }; // for
  echo "~~~~~~~~~~~~~~~~~~~~~~\n";
  echo "��������� ���������: $noexecmsg";
  if ( defined( 'EXP_REQUIRE' ) )
    imap_close( $mbox, CL_EXPUNGE );
  else
    imap_close( $mbox );
} else
  echo '�� ������� ������������ � �������: ' . imap_last_error();

include '_local.php';
?>
