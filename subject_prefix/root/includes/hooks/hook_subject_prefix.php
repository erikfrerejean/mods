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

/**
* A hook that hooks into template::display(). This hook will fill the prefix
* dropdown.
*/
function add_prefix_dropdown_to_the_posting_page(&$hook)
{
	global $db, $template, $user;
	global $phpEx;

	// Only on the posting page!
	if ($user->page['page_name'] != 'posting.' . $phpEx)
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
	if ($user->data['user_id'] != $topic_data['topic_poster'] && $auth->acl_get('!m_subject_prefix_qc'))
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

// Register all the hooks
$phpbb_hook->register('phpbb_user_session_handler', 'load_subject_prefix_files');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_dropdown_to_the_posting_page');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_to_viewtopic');