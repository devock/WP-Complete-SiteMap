<?php
	/*
		Plugin Name: WP Complete SiteMap
		Plugin URI: http://www.devock.com
		Description: You can create a page with a sitemap. You can select post types you want to show and if you want show post types by categories. You can use the default shortcode [wcs] or go to settings for have complete shortcode.
		Version: 0.5
		Author: Sébastien Trutié de Vaucresson
		Author URI: http://www.devock.com
		License: GPLv2
	*/
	
	require('wcs-config.php');
	require('wcs-shortcode.php');

	class wp_complete_sitemap {
		public $plugin;

		public function __construct() {
			// add_action('init', array($this, 'translation'));
			
			$wcs_config = new wcs_config();
			$wcs_config = new wcs_shortcode();
			
			add_shortcode('wcs', array($wcs_config, 'wcs_shortcode'));
			
			$this->plugin =  plugin_basename(__FILE__);
			add_filter( "plugin_action_links_".$this->plugin, array($this, 'plugin_add_settings_link'));
		}		
		
		function plugin_add_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=wcs-config">Settings</a>';
			array_push( $links, $settings_link );
			return $links;
		}
	}

	$wcs = new wp_complete_sitemap();
