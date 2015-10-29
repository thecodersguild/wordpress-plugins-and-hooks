<?php
/*
 * Plugin Name: Minimum Viable Plugin
 * Description: Our least effort required
 * @see https://codex.wordpress.org/File_Header
 */

add_filter( 'the_content', 'mvp_the_content' );

function mvp_the_content( $content ) {

	$content .= '<p>Copyright (c) 2015 The Coder\'s Guild</p>';

	return $content;

}





