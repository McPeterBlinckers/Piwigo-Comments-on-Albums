<?php
/*
Plugin Name: Comments on Albums
Version: auto
Description: Activate comments on albums pages
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (mobile_theme())
{
  return;
}

global $prefixeTable;


define('COA_ID',      basename(dirname(__FILE__)));
define('COA_PATH' ,   PHPWG_PLUGINS_PATH . COA_ID . '/');
define('COA_TABLE' ,  $prefixeTable . 'comments_categories');
define('COA_ADMIN',   get_root_url().'admin.php?page=plugin-' . COA_ID);
define('COA_VERSION', 'auto');


add_event_handler('init', 'coa_init');

function coa_init()
{
  global $user, $conf, $pwg_loaded_plugins;

  include_once(COA_PATH . 'maintain.inc.php');
  $maintain = new Comments_on_Albums_maintain(COA_ID);
  $maintain->autoUpdate(COA_VERSION, 'install');

  // luciano doesn't use comments
  // incompatible with dynamic display of Stripped & Collumns
  if ($user['theme'] == 'luciano' or $user['theme'] == 'stripped_black_bloc') return;

  include_once(COA_PATH . 'include/events.inc.php');

  if (defined('IN_ADMIN'))
  {
    add_event_handler('tabsheet_before_select', 'coa_tabsheet_before_select', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
    add_event_handler('loc_begin_admin_page', 'coa_admin_intro');
  }
  else
  {
    add_event_handler('loc_after_page_header', 'coa_albums');
    add_event_handler('loc_end_comments', 'coa_comments');
  }

  add_event_handler('get_stuffs_modules', 'coa_register_stuffs_module');

  load_language('plugin.lang', COA_PATH);
}
