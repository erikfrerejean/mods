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
	/** @#+
	* Subject Prefix database tables
	*/
	const SUBJECT_PREFIX_TABLE 			= 'subject_prefix';
	const SUBJECT_PREFIX_FORUMS_TABLE	= 'subject_prefix_forums';
	/**@#-*/

	/**
	* @var subject_prefix_cache The Subject Prefix cache object
	*/
	public static $sp_cache = null;

	/**
	* Initialise the MOD
	*/
	public static function init()
	{
		global $user;

		// Load the cache
		self::$sp_cache = new subject_prefix_cache();

		// Include the language file
		$user->add_lang('mods/subject_prefix/subject_prefix_common');
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
			$template->alter_block_array($blockname, array(
				'SUBJECT_PREFIX_TITLE'	=> $prefixlist[$topicdata['subject_prefix_id']]['title'],
				'SUBJECT_PREFIX_COLOUR'	=> $prefixlist[$topicdata['subject_prefix_id']]['colour'],
			), true, 'change');
		}
		else
		{
			$template->assign_vars(array(
				'SUBJECT_PREFIX_TITLE'	=> $prefixlist[$topicdata['subject_prefix_id']]['title'],
				'SUBJECT_PREFIX_COLOUR'	=> $prefixlist[$topicdata['subject_prefix_id']]['colour'],
			));
		}
	}

	/**
	* Add the subject prefix data to the query that is build in submit_post().
	* @param	string	$post_mode	The current post mode
	* @param	array	$sql_ary	The sql data for this post
	* @return void
	*/
	public static function add_prefix_to_posting_sql($post_mode, &$sql_ary)
	{
		// Only when posting or editing the topic
		if ($post_mode != 'post' && $post_mode != 'edit_topic' && $post_mode != 'edit_first_post')
		{
			return;
		}

		// Is there a prefix chosen?
		$prefix_id = request_var('prefixes', 0);

		$prefixlist = self::$sp_cache->obtain_prefix_list();

		// Shouldn't be possible, but still
		if ($prefix_id > 0 && !isset($prefixlist[$prefix_id]))
		{
			return;
		}

		// Add the prefix to the $sql_ary
		$sql_ary[TOPICS_TABLE]['sql']['subject_prefix_id'] = $prefix_id;
	}

	public static function get_prefixes($allowed)
	{
		$all = self::$sp_cache->obtain_prefix_list();

		// remove all non allowed
		$list = array();
		foreach ($all as $prefix_id => $prefix)
		{
			if (!in_array($prefix_id, $allowed))
			{
				continue;
			}

			$list[] = $prefix;
		}

		return $list;
	}
}