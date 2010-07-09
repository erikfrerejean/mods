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
 * Class that contains all hooked methods
 */
abstract class sp_hook
{
	/**
	 * Register all subject prefix hooks
	 * @param	phpbb_hook	$phpbb_hook	The phpBB hook object
	 * @return	void
	 */
	static public function register(&$phpbb_hook)
	{
		$phpbb_hook->register('phpbb_user_session_handler', 'subjectprefix\sp_hook::subject_prefix_init');
		$phpbb_hook->register(array('template', 'display'), 'subjectprefix\sp_hook::subject_prefix_template_hook');
	}

	/**
	 * A hook that is used to initialise the Subject Prefix core
	 * @param	phpbb_hook	$phpbb_hook	The phpBB hook object
	 * @return	void
	 */
	static public function subject_prefix_init(&$hook)
	{
		// Load the phpBB class
		if (!class_exists('sp_phpbb'))
		{
			global $phpbb_root_path, $phpEx;
			require($phpbb_root_path . 'includes/mods/subject_prefix/sp_phpbb.' . $phpEx);
			sp_phpbb::init();
		}

		// Load the Subject Prefix cache
		if (!class_exists('sp_cache'))
		{
			require PHPBB_ROOT_PATH . 'includes/mods/subject_prefix/sp_cache.' . PHP_EXT;
		}

		// Load the Subject Prefix core
		if (!class_exists('sp_core'))
		{
			require PHPBB_ROOT_PATH . 'includes/mods/subject_prefix/sp_core.' . PHP_EXT;
			sp_core::init();
		}
	}

	/**
	 * A hook that is used to change the behavior of phpBB just before the templates
	 * are displayed.
	 * @param	phpbb_hook	$phpbb_hook	The phpBB hook object
	 * @return	void
	 */
	static public function subject_prefix_template_hook(&$hook)
	{

	}
}

// Register
sp_hook::register($phpbb_hook);
