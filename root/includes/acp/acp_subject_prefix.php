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
	}
}
