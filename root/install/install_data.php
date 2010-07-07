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
	// Version Alpha 1
	'1.2.0-A1'	=> array(
		// The main Subject Prefix table
		array(SUBJECT_PREFIX_TABLE, array(
			'COLUMNS' => array(
				'prefix_id'		=> array('UINT', NULL, 'auto_increment'),
				'prefix_title'	=> array('VCHAR:255', ''),
				'prefix_colour'	=> array('VCHAR:6', '000000'),
			),

			'PRIMARY_KEY' => 'prefix_id',
		)),
	),
);
