<?php
/**
 *
 * @package Subject Prefix Installer
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

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	// Dev version, will be used as "main" schema
	'1.2.0-dev'	=> array(
		'table_add' => array(
			// The main Subject Prefix table
			array(SUBJECT_PREFIX_TABLE, array(
				'COLUMNS'	=> array(
					'prefix_id'		=> array('UINT', NULL, 'auto_increment'),
					'prefix_title'	=> array('VCHAR:255', ''),
					'prefix_colour'	=> array('VCHAR:6', '000000'),
				),
				'PRIMARY_KEY' => 'prefix_id',
			)),

			// The prefix-forum table
			array(SUBJECT_PREFIX_FORUMS_TABLE, array(
				'COLUMNS'	=> array(
					'prefix_id'		=> array('UINT', 0),
					'forum_id'		=> array('UINT', 0),
					'prefix_order'	=> array('UINT', 0),
				),
				'KEYS'		=> array(
					'pid'	=> array('INDEX', array('prefix_id')),
					'fid'	=> array('INDEX', array('forum_id')),
				)
			)),
		),

		// Throw the permissions in tha mix
		'permission_add'	=> array(
			array('a_subject_prefix', true),		// Main admin permission
			array('a_subject_prefix_create', true),	// Can create/edit prefixes

			array('m_subject_prefix'),				// MCP quick edit permission

			array('f_subject_prefix'),				// Can post prefixes
		),

		'permission_set'	=> array(
			// Admin roles
			array('ROLE_ADMIN_STANDARD', array(
				'a_subject_prefix',
				'a_subject_prefix_create',
			)),
			array('ROLE_ADMIN_FORUM', array(
				'a_subject_prefix',
				'a_subject_prefix_create',
			)),
			array('ROLE_ADMIN_FULL', array(
				'a_subject_prefix',
				'a_subject_prefix_create',
			)),

			// Moderator roles
			array('ROLE_MOD_FULL', array(
				'm_subject_prefix',
			)),
			array('ROLE_MOD_STANDARD', array(
				'm_subject_prefix',
			)),

			// User roles
			array('ROLE_FORUM_FULL', array(
				'f_subject_prefix',
			)),
			array('ROLE_FORUM_STANDARD', array(
				'f_subject_prefix',
			)),
			array('ROLE_FORUM_POLLS', array(
				'f_subject_prefix',
			)),
		)
	),
);
