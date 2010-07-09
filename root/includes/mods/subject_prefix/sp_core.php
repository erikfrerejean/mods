<?php
/**
 *
 * @package Subject Prefix
 * @copyright (c) 2010 Erik Frèrejean ( erikfrerejean@phpbb.com ) http://www.erikfrerejean.nl
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */
namespace subjectprefix;

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * The main Subject Prefix class
 */
abstract class sp_core
{
	static public function init()
	{
		// Define the database tables
		global $table_prefix;
		define('SUBJECT_PREFIX_TABLE', $table_prefix . 'subject_prefixes');
		define('SUBJECT_PREFIX_FORUMS_TABLE', $table_prefix . 'subject_prefix_forums');

		// We're going to need this data anyways, better to have the cache class fetch it now
		sp_phpbb::$cache->obtain_subject_prefixes();

		// Add some language files
		if (sp_phpbb::$user->page['page_dir'] == 'adm' && sp_phpbb::$user->page['page_name'] == 'index.' . PHP_EXT)
		{
			// Include the acp langauge file
			sp_phpbb::$user->add_lang('mods/subject_prefix/info_acp_subject_prefix');

			// Include the permissions file
			sp_phpbb::$user->add_lang('mods/subject_prefix/permissions_subject_prefix');
		}
	}
}
