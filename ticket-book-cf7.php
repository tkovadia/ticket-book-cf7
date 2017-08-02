<?php
/*
Plugin Name: Ticket Book Contact Form 7
Plugin URI: 
Description:  Simple ticket book contact form 7 module
Version: 1.0
Author: Tushar Patel
License: 
Text Domain: 
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if (! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) 
return;

register_activation_hook( __FILE__, 'my_plugin_create_db' );

function my_plugin_create_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'ticket_book_cf7';

	for($i=1;$i<=100;$i++ ) {
		$col[] = 'col'.$i.' tinyint(1)';
		}
	$val = implode(', ' ,$col);
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,".
		$val .",
		post_id mediumint(9) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

wpcf7_add_shortcode('ticket_book_cf7', 'wpcf7_ticket_book_cf7_shortcode_handler', true);
function wpcf7_ticket_book_cf7_shortcode_handler($tag) {
	$id = get_the_ID();
	global $wpdb;
	$table_name = $wpdb->prefix . 'ticket_book_cf7';
	
	$results = $wpdb->get_row( "SELECT * FROM ".$table_name." WHERE post_id =".$id ,ARRAY_N);
	$html .= '<p>
		<span class="wpcf7-form-control-wrap checkbox-934">
			<span class="wpcf7-form-control wpcf7-checkbox">';
				
				for($i=1;$i<=100;$i++ ) : 
				$val = $results[$i] ? ' checked disabled ': '' ;
				 $html .= '<span class="wpcf7-list-item ">
					<input name="ticket_book[col'.$i.']" value="1" type="checkbox" '.$val.'><span class="wpcf7-list-item-label">Seat '.$i.'</span>
				</span>';
				 endfor;
	 $html .= '</span>
		</span>
		<input type="hidden" name="page_id" value="' . get_the_ID() . '" />		
		</p>';
		
		
	return $html;
}//[ticket_book_cf7]

add_action("wpcf7_before_send_mail", "wpcf7_save_data");
function wpcf7_save_data($WPCF7_ContactForm)
{
	$wpcf7      = WPCF7_ContactForm::get_current();
	$submission = WPCF7_Submission::get_instance();
	if ($submission) :
		$data = $submission->get_posted_data();
		if (empty($data))
			return;
	   	$ticket_book   = isset($data['ticket_book']) ? $data['ticket_book'] : "";
		$page_id   = isset($data['page_id']) ? $data['page_id'] : "";
		
	  	global $wpdb;
	  	$table_name = $wpdb->prefix . 'ticket_book_cf7';
		$result = $wpdb->get_results('SELECT sum(post_id) as result_value FROM '.$table_name.' WHERE post_id = '.$page_id.'');
		if($result[0]->result_value) :
			$wpdb->update( $table_name, $ticket_book, array( 'post_id' => $page_id ), array( '%d', ),array( '%d' ));
		else:
			$ticket_book['post_id'] = $page_id ;
			$wpdb->insert($table_name, $ticket_book, array(  '%d' ) );
	  	endif;
	endif;
  
}
