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

	/**
	* Get all prefixes that are allowed
	* @param Array $allowed Array with the allowed prefix ids
	*/
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

	/**
	* Get the current prefix of a topic
	* @param	int		$topic_id	The id of the topic
	* @param	int		$post_id	The id of a post in the topic
	* @return	int					The id of the prefix
	*/
	public function get_prefix($topic_id = 0, $post_id = 0)
	{
		global $db;

		// Empty call
		if ($topic_id == 0 && $post_id == 0)
		{
			return;
		}

		// If only a post id is given fetch the corresponding topic
		if ($topic_id == 0)
		{
			$sql = 'SELECT topic_id
				FROM ' . POSTS_TABLE . '
				WHERE post_id = ' . $post_id;
			$result		= $db->sql_query_limit($sql, 1);
			$topic_id	= $db->sql_fetchfield('topic_id', false, $result);
			$db->sql_freeresult($result);
		}

		$sql = 'SELECT subject_prefix_id
			FROM ' . TOPICS_TABLE . '
			WHERE topic_id = ' . $topic_id;
		$result	= $db->sql_query_limit($sql, 1);
		$selected_prefix = $db->sql_fetchfield('subject_prefix_id', false, $result);
		$db->sql_freeresult($result);

		return $selected_prefix;
	}

	public function make_prefix_select_options($fid, $selected)
	{
		// Any prefixes for this forum?
		$allowed = subject_prefix_core::$sp_cache->obtain_prefix_forum_list($fid);
		if (empty($allowed))
		{
			return array();
		}

		$prefixlist	= subject_prefix_core::get_prefixes($allowed);

		$options = array("<option value='0'" . (($selected == 0) ? " selected='selected'" : '') . ">{$user->lang('SELECT_A_PREFIX')}</option>");
		foreach ($prefixlist as $prefix)
		{
			$options[] = "<option value='{$prefix['id']}'" . ((!empty($prefix['colour'])) ? " style='color: #{$prefix['colour']};'" : '') . (($prefix['id'] == $selected) ? " selected='selected'" : '') . ">{$prefix['title']}</options>";
		}
		$options = implode('', $options);

		return $options;
	}
}