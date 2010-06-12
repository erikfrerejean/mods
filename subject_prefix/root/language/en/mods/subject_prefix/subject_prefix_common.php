<?php
/**
*
* subject_prefix_common [English]
*
* @package language
* @copyright (c) 2010 Erik Frèrejean
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

/**
* Localise Prefixes.
* To localise a prefix change the name of a prefix to an UPPER CASE identifier
* and add an entry to the following array. Say you have a prefix with the name
* "LOC_PREFIX" which would be translated to "Localised prefix" the array would
* look like:
* <code>
* $lang = array_merge($lang, array(
* 	'SP_LOC_PREFIX'	=> 'Localised prefix',
* ));
* </code>
* Notice that the name is prefixed with "SP_" when added to this array!
*/
$lang = array_merge($lang, array(
	'SP_LOC_PREFIX'	=> 'Localised prefix',
));

/**
* Common language strings
*/
$lang = array_merge($lang, array(
	'PREFIX_UPDATE_FAILED'		=> 'Couldn\'t update the subject prefix!',
	'PREFIX_UPDATE_SUCCESSFULL'	=> 'Subject prefix updated successfully!',
));

/**
* info_acp_subject_prefix
*/
$lang = array_merge($lang, array(
	'ACP_MANAGE_SUBJECT_PREFIX'			=> 'Manage Subject Prefixes',
	'ACP_MANAGE_SUBJECT_PREFIX_EXPLAIN'	=> 'Using this form you can add, edit, view and delete prefixes.',
	'ACP_SUBJECT_PREFIX'				=> 'Subject Prefix',
	'ACP_SUBJECT_PREFIX_FORUMS'			=> 'Subject Prefixes per forum',
	'ADD_PREFIX'						=> 'Add prefix',

	'CONFIRM_PREFIX_DELETE'	=> 'Are you sure that you want to delete this subject prefix?',

	'MUST_SELECT_PREFIX'	=> 'You must select a prefix to delete!',

	'NO_PREFIX_TITLE'	=> 'You must provide a title for this prefix!',

	'PREFIX_ADDED'			=> 'The prefix was successfully added',
	'PREFIX_COLOUR'			=> 'Prefix colour',
	'PREFIX_COLOUR_EXPLAIN'	=> 'Defines the colour this prefix will appear in. Leave blank for the default colour.',
	'PREFIX_DELETED'		=> 'The prefix was successfully removed',
	'PREFIX_FORUMS'			=> 'Prefix forums',
	'PREFIX_FORUMS_EXPLAIN'	=> 'Select the forums in which this prefix can be used',
	'PREFIX_TITLE'			=> 'Prefix title',
	'PREFIX_UPDATED'		=> 'The prefix was successfully updated!',

	'SELECT_A_PREFIX'				=> 'Select a topic prefix',
	'SUBJECT_PREFIX_QUICK_CHANGE'	=> 'Change the topic prefix',
));

/**
* info_mcp_subject_prefix
*/
$lang = array_merge($lang, array(
	'MCP_SUBJECT_PREFIX'	=> 'Subject Prefix Quick Change',
));

/**
* permissions_subject_prefix
*/
// The ACP permissions
$lang = array_merge($lang, array(
	'acl_a_subject_prefix' => array('lang'	=> 'Can manage Subject Prefixes', 'cat' => 'misc'),
));

// The MCP permissions
$lang = array_merge($lang, array(
	'acl_m_subject_prefix' => array('lang' => 'Can use subject prefix quick change', 'cat' => 'topic_actions'),
));

// The User permissions
$lang = array_merge($lang, array(
	'acl_u_subject_prefix' => array('lang' => 'Can use Subject Prefixes', 'cat' => 'post'),
));

// The installer
$lang = array_merge($lang, array(
	'INSTALL_SUBJECTPREFIX'				=> 'Install Subject Prefix',
	'INSTALL_SUBJECTPREFIX_CONFIRM'		=> 'Do you really want to install Subject Prefix',
	'UPDATE_SUBJECTPREFIX'				=> 'Update Subject Prefix',
	'UPDATE_SUBJECTPREFIX_CONFIRM'		=> 'Do you really want to update Subject Prefix',
	'UNINSTALL_SUBJECTPREFIX'			=> 'Remove Subject Prefix',
	'UNINSTALL_SUBJECTPREFIX_CONFIRM'	=> 'Do you really want to remove Subject Prefix',
));