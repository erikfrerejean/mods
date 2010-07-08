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
 * Subject Prefix module class
 * @package acp
 */
class acp_subject_prefix
{
	public $u_action;

	public function main($id, $mode)
	{
		// Set some stuff we *really* need
		$this->tpl_name = 'acp_subject_prefix';
		add_form_key('acp_subject_prefix');

		$data = $forums = array();
		$sql_ary = array(
			'SELECT'	=> 'f.forum_id, f.forum_name, sp.*',
			'FROM'		=> array(
				SUBJECT_PREFIX_TABLE		=> 'sp',
				SUBJECT_PREFIX_FORUMS_TABLE	=> 'spt',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(
						FORUMS_TABLE	=> 'f',
					),
					'ON'	=> 'f.forum_id = spt.forum_id',
				),
			),
			'WHERE'		=> 'spt.prefix_id = sp.prefix_id',
		);
		$result	= subjectprefix\sp_phpbb::$db->sql_query(subjectprefix\sp_phpbb::$db->sql_build_query('SELECT', $sql_ary));
		while ($row = subjectprefix\sp_phpbb::$db->sql_fetchrow($result))
		{
			if (!isset($data[$row['forum_id']]))
			{
				$data[$row['forum_id']]		= array();
				$forums[$row['forum_id']]	= $row['forum_name'];
			}

			$data[$row['forum_id']][] = array(
//				'forum_id'		=> $row['forum_id'],
//				'forum_name'	=> $row['forum_name'],
				'prefix_id'		=> $row['prefix_id'],
				'prefix_title'	=> $row['prefix_title'],
				'prefix_colour'	=> $row['prefix_colour'],
			);
		}
		subjectprefix\sp_phpbb::$db->sql_freeresult($result);

		// Output the list
		foreach ($data as $forum_id => $prefixes)
		{
			// The forum block
			subjectprefix\sp_phpbb::$template->assign_block_vars('forumrow', array(
				'FORUMNAME'	=> $forums[$forum_id],
			));

			// The prefixes
			foreach ($prefixes as $prefix)
			{
				subjectprefix\sp_phpbb::$template->assign_block_vars('forumrow.prefixrow', array(
					'PREFIX_ID'		=> $prefix['prefix_id'],
					'PREFIX_NAME'	=> $prefix['prefix_title'],
					'PREFIX_COLOUR'	=> $prefix['prefix_colour'],
				));
			}
		}
	}
}
