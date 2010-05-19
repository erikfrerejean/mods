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
* The main Subject Prefix class
*/
abstract class subject_prefix_core
{
	/**
	* @var subject_prefix_cache The Subject Prefix cache object
	*/
	public static $sp_cache = null;

	/**
	* Initialise the MOD
	*/
	public static function init()
	{
		// Load the cache
		self::$sp_cache = new subject_prefix_cache();
	}

	/**
	* Add the prefix to the template.
	*
	* @param array	$topicdata	Array containing the topic data
	* @param string	$blockname	The name of the block into which the prefix has to be merges
	* 							the prefix will be added to the row that is last created.
	* @return void
	*/
	public static function add_subject_prefix_to_blockrow($topicdata, $blockname)
	{
		// Topic doesn't have a prefix
		if ($topicdata['subject_prefix_id'] == 0)
		{
			return;
		}

		// Get the prefixes
		$prefixlist = self::$sp_cache->obtain_prefix_list();

		if (!isset($prefixlist[$topicdata['subject_prefix_id']]))
		{
			return;
		}

		// We have a prefix, alter the block array and add it
		global $template;

		if ($blockname != '.')
		{
			$template->alter_block_array($blockname, array('SUBJECT_PREFIX' => $prefixlist[$topicdata['subject_prefix_id']]), true, 'change');
		}
		else
		{
			$template->assign_var('SUBJECT_PREFIX', $prefixlist[$topicdata['subject_prefix_id']]);
		}
	}
}