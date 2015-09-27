<?php
	// Add js
	function ssm_theme_js() {
		// Add custom subpage handler
		$path = get_stylesheet_directory_uri() . '/js/subpage_handler.js';
		$dependencies = array( 'jquery' );
		wp_enqueue_script( 'subpage_handler_js', $path, $dependencies, true );
	}
	add_action( 'wp_enqueue_scripts', 'ssm_theme_js' );

	// Add css
	function ssm_theme_css() {
		// Add dashicons
		wp_enqueue_style( 'dashicons' );
	}
	add_action( 'wp_enqueue_scripts', 'ssm_theme_css' );

	// Add thumbnail support
	add_theme_support( 'post-thumbnails' );
?>
