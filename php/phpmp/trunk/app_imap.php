<?php // Stan 2 ������ 2006�.
if ( !defined( 'LOCAL_DIR' ) ) die( 'Hacking attempt' );

include_once 'func_mail.php';
include_once 'func_other.php';
$mbi = 0;

for ( $i = 1; $i < mp_count( $message ); $i++ ) {
  list( $cmd, $params ) = mp_cmd( $message, $i );
      echo "---\n$cmd: \"$params\"\n";
      switch( $cmd ) {
//////////////////////////////////////////////////
        case 'open':	// �������� - ����� ������ ����� � $mboxes
//////////////////////////////////////////////////
          if ( $mbi )
            imap_close( $mbi, CL_EXPUNGE );
          if ( $nmbi = $params ) {
            $ibox = $mboxes[$user][$nmbi];
            if ( $mbi = imap_open( $ibox['mailbox'], $ibox['login'], $ibox['password'] ) ) {
              $nomsgi = imap_num_msg( $mbi );	// ���-�� ���������
              print_ra( imap_check( $mbi ) );
            } else
              echo '�� ������� ������� ����';
          }; // if
          break;
//////////////////////////////////////////////////
        case 'mailboxes':	// ��� ����������
        case 'headers':		// ��� ����������
//////////////////////////////////////////////////
          if ( $mbi ) {
            $r = $cmd == 'mailboxes'	? imap_listmailbox( $mbi, $ibox['mailbox'], '*' )
					: imap_headers ( $mbi );
            if ( $r )
              while ( list( $key, $val ) = each( $r ) )
                echo "$val\n";
            else
              echo '�� ����������';
          }; // if
          break;
//////////////////////////////////////////////////
        case 'header':		// $params - ����� ������
//////////////////////////////////////////////////
          if ( $mbi AND $params ) {
            $header = imap_header( $mbi, $params );
            print_ra( $header );
          }; // if
          break;
//////////////////////////////////////////////////
        case 'headers_table':	// ��� ����������, ���������� common.php
//////////////////////////////////////////////////
          if ( $mbi ) {
            $r = array();
            for ( $k = 1; $k <= $nomsgi; $k++ ) {
              $header = imap_header( $mbi, $k );
              if ( isset( $highlight_to[$user] ) )
                $head['to']      = full_name( $header->to[0], $highlight_to[$user] );
              else
                $head['to']      = full_name( $header->to[0] );
              if ( isset( $highlight_from[$user] ) )
                $head['from']    = full_name( $header->from[0], $highlight_from[$user] );
              else
                $head['from']    = full_name( $header->from[0] );
              if ( isset( $header->subject ) )
                $head['subject'] = htmlspecialchars( mp_decode( $header->subject ) );
              else
                $head['subject'] = '<i>No subject</i>';
              $head['Size']    = $header->Size;
              $head['no']      = $header->Msgno;
              $r[$k] = $head;
            }; // for
            print_rt( $r );
          }; // if
          break;
//////////////////////////////////////////////////
        case 'echo':		// �������� - ����� ���������
//////////////////////////////////////////////////
          $header = imap_header( $mbi, $params );
          print_ra( $header );
          $structure = imap_fetchstructure( $mbi, $params );
          print_ra( $structure );
          if ( $structure->type == 1 )		// ���� MULTI-PART ������
            $body = imap_fetchbody( $mbi, $params, '1' );
          else
            $body = imap_body( $mbi, $params );
          if ( isset( $structure->parameters[0]->attribute ) ) {
            $cp = strtolower( $structure->parameters[0]->value );
            echo $structure->parameters[0]->attribute . ": $cp\n";
            $cp_table = array(	'koi8-r' => 'k',	'windows-1251' => 'w',
				'iso8859-5' => 'i',	'x-cp866' => 'a',
				'x-mac-cyrillic' => 'm' );
            if ( isset( $cp_table[$cp] ) )
              $body = convert_cyr_string( $body, $cp_table[$cp], 'w' );
          }; // if
          if( !$body ) $body = '<i>�����</i>';
          echo "$body\n";
          break;
//////////////////////////////////////////////////
        case 'forward':		// ��������� ���� ������
//////////////////////////////////////////////////
/*
  if ( $content = imap_fetchbody( $mbi, $messageid, '2' ) )
    mail_content ( $mail_to[$user], $content, '2.jpg' );
  if ( $content = imap_fetchbody( $mbi, $messageid, '3' ) )
    mail_content ( $mail_to[$user], $content, '3.jpg' );
*/
          break;
//////////////////////////////////////////////////
        case 'delete':		// �������� - ����� ��� �������� �����
//////////////////////////////////////////////////
          if ( $mbi AND $params ) {
            $deleted = 0;	// ���-�� �������� ���������
            echo '������� ��������� ���������: ';
            $Parts = explode( ',', $params );
            while( list( $key, $val ) = each( $Parts ) )
              if ( is_numeric( $val ) AND $val <= $nomsgi ) {
                echo "$val, ";
                imap_delete( $mbi, $val );
                $deleted++;
              } elseif ( preg_match( '/(\d+)-(\d+)/', $val , $matches ) )
                if ( $matches[1] <= $nomsgi ) {
                  $start = $matches[1];
                  $stop = $matches[2] <= $nomsgi ? $matches[2] : $nomsgi;
                  if ( $start <= $stop ) {
                    echo "$start-$stop, ";
                    for ( $k = $start; $k <= $stop; $k++ )
                      imap_delete( $mbi, $k );
                    $deleted = $deleted + $stop - $start + 1;
                  }; // if
                }; // if
            echo "\n�������: $deleted";
          }; // if
          break;
//////////////////////////////////////////////////
        case 'delete_by_subject':	// �������� - ����� �� ���� ���������
//////////////////////////////////////////////////
          if ( $mbi AND $params ) {
            $deleted = 0;	// ���-�� �������� ���������
            echo '������� ��������� ���������: ';
            for ( $k = 1; $k <= $nomsgi; $k++ ) {
              $header = imap_header( $mbi, $k );
              if ( isset( $header->subject ) AND stristr( $header->subject, $params ) ) {
                echo "$k, ";
                imap_delete( $mbi, $k );
                $deleted++;
              }; // if
            }; // for
            echo "\n�������: $deleted";
          }; // if
          break;
//////////////////////////////////////////////////
        default:
//////////////////////////////////////////////////
          //${$cmd} = $params;
      }; // switch
      echo "\n";
}; // for

if ( $mbi )
  imap_close( $mbi, CL_EXPUNGE );
?>
