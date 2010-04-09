<?php // Stan 22 октября 2006г.
// Определения функций, связанные с функцией mail - отправка почты


// Отправляет текст в виде вложения сообщения
// Если файл может паковаться - пакует
// $to - получатель
// $content - содержимое (что нужно отправить)
// $filename - имя вложенного в письмо файла
// $subject - тема письма, если не задан, или имя файла
function mail_content ( $to, $content, $filename = 'default', $subject = '' ) {
  if ( preg_match( '/(.*)\.([^.]*)$/', $filename, $matches ) ) {
    $filename = $matches[1];			// имя файла
    $ext      = $matches[2];			// и расширение если есть
  } else
    $ext      = 'html';				// если нет - присваиваем
  switch ( $ext ) {
    case 'exe': case '7z': case 'rar': case 'zip':	// Архивы не пакуем
    case 'tgz': case 'gz': case 'tbz': case 'bz2':
    case 'cab': case 'pkg': case 'msi':
    case 'mp3': case 'wma':				// Музыку
    case 'jpg': case 'png': case 'gif':			// Картинки тоже
    case 'avi': case 'mov': case 'mpg': case 'divx':	// и видео
      break;
    default:						// Всё остальное - пакуем
      if ( strlen( $content ) <= MAX_ARCH_SIZE ) {
        $content = gzencode( $content, 6 );
        $ext .= '.gz';
      }; // if
  }; // switch
  $i = 0;
  while ( $subcontent = substr( $content, $i * MAX_SEND_SIZE, MAX_SEND_SIZE ) ) {
    $fname = $i ? "{$filename}_$i.$ext" : "$filename.$ext";
    $subcontent = chunk_split( base64_encode( $subcontent ) );
    $headers = 'From: ' . MAIL_FROM . "\r\nMIME-Version: 1.0\r\n" .
      "Content-Transfer-Encoding: base64\r\n" .
      "Content-Type: application/octet-stream; name=\"$fname\"\r\n" .
      "Content-Disposition: attachment; filename=\"$fname\"\r\n" .
      'X-Mailer: PHP/' . phpversion();
    mail( $to, $subject ? ( $i ? $fname : $subject ) : $fname, $subcontent, $headers );
    $i++;
  }; // while
  return 1;
} // function


// Отправляет файл / директорию по мылу (без сжатия)
// При отправке папки игнорирует все вложенные папки
// $to - получатель
// $path - путь к ресурсу - абсолютный
// $subject - тема письма, если не задан, то путь (папка) или имя (файл)
function mail_file ( $to, $path, $subject = '', $delivery_message = '' ) {
  if ( !file_exists( $path ) )		// Если ресурса не существует, то выход
    return 0;
  if ( is_dir( $path ) ) {
    $d = dir( $path );
    $dirsize = 0;		// считаем размер каталога
    $headers = 'From: ' . MAIL_FROM . "\r\nMIME-Version: 1.0\r\n" .
      "Content-Type: multipart/mixed;\r\n" .
      "  boundary=\"----------ABCD0123456789\"\r\n" .
      'X-Mailer: PHP/' . phpversion();
    $message = '';
    while ( false !== ( $entry = $d->read() ) )
      if ( $entry != '.' AND $entry != '..' ) {
        $content = file_get_contents( "$path/$entry" );
        $dirsize += strlen( $content );
// Если первый файл в каталоге - html, то и посылаем его как сообщение письма
        if ( !$message AND preg_match( '/.*\.html?/', $entry ) )
          $message .= "------------ABCD0123456789\r\n" .
            "Content-Transfer-Encoding: base64\r\n" .
            "Content-Type: text/html;\r\n\r\n";
        else
          $message .= "------------ABCD0123456789\r\n" .
            "Content-Transfer-Encoding: base64\r\n" .
            "Content-Type: application/octet-stream; name=\"$entry\"\r\n" .
            "Content-Disposition: attachment; filename=\"$entry\"\r\n" .
            "Content-ID: <$entry>\r\n\r\n";
        $message .= chunk_split( base64_encode( $content ) ) . "\r\n";
      }; // if
    $d->close();
    if ( $message ) {
      if ( $delivery_message )
        $message .= "------------ABCD0123456789\r\n" .
          "Content-Type: message/delivery-status\r\n" .
          "Content-Transfer-Encoding: 8bit\r\n\r\n$delivery_message\r\n";
      $message .= "------------ABCD0123456789--";
      mail( $to, $subject ? $subject : "$path($dirsize)", $message, $headers );
    }; // if
  } else {
    $content = file_get_contents( $path );
    mail_content( $to, $content, basename( $path ), $subject );
  }; // if
  return 1;
} // function
?>
