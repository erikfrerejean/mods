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
		$phpbb_hook->register('phpbb_user_session_handler', 'sp_hook::subject_prefix_init');
		$phpbb_hook->register(array('template', 'display'), 'sp_hook::add_subject_prefix_to_page');
		$phpbb_hook->register(array('template', 'display'), 'sp_hook::subject_prefix_template_hook');
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
	 * A hook that adds the subject prefixes to phpBB pages without modifying the page itself
	 * @param	phpbb_hook	$phpbb_hook	The phpBB hook object
	 * @return	void
	 */
	static public function add_subject_prefix_to_page(&$hook)
	{
		// Only on regular pages
		if (!empty(sp_phpbb::$user->page['page_dir']))
		{
			return;
		}

		// Add the prefix to certain pages
		switch (sp_phpbb::$user->page['page_name'])
		{
			case 'index.' . PHP_EXT :
				// To fetch the subject prefixes we'll need the last post ids
				$last_post_ids = array();
				foreach (sp_phpbb::$template->_tpldata['forumrow'] as $row => $data)
				{
					// Need the last post link
					if (empty($data['U_LAST_POST']))
					{
						continue;
					}

					$last_post_ids[$row] = substr(strrchr($data['U_LAST_POST'], 'p'), 1);
				}

				// Get the prefixes
				$sql = 'SELECT topic_last_post_id, subject_prefix_id
					FROM ' . TOPICS_TABLE . '
					WHERE ' . sp_phpbb::$db->sql_in_set('topic_last_post_id', $last_post_ids);
				$result	= sp_phpbb::$db->sql_query($sql);
				$last_post_ids = array_flip($last_post_ids);
				while ($row = sp_phpbb::$db->sql_fetchrow($result))
				{
					$last_post_subject = sp_core::generate_prefix_string($row['subject_prefix_id']) . ' ' . sp_phpbb::$template->_tpldata['forumrow'][$last_post_ids[$row['topic_last_post_id']]]['LAST_POST_SUBJECT'];

					// Alter the array
					sp_phpbb::$template->alter_block_array('forumrow', array(
						'LAST_POST_SUBJECT' => $last_post_subject,
					), $key = $last_post_ids[$row['topic_last_post_id']], 'change');
				}
				sp_phpbb::$db->sql_freeresult($result);
			break;

			case 'search.' . PHP_EXT :
				if (!isset(sp_phpbb::$template->_tpldata['searchresults']))
				{
					return;
				}

				foreach (sp_phpbb::$template->_tpldata['searchresults'] as $row => $data)
				{
					$topic_title = sp_core::generate_prefix_string($data['TOPIC_ID']) . ' ' . $data['TOPIC_TITLE'];

					sp_phpbb::$template->alter_block_array('searchresults', array(
						'TOPIC_TITLE'	=> $topic_title,
					), $row, 'change');
				}
			break;

			case 'viewforum.' . PHP_EXT :
				// As the topic data is unset once its used we'll have to introduce an query to
				// fetch the prefixes
				if (empty(sp_phpbb::$template->_tpldata['topicrow']))
				{
					return;
				}

				$topic_ids_rows = array();
				foreach (sp_phpbb::$template->_tpldata['topicrow'] as $row => $data)
				{
					$topic_ids_rows[$row] = $data['TOPIC_ID'];
				}

				$sql = 'SELECT topic_id, subject_prefix_id
					FROM ' . TOPICS_TABLE . '
					WHERE ' . sp_phpbb::$db->sql_in_set('topic_id', $topic_ids_rows) . '
						AND subject_prefix_id > 0';
				$result = sp_phpbb::$db->sql_query($sql);
				$topic_ids_rows = array_flip($topic_ids_rows);
				while ($row = sp_phpbb::$db->sql_fetchrow($result))
				{
					$topic_title = sp_core::generate_prefix_string($row['subject_prefix_id']) . ' ' . sp_phpbb::$template->_tpldata['topicrow'][$topic_ids_rows[$row['topic_id']]]['TOPIC_TITLE'];

					// Alter the array
					sp_phpbb::$template->alter_block_array('topicrow', array(
						'TOPIC_TITLE' => $topic_title,
					), $key = $topic_ids_rows[$row['topic_id']], 'change');
				}
				sp_phpbb::$db->sql_freeresult($result);
			break;

			case 'viewtopic.' . PHP_EXT :
				global $topic_data;

				// Add to the page title
				$page_title = sp_phpbb::$template->_tpldata['.'][0]['PAGE_TITLE'];
				$page_title = substr_replace($page_title, ' ' . sp_core::generate_prefix_string($topic_data['subject_prefix_id'], false), strpos($page_title, '-') + 1, 0);
				sp_phpbb::$template->assign_var('PAGE_TITLE', $page_title);

				// Add to the topic title
				$topic_title = sp_phpbb::$template->_tpldata['.'][0]['TOPIC_TITLE'];
				$topic_title = sp_core::generate_prefix_string($topic_data['subject_prefix_id']) . ' ' . $topic_title;
				sp_phpbb::$template->assign_var('TOPIC_TITLE', $topic_title);
			break;
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
		switch (sp_phpbb::$user->page['page_name'])
		{
			// Add the prefix dropdown to the posting page
			case 'posting.' . PHP_EXT :
				global $forum_id, $post_id, $topic_id;
				global $mode;

				// Must habs perms
				if (sp_phpbb::$auth->acl_get('!f_subject_prefix', $forum_id))
				{
					return;
				}

				// When editing we only pass this point when the *first* post is edited
				$selected = false;
				$sql = 'SELECT subject_prefix_id
					FROM ' . TOPICS_TABLE . "
					WHERE topic_id = $topic_id
						AND topic_first_post_id = $post_id";
				$result		= sp_phpbb::$db->sql_query($sql);
				$selected	= sp_phpbb::$db->sql_fetchfield('subject_prefix_id', false, $result);
				sp_phpbb::$db->sql_freeresult($result);

				// If submitted, change the selected prefix here
				if (isset($_POST['post']))
				{
					global $data;

					switch ($mode)
					{
						case 'edit' :
							if ($selected === false)
							{
								return;
							}

						// No Break;

						case 'post' :
							// Only have to add the prefix
							$pid = request_var('subjectprefix', 0);
							$sql = 'UPDATE ' . TOPICS_TABLE . '
								SET subject_prefix_id = ' . $pid . '
								WHERE topic_id = ' . $data['topic_id'];
							sp_phpbb::$db->sql_query($sql);

							// Done :)
							return;
						break;
					}
				}
				// Display the dropbox
				else
				{
					switch ($mode)
					{
						case 'edit' :
							if ($selected === false)
							{
								// Nope
								return;
							}

						// No Break;

						case 'post';
							sp_phpbb::$template->assign_vars(array(
								'S_SUBJECT_PREFIX_OPTIONS'	=> sp_core::generate_prefix_options($forum_id, $selected),
							));
						break;
					}
				}
			break;
		}
	}
}

// Register
sp_hook::register($phpbb_hook);
