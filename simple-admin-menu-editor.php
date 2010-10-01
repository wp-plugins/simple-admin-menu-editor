<?php

/*
Plugin Name: Simple Admin Menu Editor
Plugin URI: http://karmaprogressive.com/2010/09/our-wordpress-plugin-simple-admin-menu-editor/
Description: Easily and Simply edit what is in your admin nav. That's it.
Author: Chris Nowak
Version: 1.0.2
Author URI: http://facebook.com/chrisnowak
*/


function same_deactivate() {
	delete_option("admin-new-menu");
}
register_deactivation_hook( __FILE__, 'same_deactivate' );




function add_same_menu_option(){
	add_options_page(
	'Simple Admin Menu Editor',
	'Admin Menu Editor',
	'manage_options',
	basename(__FILE__),
	'same_menu_options'
	);
}
add_action('admin_menu','add_same_menu_option');


function same_admin_help($text, $screen) {
	// Let the default WP Dashboard help stuff through on other Admin pages
	$link = '</p><p><a href="/wp-admin/options-general.php?page=simple-admin-menu-editor.php">Show/Hide Nav Links</a></p></div>';
	return str_replace('</p></div>',$link,$text);
}

function admin_menus () {
global $menu, $remenu, $submenu, $resubmenu;
	$is_visible=false;
	$remenu = $menu; $resubmenu = $submenu;
		$new_menu = get_option('admin-new-menu');
			if($new_menu) {
				$new_menu = unserialize($new_menu);
					//print_r($new_menu);
					foreach($menu as $n=>$m) {
						if(!$new_menu[$n]) {
							if($menu[$n][0]<>'') {
								unset($menu[$n]);
							}
						}
						$n_menu[ $m[2] ] = $n;
					}
					foreach($submenu as $n=>$m) {
						//echo $n;
						if( !strpos('/',$n) ) {
							foreach($m as $t=>$y) {
								if( !$new_menu[ $n_menu[$n] ][$t] ) {
									unset($submenu[ $n ][$t]);
									
								} elseif($y[2]=='simple-admin-menu-editor.php') {$is_visible=true;}
								
	
							}
							
						} 
					}
					
				if($is_visible==false) {add_action( 'contextual_help', 'same_admin_help' );}			
			}

		//$menu[5] = array( __('Projects'), 'edit_posts', 'edit.php', '', 'open-if-no-js menu-top', 'menu-posts', 'div' );
}
add_action('admin_menu', 'admin_menus', 999);


function same_menu_options() {
	global $menu, $remenu, $submenu, $resubmenu;
	if($_POST) {
		//print_r($_POST); die();
		$p=serialize($_POST);
		update_option('admin-new-menu', $p);
		//refresh this page
		echo '<div><h2>Hang on...</h2><p>I\'m saving your menu</p></div>';
		//admin_menus();die();
		echo '<meta http-equiv="refresh" content="0;url='.$_SERVER['PHP_SELF'].'?page=simple-admin-menu-editor.php&success=1">';
		die();
	} // POST

?><div class="wrap">

	<h2>Simple Admin Menu Editor</h2>
		<?php if($_GET['success']=='1') {echo '<div id="message" class="updated fade"><p>Menu Updated.</p></div>';} ?>
		<form method="post" action="" style="margin-left: 30px">
			<?php
				foreach($remenu as $k=>$r) {
					//print_r($r);
					if($r[0]<>'') {
						$n = explode('<', $r[0]);
						echo '<input type="checkbox" name="'.$k.'" class="marker" id="'.$k.'"';
							if($menu[$k][0]==$r[0]) {echo ' checked="checked"';}
						echo ' style="margin-bottom: 4px"> '.strip_tags($n[0])."<br />";
							
							if($resubmenu[ $r[2] ]) {
								foreach($resubmenu[ $r[2] ] as $m=>$s) {
									$p = explode('<', $s[0]);
									echo '<input type="checkbox" name="'.$k.'['.$m.']" class="sub-marker-'.$k.'"';
										if($submenu[$r[2]][$m][0]==$s[0]) {echo ' checked="checked"';}
									echo ' style="margin-bottom: 4px;margin-left:20px"> '.strip_tags($p[0])."<br />";
								}
							}
							
					}
				}
				//print_r($menu);
			?>
			<br /><input type="submit" value="Save My Menu" />
		</form>
		
		<?php //print_r($submenu); ?>


</div>
<script type="text/javascript">
	jQuery(function($) {
		$('.marker').click(function() {
			if( $(this).is(':checked') ) {
				$('.sub-marker-'+ $(this).attr('id') ).attr('checked', 'checked');
			} else {
				$('.sub-marker-'+ $(this).attr('id') ).attr('checked', '');
			}
		}); 
	});
</script>

<?php } ?>