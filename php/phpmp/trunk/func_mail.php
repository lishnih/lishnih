<?php // Stan 22 ������� 2006�.
// ����������� �������, ��������� � �������� mail - �������� �����


// ���������� ����� � ���� �������� ���������
// ���� ���� ����� ���������� - ������
// $to - ����������
// $content - ���������� (��� ����� ���������)
// $filename - ��� ���������� � ������ �����
// $subject - ���� ������, ���� �� �����, ��� ��� �����
function mail_content ( $to, $content, $filename = 'default', $subject = '' ) {
  if ( preg_match( '/(.*)\.([^.]*)$/', $filename, $matches ) ) {
    $filename = $matches[1];			// ��� �����
    $ext      = $matches[2];			// � ���������� ���� ����
  } else
    $ext      = 'html';				// ���� ��� - �����������
  switch ( $ext ) {
    case 'exe': case '7z': case 'rar': case 'zip':	// ������ �� ������
    case 'tgz': case 'gz': case 'tbz': case 'bz2':
    case 'cab': case 'pkg': case 'msi':
    case 'mp3': case 'wma':				// ������
    case 'jpg': case 'png': case 'gif':			// �������� ����
    case 'avi': case 'mov': case 'mpg': case 'divx':	// � �����
      break;
    default:						// �� ��������� - ������
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


// ���������� ���� / ���������� �� ���� (��� ������)
// ��� �������� ����� ���������� ��� ��������� �����
// $to - ����������
// $path - ���� � ������� - ����������
// $subject - ���� ������, ���� �� �����, �� ���� (�����) ��� ��� (����)
function mail_file ( $to, $path, $subject = '', $delivery_message = '' ) {
  if ( !file_exists( $path ) )		// ���� ������� �� ����������, �� �����
    return 0;
  if ( is_dir( $path ) ) {
    $d = dir( $path );
    $dirsize = 0;		// ������� ������ ��������
    $headers = 'From: ' . MAIL_FROM . "\r\nMIME-Version: 1.0\r\n" .
      "Content-Type: multipart/mixed;\r\n" .
      "  boundary=\"----------ABCD0123456789\"\r\n" .
      'X-Mailer: PHP/' . phpversion();
    $message = '';
    while ( false !== ( $entry = $d->read() ) )
      if ( $entry != '.' AND $entry != '..' ) {
        $content = file_get_contents( "$path/$entry" );
        $dirsize += strlen( $content );
// ���� ������ ���� � �������� - html, �� � �������� ��� ��� ��������� ������
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
