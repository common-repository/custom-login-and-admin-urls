<?php

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

// Delete option from options table
delete_option( 'clau_login_link' );

if ( is_multisite() )
	delete_site_option( 'wpmu_clau_login_link' );

?>