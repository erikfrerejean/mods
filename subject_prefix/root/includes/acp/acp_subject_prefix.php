<?php
/**
*
* @author Erik Frèrejean (erikfrerejean@phpbb.com) http://www.erikfrerejean.nl
*
* @package acp
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
* @package acp
*/
class acp_subject_prefix
{
	/**
	* @var string Action, is filled by p_master
	*/
	public $u_action = '';

	/**
	* @var string form_key
	*/
	private $form_key = 'acp_subject_prefix';

	/**
	* Main method, is called by p_master to run the module
	*/
	public function main($mode, $id)
	{
		global $db, $template, $user;
		global $phpbb_admin_path, $phpEx;

		// Prep template
		$this->tpl_name = 'acp_subject_prefix';
		$this->page_title = 'ACP_SUBJECT_PREFIX';
		add_form_key($this->form_key);

		// Get some vars
		$action		= request_var('action', '');
		$prefix_id	= request_var('prefix_id', 0);

		// Handle actions
		switch ($action)
		{
			// Build the add and edit pages
			case 'add'	:
			case 'edit'	:
				$list = subject_prefix_core::$sp_cache->obtain_prefix_list();

				$template->assign_vars(array(
					'S_EDIT'	=> true,

					'U_SWATCH'	=> append_sid($phpbb_admin_path . 'swatch.' . $phpEx, array('form' => 'acp_subject_prefix', 'name' => 'prefix_colour')),

					'COLOUR'	=> (isset($list[$prefix_id])) ? $list[$prefix_id]['colour'] : '',
					'TITLE'		=> (isset($list[$prefix_id])) ? $list[$prefix_id]['title'] : '',
				));

				return;
			break;

			// Delete a prefix
			case 'delete' :
				if (!$prefix_id)
				{
					trigger_error($user->lang['MUST_SELECT_PREFIX'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$error = '';

					$db->sql_transaction('begin');

					// Update all topics that use this prefix
					$sql = 'UPDATE ' . TOPICS_TABLE . '
						SET subject_prefix_id = 0
						WHERE subject_prefix_id = ' . $prefix_id;
					$db->sql_query($sql);

					// Remove the prefix
					$sql = 'DELETE FROM ' . subject_prefix_core::SUBJECT_PREFIX_TABLE . '
						WHERE prefix_id = ' . $prefix_id;
					$db->sql_query($sql);

					// Commit
					$db->sql_transaction('commit');

					// Remove the cache
					subject_prefix_core::$sp_cache->destroy('_subject_prefix');
				}
				else
				{
					confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'prefix_id'	=> $prefix_id,
						'action'	=> 'delete',
					)));
				}
			break;

			// Save a prefix
			case 'save' :
				if (!check_form_key($this->form_key))
				{
					trigger_error($user->lang['FORM_INVALID']. adm_back_link($this->u_action), E_USER_WARNING);
				}

				$list = subject_prefix_core::$sp_cache->obtain_prefix_list();
				$prefix_colour	= request_var('prefix_colour', '');
				$prefix_title	= utf8_normalize_nfc(request_var('prefix_title', '', true));

				if (!$prefix_title)
				{
					trigger_error($user->lang['NO_PREFIX_TITLE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				// If there isn't a prefix with this ID, just add it
				if (!isset($list[$prefix_id]))
				{
					$prefix_id = 0;
				}

				$data_ary = array(
					'prefix_title'	=> $prefix_title,
					'prefix_colour'	=> $prefix_colour,
				);

				if (empty($prefix_id))
				{
					$db->sql_query('INSERT INTO ' . subject_prefix_core::SUBJECT_PREFIX_TABLE . ' ' . $db->sql_build_array('INSERT', $data_ary));
					$message = 'PREFIX_ADDED';
				}
				else
				{
					$db->sql_query('UPDATE ' . subject_prefix_core::SUBJECT_PREFIX_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data_ary) . '
						WHERE prefix_id = ' . $prefix_id);
					$message = 'PREFIX_UPDATED';
				}

				// Update the cache
				subject_prefix_core::$sp_cache->destroy('_subject_prefix');

				trigger_error($user->lang($message) . adm_back_link($this->u_action));
			break;
		}

		// Create an overview of all prefixes there are available
		$list = subject_prefix_core::$sp_cache->obtain_prefix_list();
		if (!empty($list))
		{
			foreach($list as $prefix_id => $prefix)
			{
				$template->assign_block_vars('prefixlist', array(
					'PREFIX_COLOUR'	=> $prefix['colour'],
					'PREFIX_TITLE' 	=> $prefix['title'],

					'U_DELETE'	=> $this->u_action . '&amp;action=delete&amp;prefix_id=' . $prefix_id,
					'U_EDIT'	=> $this->u_action . '&amp;action=edit&amp;prefix_id=' . $prefix_id,
				));
			}
		}

		// Assign all remaining stuff
		$template->assign_vars(array(
			'U_ACTION'	=> $this->u_action . ((empty($action)) ? '&amp;action=add' : '&amp;action=' . $action),
		));
	}
}