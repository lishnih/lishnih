<?php // Stan 28 марта 2007г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

$scriptname   = '';
$auto_forum   = 0;
$auto_subject = '';

// Форум тоже использует $message
$message1 = $message;
for ( $i = 1; $i < mp_count( $message1 ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message1, $i );
  echo "---\n$cmd: \"$params\"\n";
  switch( $cmd ) {
    case 'open':		// Задать форум
      $auto_user  = $myphpbb[$user][$params]['user'];
      $scriptname = $myphpbb[$user][$params]['scriptname'];
      echo "Задан $scriptname\n";
      break;
    case 'forum':		// Задать ветку (при создании нового топика)
      $auto_forum = $params;
      break;
    case 'subject':		// Задать тему топика (при создании нового топика)
      $auto_subject = $params;
      break;
    case 'newtopic':	// Новый топик
      if ( $scriptname AND $auto_forum AND $auto_subject ) {
        $auto_message = str_replace( '^', "\n", $params );
        include $scriptname;
        $auto_subject = '';
      } else
        echo "Не задан форум, номер форума или тема!\n";
      break;
    case 'packed':		// Добавить сообщение/топик
      if ( $scriptname ) {
        run_func( 'auto_packed', $params );
        $auto_packed = $params;
        include $scriptname;
        $auto_packed = '';
      } else
        echo "Не задан форум\n";
      break;
    default:		// Добавить топик
      if ( $scriptname AND is_numeric( $cmd ) ) {
        $auto_topic = $cmd;
        $auto_message = str_replace( '^', "\n", $params );
        include $scriptname;
      } else
        echo "Не задан форум или топик\n";
  }; // switch
}; // for
?>
