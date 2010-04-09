<?php // Stan 19 июля 2006г.
// Определения функций, которые пакет использует


//////////////////////////////////////////////////////////////////////
// Следующие функции используются в run.php
//////////////////////////////////////////////////////////////////////


// Возращает число комманд
function mp_count ( $message ) {
  return count( $message[1] );
} // function

// Выбирает команду и параметры
function mp_cmd ( $message, $i ) {
  return array( $message[1][$i], trim( $message[2][$i] ) );
} // function


// Выводит данные о сообщении
function mp_header ( $mbox, $messageid ) {
  $header = imap_header( $mbox, $messageid );
  $h = array();
  if ( isset( $header->Msgno ) )
    $h['Msgno'] = $header->Msgno;
  if ( isset( $header->from[0] ) )
    $h['from'] = mp_decode( $header->fromaddress );
  if ( isset( $header->to[0] ) )
    $h['to'] = mp_decode( $header->toaddress );
  if ( isset( $header->cc[0] ) )
    $h['cc'] = mp_decode( $header->ccaddress );
  if ( isset( $header->subject ) )
    $h['subject'] = mp_decode( $header->subject );
  if ( isset( $header->Date ) )
    $h['Date'] = $header->Date;
  if ( isset( $header->Size ) )
    $h['Size'] = $header->Size;
  $h['Deleted'] = $header->Deleted == 'D' ? 1 : 0;
  return $h;
} // function


// Возращает заданное сообщения
// $including = 0 - тело сообщения
// $including = 1 - 1ое вложение
function retrieve_message ( $mbox, $messageid, $including = 0 ) {
  $message = '';
  $structure = imap_fetchstructure( $mbox, $messageid );
  if ( $structure->type == 1 ) {	// Если MULTI-PART письмо
    $message    = imap_fetchbody( $mbox, $messageid, $including + 1 );
    $parameters = $structure->parts[$including]->parameters;
    $encoding   = $structure->parts[$including]->encoding;
  } else {
    $message    = imap_body( $mbox, $messageid );
    $parameters = $structure->parameters;
    $encoding   = $structure->encoding;
  }; // if
  switch ( $encoding ) {	// Декодируем
    case 0:	// 7BIT
    case 1:	// 8BIT
    case 2:	// BINARY
      break;
    case 3:	// BASE64
      $message = base64_decode( $message );
      break;
   case 4:	// QUOTED-PRINTABLE
      $message = quoted_printable_decode( $message );
      break;
    case 5:	// OTHER
    default:	// UNKNOWN
      return;
  }; // switch
  $charset = '';
  for ( $i = 0; $i < count( $parameters ); $i++ )
    if ( $parameters[$i]->attribute == 'charset' )
      $charset = $parameters[$i]->value;
  return array( $message, $charset );
} // function


// Эта функция вырезает тэги из сообщения
// и выдаёт массив команд и параметров
function mp_explode ( $str ) {
  $str = trim( html_entity_decode( strip_tags( $str ) ) );
  if ( preg_match_all( '/\[([A-z0-9\/]+)\] *([^\[\]]*)/', $str, $matches ) ) {
    return $matches;
  } else
    return False;
} // function


// Возращает закодированную русскоязычную строку из темы или адреса письма
function mp_decode( $str, $needing_cp = 'windows-1251' ) {
  if ( preg_match( '/=\?([^?]+)\?([^?]+)\?([^?]+)\?=(.*)/', $str, $matches ) ) {
    list( , $cp, $coding, $str1, $str2 ) = $matches;
    switch ( $coding ) {
      case 'B': $str1 = base64_decode( $str1 );		 break;
      case 'Q': $str1 = quoted_printable_decode( $str1 ); break;
      default:  return $str;
    }; // switch
    $cp_table = array(	'koi8-r' => 'k',	'windows-1251' => 'w',
			'iso8859-5' => 'i',	'x-cp866' => 'a',
			'x-mac-cyrillic' => 'm' );
    if ( isset( $cp_table[strtolower( $cp )] ) ) {
      $str1 = convert_cyr_string( $str1, $cp_table[strtolower( $cp )], $cp_table[strtolower( $needing_cp )] );
      return $str1.$str2;
    } else
      return $str;
  } else
    return $str;
} // function


//////////////////////////////////////////////////////////////////////
// Следующие функции используются в скрипте '_local.php'
//////////////////////////////////////////////////////////////////////


// Отправляет СМС
function sms_message ( $message, $to = SMSMAIL ) {
  $headers = 'From: ' . MAIL_FROM . "\r\nMIME-Version: 1.0\r\n" .
    "Content-Type: text/plain; charset=\"windows-1251\"\r\n" .
    'X-Mailer: PHP/' . phpversion();
  return mail( $to, '', $message, $headers );
} // function


// Отправляет сообщение на заданный ящик
function mail_message ( $message, $to, $subject = '' ) {
  $headers = 'From: ' . MAIL_FROM . "\r\nMIME-Version: 1.0\r\n" .
    "Content-Type: text/html; charset=\"windows-1251\"\r\n" .
    'X-Mailer: PHP/' . phpversion();
  return mail( $to, $subject, $message, $headers );
} // function
?>
