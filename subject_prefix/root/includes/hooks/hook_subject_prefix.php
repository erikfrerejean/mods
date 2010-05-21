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
			$sql = 'SELECT subject_prefix_id
				FROM ' . TOPICS_TABLE . '
				WHERE topic_first_post_id = ' . $post_id;
			$result	= $db->sql_query_limit($sql, 1);
			$selected_prefix = $db->sql_fetchfield('subject_prefix_id', false, $result);
			$db->sql_freeresult($result);

			if ($selected_prefix === false)
			{
				return;
			}
		}
		else
		{
			return;
		}
	}

	// Add lang file
	$user->add_lang('mods/info_acp_subject_prefix');

	// Any prefixes for this forum?
	$allowed	= subject_prefix_core::$sp_cache->obtain_prefix_forum_list($user->page['forum']);
	if (empty($allowed))
	{
		return;
	}

//	$prefixlist = subject_prefix_core::$sp_cache->obtain_prefix_list();
	$prefixlist	= subject_prefix_core::get_prefixes($allowed);

	// Build option list
	$options = array("<option value='0'" . (($selected_prefix == 0) ? " selected='selected'" : '') . ">{$user->lang('SELECT_A_PREFIX')}</option>");
	foreach ($prefixlist as $prefix)
	{
		$options[] = "<option value='{$prefix['id']}'" . ((!empty($prefix['colour'])) ? " style='color: #{$prefix['colour']};'" : '') . (($prefix['id'] == $selected_prefix) ? " selected='selected'" : '') . ">{$prefix['title']}</options>";
	}
	$options = implode('', $options);

	// Assign the list
	$template->assign_var('SUBJECT_PREFIX_DROPDOWN_OPTIONS', $options);
}

function add_prefix_to_viewtopic()
{
	global $user;
	global $phpEx;

	if ($user->page['page_name'] != 'viewtopic.' . $phpEx)
	{
		return;
	}

	global $topic_data;
	subject_prefix_core::add_subject_prefix_to_blockrow($topic_data, '.');
}

// Register all the hooks
$phpbb_hook->register('phpbb_user_session_handler', 'load_subject_prefix_files');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_dropdown_to_the_posting_page');
$phpbb_hook->register(array('template', 'display'), 'add_prefix_to_viewtopic');