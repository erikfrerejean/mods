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
		global $template;

		// Prep template
		$this->tpl_name = 'acp_subject_prefix';
		$this->page_title = 'ACP_SUBJECT_PREFIX';
		add_form_key($this->form_key);

		// Create an overview of all prefixes there are available
		$list = subject_prefix_core::$sp_cache->obtain_prefix_list();
		foreach($list as $prefix)
		{
			$template->assign_block_vars('prefixlist', array(
				'L_PREFIX_TITLE' => $prefix,
			));
		}
	}
}