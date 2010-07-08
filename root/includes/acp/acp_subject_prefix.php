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
		$action	= request_var('action', '');

		// Quick actions
		if (method_exists($this, 'qa_' . $action))
		{
			call_user_func($this, 'qa_' . $action);
		}

		$data = $forums = array();
		$sql_ary = array(
			'SELECT'	=> 'f.forum_id, f.forum_name, sp.*, spt.prefix_order',
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
			'ORDER_BY'	=> 'spt.prefix_order',
		);
		$result	= subjectprefix\sp_phpbb::$db->sql_query(subjectprefix\sp_phpbb::$db->sql_build_query('SELECT', $sql_ary), time());
		while ($row = subjectprefix\sp_phpbb::$db->sql_fetchrow($result))
		{
			if (!isset($data[$row['forum_id']]))
			{
				$data[$row['forum_id']]		= array();
				$forums[$row['forum_id']]	= $row['forum_name'];
			}

			$data[$row['forum_id']][] = array(
				'prefix_id'		=> $row['prefix_id'],
				'prefix_title'	=> $row['prefix_title'],
				'prefix_colour'	=> $row['prefix_colour'],
				'prefix_order'	=> $row['prefix_order'],
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

					// Actions
					'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;pid=' . $prefix['prefix_id'],
					'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move&amp;direction=down&amp;prefix_order=' . $prefix['prefix_order'] . '&amp;f=' . $forum_id,
					'U_MOVE_UP'		=> $this->u_action . '&amp;action=move&amp;direction=up&amp;prefix_order=' . $prefix['prefix_order'] . '&amp;f=' . $forum_id,
				));
			}
		}
	}

	/**
	 * Delete a prefix
	 * @return void
	 */
	private function qa_delete()
	{
		$pid = request_var('pid', 0);
		subjectprefix\sp_phpbb::$db->sql_query('DELETE FROM ' . SUBJECT_PREFIX_TABLE . ' WHERE prefix_id = ' . $pid);
		subjectprefix\sp_phpbb::$db->sql_query('DELETE FROM ' . SUBJECT_PREFIX_FORUMS_TABLE . ' WHERE prefix_id = ' . $pid);
	}

	/**
	 * Reorder the prefixes
	 * @return void
	 */
	private function aq_move()
	{
		$direction	 = ($_GET['direction'] == 'down') ? 'down' : 'up';
		$field_order = request_var('prefix_order', 0);
		$fid		 = request_var('f', 0);
		$order_total = $field_order * 2 + (($direction == 'up') ? -1 : 1);

		$sql = 'UPDATE ' . SUBJECT_PREFIX_FORUMS_TABLE . "
			SET prefix_order = $order_total - prefix_order
			WHERE prefix_order IN ($field_order, " . (($direction == 'up') ? $field_order - 1 : $field_order + 1) . ')
				AND forum_id = ' . $fid;
		subjectprefix\sp_phpbb::$db->sql_query($sql);
	}
}
