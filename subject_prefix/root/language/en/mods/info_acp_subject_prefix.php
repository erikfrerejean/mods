<?php
/**
*
* info_acp_subject_prefix [English]
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

$lang = array_merge($lang, array(
	'ACP_MANAGE_SUBJECT_PREFIX'			=> 'Manage Subject Prefixes',
	'ACP_MANAGE_SUBJECT_PREFIX_EXPLAIN'	=> 'Using this form you can add, edit, view and delete prefixes.',
	'ACP_SUBJECT_PREFIX'				=> 'Subject Prefix',
	'ADD_PREFIX'						=> 'Add prefix',

	'MUST_SELECT_PREFIX'	=> 'You must select a prefix to delete!',

	'NO_PREFIX_TITLE'	=> 'You must provide a title for this prefix!',

	'PREFIX_ADDED'			=> 'The prefix was successfully added!',
	'PREFIX_COLOUR'			=> 'Prefix colour',
	'PREFIX_COLOUR_EXPLAIN'	=> 'Defines the colour this prefix will appear in. Leave blank for the default colour.',
	'PREFIX_TITLE'			=> 'Prefix title',
	'PREFIX_UPDATED'		=> 'The prefix was successfully updated!',

	'SELECT_A_PREFIX'	=> 'Select a topic prefix',
));