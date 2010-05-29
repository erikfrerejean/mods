<?php
/**
*
* @author Erik Frèrejean (erikfrerejean@phpbb.com) http://www.erikfrerejean.nl
*
* @package phpBB3
* @copyright (c) 2010 Erik Frèrejean
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Minimum Requirement: PHP 5.1.0
*/

/**
* @ignore
*/
if (version_compare(PHP_VERSION, '5.1.0', '<'))
{
	die ("Subject Prefix requires at least php 5.1.0 to run!.<br />You are running php: " . PHP_VERSION);
}

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

if (!file_exists($phpbb_root_path . 'umil/umil.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// We only allow a founder to install this MOD
if ($user->data['user_type'] != USER_FOUNDER)
{
	if ($user->data['user_id'] == ANONYMOUS)
	{
		login_box('', 'LOGIN');
	}
	trigger_error('NOT_AUTHORISED');
}

if (!class_exists('umil'))
{
	include($phpbb_root_path . 'umil/umil.' . $phpEx);
}

$umil = new umil(true);

$mod = array(
	'name'		=> 'Subject Prefix',
	'version'	=> '1.0.0-rc1',
	'config'	=> 'subjectprefix_version',
	'enable'	=> 'subjectprefix_enable',
);

if (confirm_box(true))
{
	// Install the base 1.0.0-rc1 version
	if (!$umil->config_exists($mod['config']))
	{
		// Lets add a config setting for enabling/disabling the MOD and set it to true
		$umil->config_add($mod['enable'], true);

		// We must handle the version number ourselves.
		$umil->config_add($mod['config'], $mod['version']);

		$umil->permission_add(array(
			array('a_subject_prefix', 1),
			array('m_subject_prefix', 0),
			array('u_subject_prefix', 0),
		));

		$umil->permission_set(array(
			array('ROLE_ADMIN_FULL', 'a_subject_prefix'),
			array('ROLE_MOD_STANDARD', 'm_subject_prefix'),
			array('ROLE_MOD_FULL', 'm_subject_prefix'),
			array('ROLE_USER_FULL', 'u_subject_prefix'),
		));

		$umil->table_add(array(
			array(subject_prefix_core::SUBJECT_PREFIX_TABLE, array(
				'COLUMNS' => array(
					'prefix_id' => array('UINT', 0, 'auto_increment'),
					'prefix_title' => array('VCHAR:255', ''),
					'prefix_colour' => array('VCHAR:6', 000000),
				),

				'PRIMARY_KEY'	=> array('prefix_id', ''),

				'KEYS'		=> array(
					'prefix_id' => array('PRIMARY', array('prefix_id')),
				),
			)),

			array(subject_prefix_core::SUBJECT_PREFIX_FORUMS_TABLE, array(
				'COLUMNS' => array(
					'prefix_id' => array('UINT', 0),
					'forum_id' => array('UINT', 0),
				),

				'KEYS'		=> array(
					'prefix_id' => array('INDEX', array('prefix_id')),
					'forum_id' => array('INDEX', array('forum_id')),
				),
			)),
		));

		$umil->table_column_add('TOPICS_TABLE', 'subject_prefix_id', array('UINT', '0'));

		$umil->table_index_add('TOPICS_TABLE', 'topic_first_post_id', 'topic_first_post_id');

		$umil->table_index_add('TOPICS_TABLE', 'subject_prefix_id', 'subject_prefix_id');

		// Our final action, we purge the board cache
		$umil->cache_purge();
	}

	// We are done
	trigger_error('Done!');
}
else
{
	confirm_box(false, 'INSTALL_TEST_MOD');
}

// Shouldn't get here.
redirect($phpbb_root_path . $user->page['page_name']);