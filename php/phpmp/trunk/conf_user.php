<?php // Stan 21 окт€бр€ 2006г.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

// ѕароль дл€ этого пользовател€
   $pw_user = 'antispam_string';

// download   - открыт дл€ всех пользователей
// ==========================================
// ‘айлы больше следующего размера будут разбиватьс€ на блоки этого размера
// размер определ€етс€ после упаковывани€, если оно имеет место
   $split_file_size = 3145728;

// imap       - требуетс€ таблица
// ==========================================
// ѕредопределЄнные почтовые €щики
   $mboxes[$user][1] = array(	'mailbox'  => '{domain.net:143/notls}',
				                      'login'    => 'root',
				                      'password' => '*******' );
// Ёти €щики будут подсвечиватьс€ заданым цветом
   $highlight_from[$user][] = array( 'root', 'domain.net', 'green' );
   $highlight_to[$user][]   = array( 'root', 'domain.net', 'green' );

// mysql      - требуетс€ таблица
// ==========================================
// ѕредопределЄнные базы данных
   $mydbserver[$user][1] = array(	'dbhost'   => 'localhost',
					                        'dbuser'   => 'root',
					                        'password' => '*******' );

// phpbb      - требуетс€ таблица
// ==========================================
// ѕредопределЄнные форумы
   $myphpbb[$user][1] = array(	'user' => 'root',
				                        'scriptname' => '/root/www/forum/post.php' );

// selfupdate - доступ открыт
// ==========================
   $supdate_access = "$user+$pw_user";

// sh         - доступ открыт
// ==========================
   $shell_access = "$user+$pw_user";

// update     - доступ открыт
// ==========================
   $update_access = "$user+$pw_user";
   $update_dir = USER_DIR;

// ¬се письма будут отправл€тьс€ этому адресату
   $mail_to[$user] = 'root@domain.net';
?>
