<?php
/**
*
* @author Erik Frèrejean (erikfrerejean@phpbb.com) http://www.erikfrerejean.nl
*
* @package mcp
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
* @package mcp
*/
class mcp_subject_prefix
{
	/**
	* Main method, is called by p_master to run the module
	*/
	public function main($mode, $id)
	{
		global $db, $user;
		global $phpEx;

		if (!check_form_key('posting'))
		{
			trigger_error($user->lang['FORM_INVALID']);
		}

		// Fetch all the data
		$fid	= request_var('f', 0);
		$pid	= request_var('prefixid', 0);
		$red	= request_var('redirect', 'index.' . $phpEx);
		$tid	= request_var('t', 0);

		// Prefix didn't change
		$sql = 'SELECT subject_prefix_id
			FROM ' . TOPICS_TABLE . '
			WHERE topic_id = ' . $tid . '
				AND subject_prefix_id = ' . $pid;
		$result = $db->sql_query_limit($sql, 1);
		$test	= $db->sql_fetchfield('subject_prefix_id', false, $result);
		$db->sql_freeresult($result);

		if ($test)
		{
			return;
		}

		// Get the available prefixes for this forum
		$prefixlist = subject_prefix_core::$sp_cache->obtain_prefix_forum_list($fid);

		// Possible?
		if ($pid > 0 && !in_array($pid, $prefixlist))
		{
			trigger_error('PREFIX_NOT_ALLOWED');
		}

		// Change the prefix
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET subject_prefix_id = ' . $pid . '
			WHERE topic_id = ' . $tid;
		$db->sql_query($sql);
		if ($db->sql_affectedrows() == -1)
		{
			trigger_error('PREFIX_UPDATE_FAILED');
		}
		else
		{
			$redirect = reapply_sid($red);
			meta_refresh(2, $redirect);
			trigger_error($user->lang['PREFIX_UPDATE_SUCCESSFULL'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect . '">', '</a>'));
		}
	}
}