<?php


function file_scan_directory($dir, $pattern) {
  $files = list_files($dir);
  $matching_files = array();
  foreach ($files as $file) {
    if (preg_match($pattern, $file)) {
      $matching_files[] = $file;
    }
  }
  return $matching_files;
}

function rmdir_recursive($path) {
  global $wp_filesystem;
  $wp_filesystem->rmdir($path, TRUE);
}


function check_plain($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


function set_message($msg, $type = 'status  ') {
  global $edge_suite;
  $edge_suite->msg[] = $msg;
}

function get_messages() {
  global $edge_suite;
  return "\n" . implode("</br>\n", $edge_suite->msg) . "\n";
}


function file_delete($file) {
  unlink($file);
}

function t($string, array $args = array()) {
  if (empty($args)) {
    return $string;
  }
  else {
    return format_string($string, $args);
  }
}

function format_string($string, array $args = array()) {
  // Transform arguments before inserting them.
  foreach ($args as $key => $value) {
    switch ($key[0]) {
      case '@':
        // Escaped only.
        $args[$key] = check_plain($value);
        break;

      case '%':
      default:
        // Escaped and placeholder.
        $args[$key] = '<em class="placeholder">' . check_plain($value) . '</em>';
        break;

      case '!':
        // Pass-through.
    }
  }
  return strtr($string, $args);
}


function update_record($table, $values, $key) {
  global $wpdb;
  $table_name = $wpdb->prefix . $table;
  $wpdb->update($table_name, $values, array($key => $values[$key]));
}

function write_record($table, $values) {
  global $wpdb;
  $table_name = $wpdb->prefix . $table;
  $wpdb->insert($table_name, $values);
  return $wpdb->insert_id;
}
