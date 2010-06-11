<?php
/**
*
* @author Erik Frèrejean (erikfrerejean@phpbb.com) http://www.erikfrerejean.nl
*
* @package phpBB3
* @copyright (c) 2010 Erik Frèrejean
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Minimum Requirement: PHP 5.1.0
*/

/**
* @ignore
*/
if (version_compare(PHP_VERSION, '5.1.0', '<'))
{
	die ("Subject Prefix requires at least php 5.1.0 to run!.<br />You are running php: " . PHP_VERSION);
}

define('UMIL_AUTO', true);
define('IN_PHPBB', true);
define('IN_INSTALL', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// Force the hook. Its possible that the file isn't loaded at this point
if (!function_exists('add_prefix_to_viewtopic'))
{
	include ($phpbb_root_path . 'includes/hooks/hook_subject_prefix.' . $phpEx);
}

// The name of the mod to be displayed during installation.
$mod_name = 'SUBJECTPREFIX';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'subject_prefix_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
$language_file = 'mods/subject_prefix/subject_prefix_common';

// Get version info
include($phpbb_root_path . 'install/install_versions.' . $phpEx);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

// Add demo prefix
$umil->db->sql_query('INSERT INTO ' . subject_prefix_core::SUBJECT_PREFIX_TABLE . "(prefix_id, prefix_title, prefix_colour) VALUES (NULL, 'LOC_PREFIX', '')");


// clear cache
cache_purge('', 0);
cache_purge('auth', 0);