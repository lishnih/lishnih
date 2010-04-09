<?php
////////////////////////////////////////////////////////////////
//                                                            //
//       Программирование: Stan                               //
//       Дата создания проекта: 14 июля 2006г.                //
//       Страница автора: http://usede.net                    //
//       mailto:phpscripts(shift-2)usede.net                  //
//                                                            //
////////////////////////////////////////////////////////////////
include '_local.php';	// Инициализация вывода в браузер
$run_welcome = "MP Версия 0.51 1 апреля 2007г.\n";	// Выводим версию
// Версия ядра пакета: 0.3 1 апреля 2007г.
$run_welcome .= date( 'd.m.Y H:i:s O' )."\n";		// и дату
echo $run_welcome;

// Читаем последовательно сообщения и обрабатываем
if ( $mbox = imap_open( IMAILBOX, ILOGIN, IPASSWORD ) ) {
  $nomsg = imap_num_msg( $mbox );		// Кол-во сообщений
  echo "Всего сообщений: $nomsg\n";
  for ( $j = 1; $j <= $nomsg; $j++ ) {		// Просматриваем все письма в ящике
    $user = '';					// Сбрасываем пользователя
    ob_start();					// Буферизуем вывод для каждого пользователя
    echo "======================";		// Выводим данные о сообщении
    $h = mp_header( $mbox, $j );
    print_ra( $h );
    if ( $h['Deleted'] )			// Если сообщение удалено
      echo "Сообщение удалено!\n";
    elseif ( !isset( $h['subject'] ) )
      echo "Тема не задана!\n";
    else {
      $module = 'app_' . $h['subject'] . '.php';			// Имя модуля
      if ( file_exists( $module ) ) {					// Если модуль существует
        list( $message, $charset ) = retrieve_message( $mbox, $j );	// Извлекаем сообщение
        echo "Кодировка: $charset\n";
        $message = mp_explode( $message );				// Преобразуем
        imap_delete( $mbox, $j );		// сразу удаляем письмо на случай, если модуль вызовет ошибки
        $user = $message[1][0];
        $pw = trim( $message[2][0] );
        if ( $user = $message[1][0] ) {		// Считываем пользователя
          echo "Пользователь '$user' - ";
          $user_conf = "conf_$user.php";	// Настройки пользователя
          if ( file_exists( $user_conf ) ) {	// Если настройки существуют
            echo "ok\n";
            if ( !isset( $runs[$user] ) )	// Буферизуем в массив $runs
              $runs[$user] = '';
            include $user_conf;			// загружаем и
            if ( $pw == $pw_user ) {		// проверяем кодовое слово
              $noexecmsg++;			// Засчитываем письмо и
              $err_code = include $module;	// запускаем необходимый модуль
              if ( $err_code != 1 )
                echo "Модуль возратил ошибку: $err_code\n";
            } else
              echo "Пароль задан неверно!\n";
          } else {
            echo "не существует!\n";
            $user = '';
          }; // if
        } else
          echo "Сообщение $j - пустое или не соответствует формату!\n";
      } else {
        echo "Модуль '{$h['subject']}' не предусмотрен!\n";
        if ( defined( 'DEL_ALL' ) )
          imap_delete( $mbox, $j );	// Удаляем все сообщения, если требуется
      }; // if
    }; // if
    if ( $user )			// Если пользователь активен - сохраняем кеш
      $runs[$user] .= ob_get_contents();
    ob_end_flush();			// Выводим в браузер
  }; // for
  echo "~~~~~~~~~~~~~~~~~~~~~~\n";
  echo "Выполнено сообщений: $noexecmsg";
  if ( defined( 'EXP_REQUIRE' ) )
    imap_close( $mbox, CL_EXPUNGE );
  else
    imap_close( $mbox );
} else
  echo 'Не удалось подключиться к серверу: ' . imap_last_error();

include '_local.php';
?>
