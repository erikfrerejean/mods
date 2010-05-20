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
	/**
	* @var Array Array containing all prefixes from the database
	*/
	private static $prefixlist = array();

	/**
	* Get all the prefixes from the database
	* @return Array Array containing all prefixes from the database
	*/
	public function obtain_prefix_list()
	{
		global $db;

		// Might be needed more than once
		if (!empty(self::$prefixlist))
		{
			return self::$prefixlist;
		}

		if ((self::$prefixlist = $this->get('_subject_prefix')) === false)
		{
			$sql = 'SELECT *
				FROM ' . subject_prefix_core::SUBJECT_PREFIX_TABLE;
			$result	= $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				self::$prefixlist[$row['prefix_id']] = array(
					'title'		=> $row['prefix_title'],
					'colour'	=> $row['prefix_colour'],
				);
			}
			$db->sql_query($sql);

			$this->put('_subject_prefix', self::$prefixlist);
		}

		return self::$prefixlist;
	}
}