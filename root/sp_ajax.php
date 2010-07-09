<?php
/**
 *
 * @package Subject Prefix
 * @copyright (c) 2010 Erik Frèrejean ( erikfrerejean@phpbb.com ) http://www.erikfrerejean.nl
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Permission check
if (!isset($user->data['session_admin']) || !$user->data['session_admin'])
{
	exit_handler();
}

// Get the table
$tablename	= request_var('tablename', '');
$tableid	= substr($tablename, 13);

// Fetch the posted list
$prefixlist = request_var($tablename, array(0 => ''));

// Run through the list
$sqls = array();
foreach ($prefixlist as $order => $prefix)
{
	// First one is the header, skip it
	if ($order == 0)
	{
		continue;
	}

	// Get the order nr
	$prefix = substr($prefix, 2);

	// Keep the header in mind
	$order = $order - 1;

	// Update in the db
	subjectprefix\sp_phpbb::$db->sql_query('UPDATE ' . SUBJECT_PREFIX_FORUMS_TABLE . ' SET prefix_order = ' . $order . ' WHERE prefix_id = ' . $prefix);
}

// Tell the template we're good ^^
if (subjectprefix\sp_phpbb::$db->sql_affectedrows() > 0)
{
	echo 'success';
}
exit_handler();