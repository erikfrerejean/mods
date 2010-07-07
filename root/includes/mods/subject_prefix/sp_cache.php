<?php
/**
 *
 * @package Subject Prefix
 * @copyright (c) 2010 Erik Frèrejean ( erikfrerejean@phpbb.com ) http://www.erikfrerejean.nl
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */
namespace subjectprefix;

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (!class_exists('acm'))
{
	require PHPBB_ROOT_PATH . 'includes/acm/acm_' . $acm_type . '.' . PHP_EXT;
}

/**
 * Class that is used to handle all Subject Prefix related caching
 */
class sp_cache extends \cache
{

}

// Drop the phpBB cache and overwrite it with the custom cache
sp_phpbb::$cache = null;
sp_phpbb::$cache = new sp_cache();
