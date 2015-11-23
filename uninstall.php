<?php
$options = get_option( 'tissheets_settings' );

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

if ( $options['full_uninstall'] ) {
	delete_option( 'tissheets_settings' );
}
