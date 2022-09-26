<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/* Retrieve the published extensions from wpplugin.org and store within transient. */
function etimeclockwp_get_extensions()	{
	$extensions = get_transient( '_etimeclockwp_extensions_feed' );

	if ( false === $extensions || doing_action( 'etimeclockwp_daily_scheduled_events' ) ) {
		$route    = esc_url( 'https://wpplugin.org/edd-api/products/' );
		$number   = 20;
		$endpoint = add_query_arg( array( 'number' => $number ), $route );
		$response = wp_remote_get( $endpoint );
		
		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$body    = wp_remote_retrieve_body( $response );
			$content = json_decode( $body );
			
			if ( is_object( $content ) && isset( $content->products ) ) {
				set_transient( '_etimeclockwp_extensions_feed', $content->products, DAY_IN_SECONDS / 2 ); // Store for 24 hours
				$extensions = $content->products;
			}
		}
	}

	return $extensions;
}
add_action( 'etimeclockwp_daily_scheduled_events', 'etimeclockwp_get_extensions' );
