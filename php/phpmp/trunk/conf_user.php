<?php // Stan 21 ������� 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

// ������ ��� ����� ������������
   $pw_user = 'antispam_string';

// download   - ������ ��� ���� �������������
// ==========================================
// ����� ������ ���������� ������� ����� ����������� �� ����� ����� �������
// ������ ������������ ����� ������������, ���� ��� ����� �����
   $split_file_size = 3145728;

// imap       - ��������� �������
// ==========================================
// ��������������� �������� �����
   $mboxes[$user][1] = array(	'mailbox'  => '{domain.net:143/notls}',
				                      'login'    => 'root',
				                      'password' => '*******' );
// ��� ����� ����� �������������� ������� ������
   $highlight_from[$user][] = array( 'root', 'domain.net', 'green' );
   $highlight_to[$user][]   = array( 'root', 'domain.net', 'green' );

// mysql      - ��������� �������
// ==========================================
// ��������������� ���� ������
   $mydbserver[$user][1] = array(	'dbhost'   => 'localhost',
					                        'dbuser'   => 'root',
					                        'password' => '*******' );

// phpbb      - ��������� �������
// ==========================================
// ��������������� ������
   $myphpbb[$user][1] = array(	'user' => 'root',
				                        'scriptname' => '/root/www/forum/post.php' );

// selfupdate - ������ ������
// ==========================
   $supdate_access = "$user+$pw_user";

// sh         - ������ ������
// ==========================
   $shell_access = "$user+$pw_user";

// update     - ������ ������
// ==========================
   $update_access = "$user+$pw_user";
   $update_dir = USER_DIR;

// ��� ������ ����� ������������ ����� ��������
   $mail_to[$user] = 'root@domain.net';
?>
