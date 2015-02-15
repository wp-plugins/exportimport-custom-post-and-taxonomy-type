<?php
/*
Plugin Name: Export/Import Custom Post  and Taxonomy Types
Plugin URI: 
Description: This is a addon to WordPress Creation Kit (WCK) plugin.This plugin enable import export functionality to WordPress Creation Kit plugin
Version: 1.0
Author: Nabajit Roy
Author URI:
Email: nabajitroy@gmail.com
*/
include( plugin_dir_path( __FILE__ ) . 'import.php');
function cpt_exporter_admin_menu(){
   add_submenu_page('wck-page', 'Export', 'Export', 'manage_options', 'cpt-export', 'export_custom_post_type');
   add_submenu_page('wck-page', 'Import', 'Import', 'manage_options', 'import', 'import_custom_post_type');
 }
 
 



 function export_custom_post_type(){
   ?>
   <div class='wrap'>
     <h2>Export</h2>
    <div class="post-types">
       <h2>Export Post types</h2>
	<form method="post" name="post_exporter_form" action="<?php echo plugin_dir_url( __FILE__ ); ?>export.php">
	    <h3>Select custom post types to export</h3>
	    <?php
	     $post_types = get_option( 'wck_cptc' );
	     foreach($post_types as $type => $value):
	      echo '<p><input  value="'. urlencode(base64_encode(serialize($value))) .'"  name="post_type[]" type="checkbox">'.$value['plural-label'].'</p>';
	     endforeach;
	    ?>
	    <p class="submit"><input type="submit" name="posts" value="Export Custom Post Types" /></p>
	</form>
    </div>
    
    <div class="taxonomy-types">
       <h2>Export Taxonomy types</h2>
	<form method="post" name="taxonomy_exporter_form" action="<?php echo plugin_dir_url( __FILE__ ); ?>export.php">
	    <h3>Select custom post types to export</h3>
	    <?php
	     $taxonomies = get_option( 'wck_ctc' );
	     foreach($taxonomies as $type => $value):
	      echo '<p><input  value="'. urlencode(base64_encode(serialize($value))) .'"  name="taxonomy_type[]" type="checkbox">'.$value['plural-label'].'</p>';
	     endforeach;
	    ?>
	    <p class="submit"><input type="submit" name="taxonomy" value="Export Custom Taxonomies" /></p>
	</form>
    </div>
    
    
   </div>
   <?php
 }
 


 
 
 add_action('admin_menu','cpt_exporter_admin_menu');
 add_action( 'admin_init', 'register_admin_style' );	//register file during initialization
function register_admin_style() {
	wp_register_style( 'exporter_admin_style', plugins_url('/css/cpt-exporter.css', __FILE__), false, '1.0.0', 'all' );
	wp_enqueue_style( 'exporter_admin_style' );
}

 
 
 
 
 
 

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 