<?php // Stan 10 октября 2006г.
// Этот скрипт вызывается в начале исполняемого скрипта
// для инициализции и в конце - для вывода в броузер
// После второго вызова прекращает выполнение скрипта

// Защита на то, чтобы этот скрипт не вызывался отдельно
//if ( basename( $PHP_SELF ) == basename( __FILE__ ) )
//  exit( 'Hacking attempt!' );

if ( !defined( 'LOCAL_DIR' ) ) {
  define( 'LOCAL_DIR', dirname( __FILE__ ) );
  //header( 'Content-Type: text/plain' );
  ob_start();	// Кешируем вывод в браузер

  // Загружем common.php
  define( 'ALWAYSFULLSTRING', 1 );
  if ( !@include( 'common.php' ) ) {
    error_reporting( E_ALL );
    echo "Модуль common.php не найден!\n";
  }; // if

  // Если в PHP не включен модуль IMAP, то скрипт не будет работть,
  // но для эмуляции работы можно включать дополнительный скрипт
  if ( !function_exists( 'imap_open' ) )
    include '_imap_emu.php';
  $noexecmsg = 0;	// Считаем сколько сообщений выполнится
  // Загружаем конфигурационный файл
  include 'conf.php';
  include 'func.php';
  //print_r( get_defined_constants() );
} else {
  if ( $imap_alerts = imap_alerts() ) {
    echo "\nСообщения IMAP:\n";
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
    // Весь выведенный на экран текст отправляем по мылу
    mail_message( $content, LOG_TO, 'Отчёт ' . date( 'd.m.Y H:i:s O' ) );
    // Посылаем смс о выполнении скрипта на мобилу
    sms_message( "Кол-во сообщений: $nomsg, обработано: $noexecmsg" );
    // Всем пользоваетелям, кроме админа, рассылаем отчёты
    while( list( $key, $val ) = each( $runs ) ) {
      $pos = strpos( $mail_to[$key], LOG_TO );
      if ( $pos === False )
        mail_message( "<pre>\n$run_welcome$val\n</pre>\n", $mail_to[$key], "$key: " . date( 'd.m.Y H:i:s O' ) );
    }; // while
  }; // if

  //if ( $PHP_SELF )	// Если запуск из веб-сервера, то вывести в броузер
  echo $content;
  exit();
}; // if
?>
