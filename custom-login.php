<?php

/*
Plugin Name: Custom Login and Admin URLs (CLAU)
Plugin URI: http://www.vcarvalho.pt/wordpress-plugins/custom-login-and-admin-urls
Version: 0.1.2
Author: lightningspirit
Author URI: http://vcarvalho.com
Description: Change wp-admin and wp-login URL to any of your choice. Security by obscurity!
Tags: plugin, security, login, admin, wp-admin, wp-login, security by obscurity, custom url,
License: GPL3
*/


/**
 * Custom Login and Admin URLs (CLAU)
 *
 * @package WordPress
 * @subpackage Custom Login and Admin URLs
 * @since 0.1
 * @author lightningspirit
 * @copyright Lightningspirit.NET 2011
 * @credits Based on the excelent work of Ozh (http://ozh.org/) with Ozh' Pretty Login URL (http://planetozh.com/blog/?s=pretty+login+url)
 * This code is released under the GPL licence version 3 or later
 * http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * 
 */



/*
 *      Custom Login and Admin URLs (CLAU)
 *      
 *      Copyright 2011 Lightningspirit.NET <email@vcarvalho.com>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 3 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */
 



/* 
 * CHANGELOG:
 * 
 * 0.1.2	Bugfix: Login wasn't redirect from wp-admin. Throw a filter error.
 * 
 * 0.1.1	Bugfixes: login not working properly after install
 * 
 * 0.1 - Initial Release
 * ---------------------
 * · Sets basic login and admin redirects.
 * · Able to change links through permalink options page.
 * 
 * 
 */

 
 
 
/*
 * TODO:
 * 
 * 0.1	Create verification for basic input of trailling slashes
 * 		Develop a method to redirect wp-admin (which I could not do it because of the lack of filters, actions and even documentation...)
 * 
 */

 
 
 
 


// Checks if it is accessed from Wordpress call
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
	
}



$wp_clau_textdomain = 'custom-login-and-admin-urls';



// Wordpress version control. No compatibility with older versions.
if ( version_compare( get_bloginfo( 'version' ), '3.2', '<' ) ) {
	wp_die( 'Custom Login and Admin URLs can be used only with Wordpress 3.2 version' );

}






/* Actions, Filters and activation hooks */

register_activation_hook( __FILE__, 'wp_clau_activate' );
register_deactivation_hook( __FILE__, 'wp_clau_deactivate' );

add_action( 'plugins_loaded', 'wp_clau_plugins_loaded' );
add_action( 'init', 'wp_clau_rewrite' );
add_action( 'admin_init', 'wp_clau_options_page' );
add_action( 'login_init', 'wp_clau_login_init' );
add_action( 'template_redirect', 'wp_clau_display' );

add_filter( 'login_url', 'wp_clau_login_url_filter', 2, 10 );
add_filter( 'site_url', 'wp_clau_site_url_filter', 4, 10 );
add_filter( 'wp_redirect', 'wp_clau_logout_url_filter' );



/**
 * Loads textdomain
 * 
 * @since 0.1
 */
function wp_clau_plugins_loaded() {
	global $wp_clau_textdomain;
	
	load_plugin_textdomain( $wp_clau_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	if ( is_multisite() ) {
		add_action( 'wpmu_options', 'wpmu_clau_options' ); // Add options to multisite
		add_action( 'wpmu_new_blog', 'wpmu_clau_new_blog' );
		
		if ( isset( $_POST['wpmu_clau_login_link'] ) ) 
			add_site_option( 'wpmu_clau_login_link', strip_tags( $_POST['wpmu_clau_login_link'] ) );
		
	}

}


/**
 * Activate the Plugin 
 * Add new rewrites and flushes the rules
 * 
 * @since 0.1
 */
function wp_clau_activate() {
/*	if ( ! get_option( 'clau_admin_link' ) )
		update_option( 'clau_admin_link', 'admin' );
*/		
	if ( ! get_option( 'clau_login_link' ) )
		update_option( 'clau_login_link', 'login' );
		
	if ( ! get_option( 'clau_login_link' ) && is_multisite() ) {
		update_option( 'clau_login_link', get_site_option( 'wpmu_clau_login_link' ) ? get_site_option( 'wpmu_clau_login_link' ) : 'login' );
		
	}
	
	wp_clau_rewrite();
	flush_rewrite_rules();

}


/**
 * Displays the login and admin screens
 * 
 * @since 0.1
 */
function wp_clau_display() {
	global $wp_query;
	//var_dump($wp_query);
	
	switch( $wp_query->query_vars['pagename'] ) {
		
		case get_option( 'clau_login_link' ) :
			include( ABSPATH . '/wp-login.php' );
			exit;
			break;
		
/*	
		case get_option( 'clau_admin_link' ) :
			include( ABSPATH . '/wp-admin/index.php' );
			break;
			//include( ABSPATH . '/wp-admin/'.$query_var.'.php' );
		
*/	}
	
}


/** 
 * Rewrite Rules
 * 
 * add_rewrite_rules to 'dashboard' (wp-admin) and 'login' (wp-login.php)
 * 
 * @since 0.1
 */

function wp_clau_rewrite() {
	
	add_rewrite_tag( '%login%', '([^/]+)' );
	add_permastruct( 'login', '/%login%' );
/*	add_rewrite_tag( '%admin%', '([^/]+)' );
	add_permastruct( 'admin', '/%admin%' );
	//add_rewrite_rule( get_option( 'clau_login_link' ) . '/?([^/]*)', 'wp-login.php?'.$matches[1], 'top' );
	//add_rewrite_rule( get_option( 'clau_admin_link' ) . '/?([^/]*)', 'wp-admin/'.$matches[1], 'top' );
*/
}



/**
 * Deactivate plugin 
 * Flushes the rules
 * 
 * @since 0.1
 */ 

function wp_clau_deactivate() {
	flush_rewrite_rules();
	
}





/**
 * Add options in Permalink Rules options page
 * 
 * @since 0.1
 */
function wp_clau_options_page() {
	global $wp_clau_textdomain;
	
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to manage permalink options for this site.', $wp_clau_textdomain ) );
		
		
	if ( /*isset( $_POST['clau_admin_link'] ) && */isset( $_POST['clau_login_link'] ) ) {
		//check_admin_referer( 'clau_login_link_nonce' );
//		update_option( 'clau_admin_link', strip_tags( $_POST['clau_admin_link'] ) );
		update_option( 'clau_login_link', sanitize_key( wp_strip_all_tags( $_POST['clau_login_link'] ) ) );
		wp_clau_rewrite();
		
	}
	
//	add_settings_field( 'clau_admin_link', __( 'Administration Base' ), 'wp_clau_options_page_admin_link', 'permalink', 'optional' );
	add_settings_field( 'clau_login_link', __( 'Login Base', $wp_clau_textdomain ), 'wp_clau_options_page_login_link', 'permalink', 'optional' );
	
//	register_setting( 'permalink', 'clau_admin_link', 'strval' );
	register_setting( 'permalink', 'clau_login_link', 'strval' );
	
}



/**
 * Prevents the user having access to wp-login.php directly
 * 
 * @since 0.1
 */
function wp_clau_login_init() {
	if ( '/wp-login.php' == $_SERVER['PHP_SELF'] ) {
		header("HTTP/1.0 404 Not Found");
		global $wp_query;
		$wp_query->set_404();
		require TEMPLATEPATH . '/404.php';
		exit;
		
	}
	
}



/**
 * Add options in Permalink Rules options page
 * 
 * @since 0.1
 *//*
function wp_clau_options_page_admin_link() {
		
?>
<input id="clau_admin_link" name="clau_admin_link" type="text" class="regular-text code" value="<?php echo get_option( 'clau_admin_link' ); ?>" />
<?php
	
}*/




/**
 * Add options in Permalink Rules options page
 * 
 * @since 0.1
 */
function wp_clau_options_page_login_link() {
	global $wp_clau_textdomain;
?>
<input id="clau_login_link" name="clau_login_link" type="text" class="regular-text code" value="<?php echo get_option( 'clau_login_link' ); ?>" />
<p class="howto"><?php _e( 'Allowed characters are a-z, 0-9, - and _', $wp_clau_textdomain ); ?></p>
<?php
	
}




/**
 * Options for Multisite
 * 
 * @since 0.1.1
 */
function wpmu_clau_options() {
	global $wp_clau_textdomain;
?>
<h3><?php _e( 'Permalink Settings', $wp_clau_textdomain ); ?></h3>
<table id="permalink" class="form-table">
	<?php do_action( 'wpmu_permalink_options' ); ?>
	<tr valign="top">
		<th scope="row">
			<label for="wpmu_clau_login_link"><?php _e( 'Default Login Base', $wp_clau_textdomain ); ?></label>
		</th>
		<td>
			<input type="text" name="wpmu_clau_login_link" id="wpmu_clau_login_url" value="<?php echo get_site_option( 'wpmu_clau_login_link' ); ?>" />
			
		</td>
	</tr>
	<?php do_action( 'wpmu_permalink_options_2' ); ?>
</table>
<?php
}



/**
 * Sets options for new blogs
 * 
 * @since 0.1.1
 */
function wpmu_clau_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	add_blog_option( $blog_id, 'clau_login_link', get_site_option( 'wpmu_clau_login_link' ) );
	
}




/**
 * Filters the login_url
 * 
 * @since 0.1
 */
function wp_clau_login_url_filter( $login_url, $redirect ) {
	return str_replace( 'wp-login.php', get_option( 'clau_login_link' ), $login_url );
	
}


/**
 * Filters the login_url by matching wp-login.php
 * 
 * @since 0.1
 */
function wp_clau_site_url_filter( $url, $path, $orig_scheme, $blog_id ) {
	if ( 'login' == $orig_scheme || 'login_post' == $orig_scheme ) {
		$url = str_replace( 'wp-login.php', get_option( 'clau_login_link' ), $url );
		
	}
	
	return $url;
	
}


/**
 * Filters the logout
 * 
 * @since 0.1.1
 */
function wp_clau_logout_url_filter( $location ) {
	return str_replace( 'wp-login.php', get_option( 'clau_login_link' ), $location );
	
}


?>