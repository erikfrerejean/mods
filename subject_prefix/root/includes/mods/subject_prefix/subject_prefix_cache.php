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

if (!class_exists('acm'))
{
	require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
}

/**
* A class that handles all Subject Prefix cache actions
*/
class subject_prefix_cache extends acm
{

}