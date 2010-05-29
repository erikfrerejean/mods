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
* @param phpbb_hook $hook The phpBB hook object
* @return void
*/
function load_subject_prefix_files(&$hook)
{
	global $phpbb_root_path, $phpEx;

	// Include the core class
	if (!class_exists('subject_prefix_core'))
	{
		require($phpbb_root_path . 'includes/mods/subject_prefix/subject_prefix_core.' . $phpEx);
	}

	// Include the cache class
	if (!class_exists('subject_prefix_cache'))
	{
		require($phpbb_root_path . 'includes/mods/subject_prefix/subject_prefix_cache.' . $phpEx);
	}

	// Init
	subject_prefix_core::init();
}

/**
* A hook that hooks into template::display(). This hook will fill the prefix
* dropdown.
* @param phpbb_hook $hook The phpBB hook object
* @return void
*/
function add_prefix_dropdown_to_the_posting_page(&$hook)
{
	global $auth, $db, $template, $user;
	global $phpEx;

	// Only on the posting page!
	if ($user->page['page_name'] != 'posting.' . $phpEx)
	{
		return;
	}

	// User is allowed?
	if ($auth->acl_get('!u_subject_prefix', $user->page['forum']))
	{
		return;
	}

	// For the time being only when creating a new topic, or editing the first post
	// Might add prefixes for posts subjects later
	$selected_prefix = 0;
	if (strpos($user->page['query_string'], 'mode=post') === false)
	{
		// First post in the topic?
		if (strpos($user->page['query_string'], 'mode=edit') !== false)
		{
			$post_id = request_var('p', 0);
			$selected_prefix = subject_prefix_core::get_prefix(0, $post_id);
		}
		else
		{
			return;
		}
	}

	// Build option list
	$options = subject_prefix_core::make_prefix_select_options($user->page['forum'], $selected_prefix);
	if (empty($options))
	{
		return;
	}

	// Assign the list
	$template->assign_var('SUBJECT_PREFIX_DROPDOWN_OPTIONS', $options);
}

/**
* Load the prefix into viewtopic
* @param phpbb_hook $hook The phpBB hook object
* @return void
*/
function add_prefix_to_viewtopic()
{
	global $auth, $db, $user, $topic_data;
	global $phpbb_root_path, $phpEx;

	if ($user->page['page_name'] != 'viewtopic.' . $phpEx)
	{
		return;
	}

	// Only display to topic starter or those with the mod permission
	if ($user->data['user_id'] != $topic_data['topic_poster'] && $auth->acl_get('!m_subject_prefix'))
	{
		return;
	}

	global $forum_id, $topic_id, $viewtopic_url;
	global $template;
	subject_prefix_core::add_subject_prefix_to_blockrow($topic_data, '.');

	// Get the currently selected prefix
	$sql = 'SELECT subject_prefix_id
		FROM ' . TOPICS_TABLE . '
		WHERE topic_id = ' . $topic_id;
	$result	= $db->sql_query_limit($sql, 1);
	$selected_prefix = $db->sql_fetchfield('subject_prefix_id', false, $result);
	$db->sql_freeresult($result);

	$options = subject_prefix_core::make_prefix_select_options($forum_id, $selected_prefix);
	if (empty($options))
	{
		return;
	}

	// Throw the quickchange box in the mix
	$template->assign_vars(array(
		'S_SUBJECT_PREFIX_QUICK_CHANGE_OPTIONS'	=> $options,
		'U_SUBJECT_PREFIX_QUICK_CHANGE_ACTION'	=> append_sid($phpbb_root_path . 'mcp.' . $phpEx, array('i' => 'subject_prefix', 'mode' => 'subject_prefix_qc', 'f' => $forum_id, 't' => $topic_id, 'redirect' => urlencode(str_replace('&amp;', '&', $viewtopic_url))), true, $user->session_id),
	));
}

/**
* When moving topics the prefixes must be checked to see whether they are allowed
* in the new forum
* @param	phpbb_hook	The phpBB hook object
* @param	array		Array containing all forum ids that are being moved
* @param	integer		The destination forum id
* @return 	void
*/
function move_hook_function(&$hook, $topic_ids, $forum_id)
{
	global $db;

	// Do we move any prefixes to forums where they aren't allowed?
	$allowed = subject_prefix_core::$sp_cache->obtain_prefix_forum_list($forum_id);
	$current = array();
	$sql = 'SELECT topic_id, subject_prefix_id
		FROM ' . TOPICS_TABLE . '
		WHERE ' . $db->sql_in_set('topic_id', $topic_ids);
	$result = $db->sql_query($sql);
	while ($prefix = $db->sql_fetchrow($result))
	{
		$current['topic_id'] = $prefix['subject_prefix_id'];
	}
	$db->sql_freeresult($result);

	// Any prefixes in $current but not in $allowed must be changed
	$conflicted = array_diff($current, $allowed);
	$conflicted = array_unique($conflicted);

	if (empty($conflicted))
	{
		return;
	}

	// First remove the conflicted prefixes, we'll ask the user what to do with them later
	$sql = 'UPDATE ' . TOPICS_TABLE . '
		SET subject_prefix_id = ' . 0 . '
		WHERE ' . $db->sql_in_set('topic_id', $topic_ids) . '
			AND ' . $db->sql_in_set('subject_prefix_id', $conflicted);
	$db->sql_query($sql);

	/**
	* @todo In some future version add something that allows the user to define
	* 		which prefix he wants to use for the conflicting topics.
	* 		for now simply remove them
	*/
}

// Add our custom hooks
$phpbb_hook->add_hook('subject_prefix_move_hook');

// Register all the hooks
$phpbb_hook->register('phpbb_user_session_handler', 'load_subject_prefix_files');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_dropdown_to_the_posting_page');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_to_viewtopic');
$phpbb_hook->register('subject_prefix_move_hook', 'move_hook_function');