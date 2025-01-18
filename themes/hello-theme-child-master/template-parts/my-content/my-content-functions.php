<?php


function content_styles() {

	wp_enqueue_style( 'content-css', get_stylesheet_directory_uri() . '/template-parts/my-content/content-styles.css');
}

add_action( 'wp_enqueue_scripts', 'content_styles', 15 );



function favourite_feed($atts){
	ob_start();
	echo get_template_part('template-parts/my-content/button-feed');
	return ob_get_clean();
}
add_shortcode('favourite_feed', 'favourite_feed');


?>