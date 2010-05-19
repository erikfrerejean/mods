<?php
/**
*
* @author Erik Frèrejean (erikfrerejean@phpbb.com) http://www.erikfrerejean.nl
*
* @package phpBB3
* @copyright (c) 2010 Erik Frèrejean
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* A hook to setup the subject prefix file while phpBB is loaded.
* By utalising the hook system no file edits have to be made for this ;)
*/
function load_subject_prefix_files(&$hook)
{
	global $phpbb_root_path, $phpEx;

	// Include the core class
	require($phpbb_root_path . 'includes/mods/subject_prefix/subject_prefix_core.' . $phpEx);

	// Include the cache class
	require($phpbb_root_path . 'includes/mods/subject_prefix/subject_prefix_cache.' . $phpEx);

	// Init
	subject_prefix_core::init();
}

$phpbb_hook->register('phpbb_user_session_handler', 'load_subject_prefix_files');