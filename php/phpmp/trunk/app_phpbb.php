<?php // Stan 28 ����� 2007�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

$scriptname   = '';
$auto_forum   = 0;
$auto_subject = '';

// ����� ���� ���������� $message
$message1 = $message;
for ( $i = 1; $i < mp_count( $message1 ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message1, $i );
  echo "---\n$cmd: \"$params\"\n";
  switch( $cmd ) {
    case 'open':		// ������ �����
      $auto_user  = $myphpbb[$user][$params]['user'];
      $scriptname = $myphpbb[$user][$params]['scriptname'];
      echo "����� $scriptname\n";
      break;
    case 'forum':		// ������ ����� (��� �������� ������ ������)
      $auto_forum = $params;
      break;
    case 'subject':		// ������ ���� ������ (��� �������� ������ ������)
      $auto_subject = $params;
      break;
    case 'newtopic':	// ����� �����
      if ( $scriptname AND $auto_forum AND $auto_subject ) {
        $auto_message = str_replace( '^', "\n", $params );
        include $scriptname;
        $auto_subject = '';
      } else
        echo "�� ����� �����, ����� ������ ��� ����!\n";
      break;
    case 'packed':		// �������� ���������/�����
      if ( $scriptname ) {
        run_func( 'auto_packed', $params );
        $auto_packed = $params;
        include $scriptname;
        $auto_packed = '';
      } else
        echo "�� ����� �����\n";
      break;
    default:		// �������� �����
      if ( $scriptname AND is_numeric( $cmd ) ) {
        $auto_topic = $cmd;
        $auto_message = str_replace( '^', "\n", $params );
        include $scriptname;
      } else
        echo "�� ����� ����� ��� �����\n";
  }; // switch
}; // for
?>
