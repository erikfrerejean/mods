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
* @package module_install
*/
class acp_subject_prefix_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_subject_prefix',
			'title'		=> 'ACP_SUBJECT_PREFIX',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'subject_prefix'		=> array('title' => 'ACP_SUBJECT_PREFIX', 'auth' => 'acl_a_subject_prefix', 'cat' => array('ACP_SUBJECT_PREFIX')),
//				'subject_prefix_forums'	=> array('title' => 'ACP_SUBJECT_PREFIX_FORUMS', 'auth' => 'acl_a_subject_prefix', 'cat' => array('ACP_SUBJECT_PREFIX')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}