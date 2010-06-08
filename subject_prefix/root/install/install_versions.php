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

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(

	// Version 1.0.0
	'1.0.0-RC1'	=> array(
		// Add permission settings
		'permission_add' => array(
			array('a_subject_prefix', 1),
			array('m_subject_prefix', 1),
			array('u_subject_prefix', 1),
		),

		// Add to some roles
		'permission_set' => array(
			array('ROLE_ADMIN_FULL', 'a_subject_prefix'),
			array('ROLE_MOD_STANDARD', 'm_subject_prefix'),
			array('ROLE_MOD_FULL', 'm_subject_prefix'),
			array('ROLE_USER_FULL', 'u_subject_prefix'),
		),

		// Add the modules
		'module_add' => array(
			// ACP Module
			array('acp', 'ACP_CAT_MODS', array(
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'module_langname'	=> 'ACP_SUBJECT_PREFIX',
				'module_auth'		=> 'acl_a_subject_prefix',
			)),
			array('acp', 'ACP_SUBJECT_PREFIX', array(
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'module_langname'	=> 'ACP_SUBJECT_PREFIX',

				'module_basename'	=> 'subject_prefix',
				'module_mode'		=> 'subject_prefix',
				'module_auth'		=> 'acl_a_subject_prefix',
			)),

			// MCP Module
			array('mcp', 'MCP_MAIN', array(
				'module_enabled'	=> 1,
				'module_display'	=> 0,
				'module_langname'	=> 'MCP_SUBJECT_PREFIX',

				'module_basename'	=> 'subject_prefix',
				'module_mode'		=> 'subject_prefix_qc',
				'module_auth'		=> 'acl_m_subject_prefix',
			)),
		),

		// Add tables
		'table_add'	=> array(
			array(subject_prefix_core::SUBJECT_PREFIX_TABLE, array(
				'COLUMNS' => array(
					'prefix_id'		=> array('UINT', 0, 'auto_increment'),
					'prefix_title'	=> array('VCHAR:255', ''),
					'prefix_colour'	=> array('VCHAR:6', 000000),
				),

				'PRIMARY_KEY' => array('prefix_id', ''),
			)),

			array(subject_prefix_core::SUBJECT_PREFIX_FORUMS_TABLE, array(
				'COLUMNS' => array(
					'prefix_id'	=> array('UINT', 0),
					'forum_id'	=> array('UINT', 0),
				),

				'KEYS' => array(
					'prefix_id' => array('INDEX', array('prefix_id')),
					'forum_id'	=> array('INDEX', array('forum_id')),
				),
			)),
		),

		// Add columns
		'table_column_add' => array(
			array('TOPICS_TABLE', 'subject_prefix_id', array('UINT', '0'))
		),

		// Add index
		'table_index_add' => array(
			array('TOPICS_TABLE', 'topic_first_post_id', 'topic_first_post_id'),
			array('TOPICS_TABLE', 'subject_prefix_id', 'subject_prefix_id'),
		),
	),
);
