<?php
	/*** ShortCode ***/
	
	class wcs_shortcode {
		function __construct() {
		
		}
		
		function wcs_shortcode($att) {		
			$select_post = array();
			$categorize = '';
			
			
			// Select the post types 
			if(isset($att['post']) && !empty($att['post'])) {
				foreach(explode(',', $att['post']) as $p) {
					$select_post[] = $p;
				}
			} else {
				$post_types = get_post_types( );
								
				foreach($post_types as $p) {
					$p = get_post_type_object($p);
					if($p->name != 'nav_menu_item' && $p->name != 'revision') {
						$select_post[] = $p->name;
					}
				}
			}
			
			// Check categorize
			if(isset($att['categorize']) && ($att['categorize'] == 'on' || $att['categorize'] == 'off')) {
				$categorize = $att['categorize'];
			} else {
				$categorize = 'on';
			}
			
			// check exclude
			if(isset($att['exclude']) && !empty($att['exclude'])) {
				$exclude = $att['exclude'];
			} else {
				$exclude = null;
			}
			
			// foreach post types
			foreach($select_post as $p) {
				$p = get_post_type_object($p);
								
				echo '<h2>'.$p->labels->name.'</h2>';
				
				if($categorize == "on") {
					$categories = get_categories(array(
						'type' 		=> $p->name,
						'taxonomy' 	=> 'category' 
					));
					
					foreach($categories as $c) {				
						$this->getLoop($p, $c, $exclude);
					}
					
					
					$this->getLoop($p, -1, $exclude);
				} else {
					$this->getLoop($p, null, $exclude);
				}
			}	
		}
		
		function getLoop($p, $cat = null, $exclude = null) {
			$args = array(
				'post_type' => $p->name,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'orderby' => 'menu_order ID', 
				'order' => 'DESC' 
			);
			
			if(isset($exclude) && !is_null($exclude)) {
				$args['exclude'] = $exclude;
			}

			
			if(!is_null($cat)) {
				if($cat != -1)
					$args['cat'] = $cat->term_id;
				else 
					$args['cat'] = -1;
			}
						
			
			$myposts = get_posts($args);
			
			if(count($myposts) >0) {
				if(!is_null($cat) && $cat != -1) { // if Categorize == true
					echo '<h3><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></h3>';
				}
				
				echo '<ul>';
					foreach($myposts as $post) {
						echo '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
					}
				echo '</ul>';
			}
		}
	}