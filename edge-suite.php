<?php
/*
Plugin Name: Edge Suite
Plugin URI: http://edgedocks.com/edge_suite
Description: Upload Adobe Edge compositions to your website.
Author: Timm Jansen
Author URI: http://timm-jansen.net/
Version: 0.3
*/

/*  Copyright 2012 Timm Jansen (email: info at timm-jansen.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * This is a port of the Drupal Edge Suite module (done by me as well) for wordpress.
*/

require_once('includes/edge-suite-general.php');
require_once('includes/edge-suite-comp.inc');

/**
 * Set all needed constants
 */
function edge_suite_init_constants(){
  // Respect general upload path.
  $upload_dir = get_option('upload_path');
  if (empty($upload_dir)) {
    $upload_dir = 'wp-content/uploads';
  }
  $upload_dir = untrailingslashit($upload_dir);

  define('EDGE_SUITE_PUBLIC_DIR_REL', get_bloginfo('wpurl') . '/' . $upload_dir . '/edge_suite');
  define('EDGE_SUITE_PUBLIC_DIR', untrailingslashit(ABSPATH) . '/' . $upload_dir . '/edge_suite');

  define('EDGE_SUITE_COMP_PROJECT_DIR', EDGE_SUITE_PUBLIC_DIR . '/project');
  define('EDGE_SUITE_COMP_PROJECT_DIR_REL', EDGE_SUITE_PUBLIC_DIR_REL . '/project');


  define('EDGE_SUITE_ALLOWED_ASSET_EXTENSIONS', 'js|png|jpg|gif|svg|css');


  define('REQUEST_TIME', time());

}

/*** UN/INSTALL ***/

function edge_suite_install() {
  // Create DB schema.
  global $wpdb;
  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "
      CREATE TABLE " . $table_name . " (
        definition_id int(11) NOT NULL AUTO_INCREMENT,
        project_name varchar(255) NOT NULL,
        composition_id varchar(255) NOT NULL,
        archive_extension varchar(255) NOT NULL,
        info longtext,
        uid int(11) NOT NULL,
        created int(11) NOT NULL,
        changed int(11) NOT NULL,
        PRIMARY KEY (definition_id)
      );
    ";

    // TODO: Is this the correct way to do this?
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  // Default options.
  add_option('edge_suite_max_size', 2);
  add_option('edge_suite_comp_default', -1);
  add_option('edge_suite_comp_homepage', 0);
  add_option('edge_suite_deactivation_delete', 0);
  add_option('edge_suite_widget_shortcode', 0);

  // Create main edge suite directory.
  mkdir_recursive(trailingslashit(EDGE_SUITE_PUBLIC_DIR));
}

register_activation_hook(__FILE__, 'edge_suite_install');


function edge_suite_uninstall() {
  if(get_option('edge_suite_deactivation_delete') == 1){
    edge_suite_init_constants();

    global $wpdb;
    $table_name = $wpdb->prefix . "edge_suite_composition_definition";
    if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {
      $wpdb->query('DROP TABLE ' . $table_name);
    }

    // Delete all edge directories
    rmdir_recursive(trailingslashit(EDGE_SUITE_PUBLIC_DIR));

    // Delete options
    delete_option('edge_suite_max_size');
    delete_option('edge_suite_comp_default');
    delete_option('edge_suite_comp_homepage');
    delete_option('edge_suite_deactivation_delete');
    delete_option('edge_suite_widget_shortcode');
  }
}

register_deactivation_hook(__FILE__, 'edge_suite_uninstall');
//register_uninstall_hook(__FILE__, 'edge_suite_uninstall');


/**
* Register general options.
*/
function edge_suite_options_init() {
  register_setting('edge_suite_options', 'edge_suite_max_size');
  register_setting('edge_suite_options', 'edge_suite_comp_default');
  register_setting('edge_suite_options', 'edge_suite_comp_homepage');
  register_setting('edge_suite_options', 'edge_suite_deactivation_delete');
  register_setting('edge_suite_options', 'edge_suite_widget_shortcode');
}

add_action('admin_init', 'edge_suite_options_init');



/** INIT **/
function edge_suite_boot() {
  // Set up basic global edge object.
  global $edge_suite;
  $edge_suite = new stdClass();
  $edge_suite->header = array();
  $edge_suite->stage = "";
  $edge_suite->msg = array();

  edge_suite_init_constants();

  //Check if dir is writable and create directory structure.
  if (!mkdir_recursive(EDGE_SUITE_COMP_PROJECT_DIR)) {
    print sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), EDGE_SUITE_COMP_PROJECT_DIR_REL);
  }

}

/**
 * Init function that triggers composition rendering if needed.
 */
function edge_suite_init() {
  edge_suite_boot();

  // Get default composition.
  $definition_id = get_option('edge_suite_comp_default');

  // Get homepage composition.
  if (is_home()) {
    if (get_option('edge_suite_comp_homepage') != 0) {
      $definition_id = get_option('edge_suite_comp_homepage');
    }
  }
  //Get post composition
  else {
    global $post;
    $post_id = $post->ID;
    $post_reference_id = get_post_meta($post_id, '_edge_composition', TRUE);
    if (!empty($post_reference_id)) {
      $definition_id = $post_reference_id;
    }
  }

  // Render composition.
  global $edge_suite;
  $definition_res = edge_suite_comp_render($definition_id);
  // Split scripts and stage so they can be used by the respective functions.
  $edge_suite->scripts = isset($definition_res['scripts']) ? $definition_res['scripts'] : '';
  $edge_suite->stage = isset($definition_res['stage']) ? $definition_res['stage'] : '';

}

add_action('wp', 'edge_suite_init');


/** COMPOSITION **/

/**
 * Add needed scripts to the header that were located during composition
 * rendering in the init phase.
 */
function edge_suite_header() {
  global $edge_suite;
  if(isset($edge_suite->scripts) && is_array($edge_suite->scripts)){
    print "\n" . implode("\n", $edge_suite->scripts) . "\n";
  }
}

add_action("wp_head", 'edge_suite_header');


/**
 * Theme callback to retrieve the rendered stage. The composition gets rendered
 * in the init phase. Scripts will be placed through edge_suite_header.
 * @return string
 *   Returns the rendered stage.
 */
function edge_suite_view() {
  global $edge_suite;
  return $edge_suite->stage;
}

/** MENU **/

function edge_suite_menu() {
  // Todo: Create icon.
  // $icon_url = plugins_url('/edge-suite').'/admin/edge_icon.png';
  $icon_url = '';
  add_menu_page('Edge Suite', 'Edge Suite', 'edge_suite_administer', __FILE__, 'edge_suite_menu_main', $icon_url);
  add_submenu_page(__FILE__, 'Manage', 'Manage', 'edge_suite_administer', __FILE__, 'edge_suite_menu_main');
  add_submenu_page(__FILE__, 'Settings', 'Settings', 'edge_suite_administer', 'edge_suite_menu_settings', 'edge_suite_menu_settings');
  add_submenu_page(__FILE__, 'Usage', 'Usage', 'edge_suite_administer', 'edge_suite_menu_usage', 'edge_suite_menu_usage');
}

add_action('admin_menu', 'edge_suite_menu');

function edge_suite_menu_main() {
  include('admin/manage.php');
}

function edge_suite_menu_settings() {
  include('admin/options.php');
}

function edge_suite_menu_usage() {
  include('admin/usage.php');
}

/** CAPABILITIES **/
function edge_suite_map_meta_cap($caps, $cap, $user_id, $args) {
  $meta_caps = array(
    'edge_suite_administer' => 'manage_options',
    'edge_suite_select_composition' => 'publish_pages',
  );

  $caps = array_diff($caps, array_keys($meta_caps));

  if (isset($meta_caps[$cap])) {
    $caps[] = $meta_caps[$cap];
  }

  return $caps;
}

add_filter('map_meta_cap', 'edge_suite_map_meta_cap', 10, 4);

/** COMPOSITION BY PAGE/POST **/

/**
 * Adds a select box to posts/pages to be able to choose a composition that will
 * appear on the page.
 */
function edge_suite_add_box() {
  if (current_user_can('edge_suite_select_composition')) {
    add_meta_box('edge_suite_composition_selection', 'Edge Suite', 'edge_suite_reference_form', 'post', 'advanced', 'high');
    add_meta_box('edge_suite_composition_selection', 'Edge Suite', 'edge_suite_reference_form', 'page', 'advanced', 'high');
  }
}

add_action('admin_menu', 'edge_suite_add_box');


/**
 * Callback for post_save. It's being checked if a composition was selected for
 * the corresponding page/post within the edge_suite_box and the id of the
 * composition will be saved with it.
 * @param $id
 *   Id of the post/page
 */
function edge_suite_save_post_reference($id) {
  if (current_user_can('edge_suite_select_composition')) {
    $definition_id = intval($_POST['edge_suite_composition']);
    if ($definition_id != 0) {
      //    add_post_meta($id, '_edge_composition', $definition_id, true) ||
      update_post_meta($id, '_edge_composition', $definition_id);
    }
    else {
      delete_post_meta($id, '_edge_composition');
    }
  }
}

add_action('save_post', 'edge_suite_save_post_reference');


/**
 * Meta box callback
 */
function edge_suite_reference_form() {
  global $post;
  $selected = get_post_meta($post->ID, '_edge_composition', TRUE);
  $select_form = edge_suite_comp_select_form('edge_suite_composition', $selected);

  $form = $select_form;
  $form .= '<p class="description">Choose an Edge composition for the page.
  Compositions can be uploaded through the <a href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">Edge Suite Management page</a>.
  Check the <a href="/wp-admin/admin.php?page=edge_suite_menu_usage">usage page</a> for further instructions.</p>';

  echo $form;
}


/*** FORM HELPER FUNCTIONS ***/

/**
 * Returns a select form element with all available compositions keyed by composition id.
 *
 * @param $select_form_id
 *  Form name and id
 * @param string $selected
 *   Key that gets selected.
 * @param bool $default_option
 *   If set to true the option 'default' will be added.
 * @param bool $none_option
 *   If set to true the option 'none' will be added.
 *
 * @return string
 */
function edge_suite_comp_select_form($select_form_id, $selected, $default_option = TRUE, $none_option = TRUE) {
  global $wpdb;

  if (empty($selected)) {
    $selected = 0;
  }

  // Get all compositions.
  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  $definitions = $wpdb->get_results('SELECT * FROM ' . $table_name);
  $options = array();
  foreach ($definitions as $definition) {
    $options[$definition->definition_id] = $definition->definition_id . ' - ' . $definition->project_name . ' ' . $definition->composition_id;
  }

  $form = '';
  $form .= '<select name="' . $select_form_id . '" id="' . $select_form_id . '">' . "\n";

  $options_default = array();
  if ($none_option) {
    $options_default['-1'] = 'None';
  }
  if ($default_option) {
    $options_default['0'] = 'Default';
  }

  $options_default += $options;
  foreach ($options_default as $key => $value) {
    $form .= '<option value="' . $key . '" ' . ($selected == $key ? 'selected' : '') . '>' . $value . '</option>' . "\n";
  }
  $form .= '</select>' . "\n";

  return $form;
}

/**
 * Shortcode implementation for 'edge_animation'
 */
function edge_suite_shortcode_edge_animation( $atts ) {
  $id = -1;
  extract( shortcode_atts( array(
    'id' => '-1',
    'left' => null,
    'top' => null,
  ), $atts ) );

  $styles = '';

  // Add left position offset to style.
  if(isset($left)){
    if($left == 'auto'){
      $styles .= 'margin: 0px auto;';
    }
    else if(intval($left) != 0){
      $styles .= 'margin-left:' . $left . 'px;';
    }
  }

  //Add top position offset to style.
  if(isset($top) && intval($top) != 0){
    $styles .= 'margin-top:' . $top . 'px;';
  }

  $definition_id = $id;
  $definition_res = edge_suite_comp_render($definition_id, $styles);

  $stage = $scripts = '';

  if($definition_res != NULL){
    // Todo: Placing scripts inline in the content really isn't pretty, but so far there
    // doesn't seem to be an easy way around this, otherwise we need to handle shortcode
    // before header fuctions get called.
    $scripts = implode("\n", $definition_res['scripts']);

    $stage = $definition_res['stage'];
  }

  return "\n" . $scripts . "\n" . $stage ."\n";

}
add_shortcode('edge_animation', 'edge_suite_shortcode_edge_animation');

// Enable widget shortcut support if configured
if(get_option('edge_suite_widget_shortcode') == 1){
  add_filter('widget_text', 'do_shortcode');
}