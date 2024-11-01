<?php
/*
Plugin Name: Stripe Frontend Registration
Plugin URI: https://wordpress.org/plugins/stripe-frontend-registration/
Description: Wordpress registration using Stripe Plugin
Version: 1.1
Author: Prakash
Author URI: http://prakashparghi.com/
License: GPL
*/



$siteurl = get_option('siteurl');
$plugin_url = plugins_url();
define('PRO_FOLDER', dirname(plugin_basename(__FILE__)));
define('PRO_URL', $plugin_url.'/'. PRO_FOLDER);
define('PRO_FILE_PATH', dirname(__FILE__));
define('PRO_DIR_NAME', basename(PRO_FILE_PATH));
// this is the table prefix
global $wpdb;
$pro_table_prefix=$wpdb->prefix.'pro_';
define('PRO_TABLE_PREFIX', $pro_table_prefix);

register_activation_hook(__FILE__,'pro_install');
register_deactivation_hook(__FILE__ , 'pro_uninstall' );


wp_enqueue_script('inkthemes', plugins_url( '/js/check.js' , __FILE__ ) , array( 'jquery' ));
// including ajax script in the plugin Myajax.ajaxurl
wp_localize_script( 'inkthemes', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));





function pro_install()
{
	global $wpdb;
      
	$table2 = PRO_TABLE_PREFIX."registration_stripe_detail";
    $structure2 = "CREATE TABLE $table2 (
    		id int(11) NOT NULL AUTO_INCREMENT,
  			metaname varchar(255) NOT NULL,
 		 	value varchar(225) NOT NULL,
			PRIMARY KEY (`id`)
    );";
    $wpdb->query($structure2);
	
	
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (1,'registrationcharge', 5)");
		
	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (3,'secret_key', '')");

	$wpdb->query("INSERT INTO $table2 (id,metaname, value)
        VALUES (4,'publishable_key', '')");
		
		  
	  
	  
}
function pro_uninstall()
{
    global $wpdb;
	
	$table2 = PRO_TABLE_PREFIX."registration_stripe_detail";
    $structure2 = "drop table if exists $table2";
    $wpdb->query($structure2);  
}


add_action('admin_menu','pro_admin_menu');

function pro_admin_menu() { 
	add_menu_page(
		"Stripe Register",
		"Stripe Register",
		8,
		__FILE__,
		"pro_admin_menu_list",
		PRO_URL."/images/prakash.png"
	); 
	/*add_submenu_page(__FILE__,'Account Details','Account Details','8','list-charge','pro_admin_list_site');*/
}

function pro_admin_menu_list()
{
	 include 'register_amount.php';
}


// function for the site listing
function pro_admin_list_site()
{
	 include 'account_detail.php';
}


//Add ShortCode for "front end listing"
//Short Code [registartion_form]

add_shortcode("stripe_form","registartion_form_shortcode");
 function registartion_form_shortcode($atts) 
{ 
	  include 'registration_stripe_form.php';
}




add_action('init', array('pra_stripe', 'init'));
class pra_stripe {
	function init() { 
	 wp_enqueue_style( 'stripe_css', plugins_url('/css/stripe.css', __FILE__ )); 
     wp_register_script( 'stripe_js', plugins_url('/js/v2.js', __FILE__ ));
     wp_enqueue_script('stripe_js');
}}


function post_word_count(){
		$user_login = $_POST['user_login'];
		global $wpdb;
		$user_exist = mysql_num_rows(mysql_query('SELECT * FROM wp_users WHERE user_login="'.$user_login.'"'));
		$namesize = strlen($user_login);
		if($namesize<4)
		{
			echo 'Please enter username more than 3 charecters';
		}
		else if($user_exist > 0) //if username exist in wp_user table
		{
			echo 'Try another,this Username is already exist';
		}
		else
		{
			echo 'You can use this Username';
		}
		die();
		return true;
}
add_action('wp_ajax_post_word_count', 'post_word_count');
add_action('wp_ajax_nopriv_post_word_count', 'post_word_count');





 ?>