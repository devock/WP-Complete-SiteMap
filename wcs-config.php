<?php 
	/*** Configuration ***/
	
	class wcs_config {
		
  		const CAPABILITY = 'edit_posts'; // Privilège requis
  		const MENU_TITLE = 'WP Complete SiteMap'; // Titre du sous menu
  		const PAGE_TITLE = 'Configuration WP Complete SiteMap'; // Titre de la page d'administration
		
		private $textarea;
		
		function __construct() {
			$this->textarea = '[wcs]';
			
			add_action('admin_menu', array($this, 'add_submenu'));
		}
		
		function add_submenu() {
			add_submenu_page('options-general.php', self::PAGE_TITLE, self::MENU_TITLE, self::CAPABILITY, 'wcs-config', array($this, 'configuration'));
		}
		
		function configuration() {
			if(!current_user_can(self::CAPABILITY)) 
     			wp_die( __('You do not have sufficient permissions to access this page.') );
			
     		if(isset($_POST['submit'])){
     			$this->config_page_update($_POST);
     		}
			
			$this->set_html();
		}
		
		private function set_html() {
			?>
			<style>
				form { margin: 20px 0; }
				.wrap input[type="text"] { width: 400px; }
				.wrap label { margin-left: 10px; }
				.postbox { margin: 20px 0 0 0; }
			</style>
			<div class="wrap">
				<form action="" method="post">
					<div class="icon32" id="icon-options-general"><br></div>
					<h2><?php echo self::PAGE_TITLE; ?></h2>
															
						<p>You can generate a shortcode here. Choose all post types you want to show and if you want to show post types by categories.</p>

						<div class="inside">
							<h3 class="title">Select post type</h3>
							<?php
								$post_types = get_post_types( );
								
								foreach($post_types as $p) {
									$p = get_post_type_object($p);
									if($p->name != 'nav_menu_item' && $p->name != 'revision') {
										echo '<p><input type="checkbox" name="'.$p->name.'" id="'.$p->name.'" '.((isset($_POST[$p->name]) && $_POST[$p->name] == "on")?'checked="checked"':'').'><label for="'.$p->name.'">'.$p->labels->name.'</label></p>';
									}
								}
							?>
							
							<h3 class="title">Categorize post types</h3>
							<p><input type="checkbox" name="categorize" id="categorize" <?php echo ((isset($_POST['categorize']) && $_POST['categorize'] == "on")?'checked="checked"':''); ?>><label for="categorize">Do you want categorize post types ?</label></p>
							
							
							<h3 class="title">Exclude Pages (Select the pages you want to exclude)</h3>
							<?php
								$args = array(
									'sort_order' => 'ASC',
									'sort_column' => 'post_title',
									'hierarchical' => 1,
									'authors' => '',
									'child_of' => 0,
									'parent' => -1,
									'offset' => 0,
									'post_type' => 'page',
									'post_status' => 'publish'
								); 
								$pages = get_pages($args); 
																
								if(count($pages) > 0) {
									foreach($pages as $page) {
										echo '<p><input type="checkbox" name="exclude[]" id="exclude'.$page->ID.'" value="'.$page->ID.'" '.((isset($_POST['exclude']) && in_array($page->ID, $_POST['exclude']))?'checked="checked"':'').'><label for="exclude'.$page->ID.'">'.$page->post_title.'</label></p>'; 
									}
								}
							?>
							
							<h3 class="title">Exclude Posts (Select the posts you want to exclude)</h3>
							<?php
								$args = array(
									'posts_per_page'  => -1,
									'offset'          => 0,
									'order'           => 'DESC',
									'post_type'       => 'post',
									'post_status'     => 'publish',
								); 
								
								$the_query = new WP_Query($args);
							?> 
							<?php if($the_query->have_posts()) : ?>
								<?php
									
									while($the_query->have_posts()) : $the_query->the_post();
										global $post;
										echo '<p><input type="checkbox" name="exclude[]" id="exclude'.$post->ID.'" value="'.$post->ID.'" '.((isset($_POST['exclude']) && in_array($post->ID, $_POST['exclude']))?'checked="checked"':'').'><label for="exclude'.$post->ID.'">'.get_the_title().'</label></p>'; 
									endwhile;
								?>
							<?php endif; ?>
							<?php wp_reset_query(); ?>
						</div>
					<p><input type="submit" value="Generate" class="button-primary" name="submit" /></p>
				</form>	
			</div>
			
			<p>Copy and paste this shortcode on a page : </p>
			<textarea cols="80" rows="5"><?php echo $this->textarea; ?></textarea>
			
			<?php
		}
		
		private function config_page_update($post) {		
			$select_post = array();
			
			$post_types = get_post_types();
			
			foreach($post_types as $p) {
				$p = get_post_type_object($p);
				if($p->name != 'nav_menu_item' && $p->name != 'revision') {
					if(isset($post[$p->name]) && $post[$p->name] == "on") {
						$select_post[] = $p->name;
					}
				}
			}
			
			$this->textarea = '[wcs';
			
			if(count($select_post) > 0) {
				$this->textarea .= ' post="';
				foreach($select_post as $key => $sp) {
					$this->textarea .= (($key>0)?',':'').$sp;
				}
				$this->textarea .= '"';
			}
			
			if(isset($post['categorize']) && $post['categorize'] == "on") {
				$this->textarea .= ' categorize="on"';
			} else {
				$this->textarea .= ' categorize="off"';
			}
			
			if(isset($post['exclude']) && count($post['exclude']) > 0) {
				$exclude = '';
				
				foreach($post['exclude'] as $pe) {
					if($exclude != '') $exclude .= ',';
					$exclude .= $pe;
				}
				
				$this->textarea .= ' exclude="'.$exclude.'"';
			}
			
			$this->textarea .= ']';
		}	
	}