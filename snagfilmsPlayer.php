<?php
/*
	Plugin Name: SnagFilms WP Embeded Player
	Plugin URI: http://wordpress.org/extend/plugins/snagfilms-player-embed/
	Description: Adds support for embedding a SnagFilms Film on your blog using a shortcode; ex: [snagfilm id="ea4af620-a748-11e0-a92a-0026bb61d036"].
	Version: 1.0.0
	Author: SnagFilms
	Author URI: http://profiles.wordpress.org/snagfilms/
	License: GNU GENERAL PUBLIC LICENSE GPL2
*/
/*  Copyright 2012  SnagFilms  (email : adam@snagfilms.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !function_exists( 'embed_snagplayer_shortcode' ) ) :
	function snagplayer_enqueue_script() {
		wp_enqueue_script( 'jquery' );
	}
	add_action('wp_enqueue_scripts', 'snagplayer_enqueue_script');
	
	//embed snagplayer function
	function embed_snagplayer_shortcode($atts, $content = null) {
		$defaults = array(
			'id' => '',
			'width' => '500',
			'height' => '350',
			'scrolling' => 'no',
			'frameborder' => '0'
		);
		foreach ($defaults as $default => $value) { //add default values
			if (!@array_key_exists($default, $atts)) { //hide warning if no params
				$atts[$default] = $value;
			}
		}
		//Inject Snag iframe embed player
		$html = '';
		if( isset( $atts["same_height_as"] ) ){
			$same_height_as = $atts["same_height_as"];
		}else{
			$same_height_as = '';
		}
		if( $same_height_as != '' ){
			$atts["same_height_as"] = '';
			if( $same_height_as != 'content' ){ //set height of iframe area
				if( $same_height_as == 'document' || $same_height_as == 'window' ){
					$target_selector = $same_height_as;
				}else{
					$target_selector = '"' . $same_height_as . '"';
				}
				$html .= '
					<script>
					jQuery(document).ready(function($) {
						var target_height = $(' . $target_selector . ').height();
						$("iframe.' . $atts["class"] . '").height(target_height);
						//alert(target_height);
					});
					</script>
				';
			}else{ //set height of iframe with no scroll
				$html .= '
					<script>
					jQuery(document).ready(function($) {
						$("iframe.' . $atts["class"] . '").bind("load", function() {
							var embed_height = $(this).contents().find("body").height();
							$(this).height(embed_height);
						});
					});
					</script>
				';
			}
		}
        $html .= "\n".'<!-- SnagFilms Embed Player Plugin v:1.0 - snagfilms.com/ -->'."\n";
		$html .= '<iframe';
        foreach ($atts as $attr => $value) {
			if( $attr != 'same_height_as' ){ //remove unused attributes
				if( $value != '' ) { //add attributes to iframe
					if ($attr == 'id') {
						$html .= ' ' . 'src' . '=http://embed.snagfilms.com/embed/player?filmId=' . $value . '&w=500';
					} else {
						$html .= ' ' . $attr . '="' . $value . '"';
					}
				}
			}
		}
		$html .= '></iframe>';
		return $html;
	}
	add_shortcode('snagfilm', 'embed_snagplayer_shortcode');
endif;
?>