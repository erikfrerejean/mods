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
	* @var Array Array containing all prefixis for a given forum
	*/
	private static $forumprefixlist = array();

	/**
	* @var Array Array containing all forums for a given prefix
	*/
	private static $prefixforumlist = array();

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
					'id'		=> $row['prefix_id'],
					'title'		=> $row['prefix_title'],
					'colour'	=> $row['prefix_colour'],
				);
			}
			$db->sql_freeresult($result);

			$this->put('_subject_prefix', self::$prefixlist);
		}

		return self::$prefixlist;
	}

	/**
	* Get a list of all prefixes for a given forum
	* @param int $fid The forum id
	* @return Array The data array
	*/
	public function obtain_prefix_forum_list($fid = false)
	{
		global $db;

		if (!empty(self::$forumprefixlist))
		{
			if ($fid !== false)
			{
				return self::$forumprefixlist[$fid];
			}
			else
			{
				return self::$forumprefixlist;
			}
		}

		if ((self::$forumprefixlist = $this->get('_subject_prefix_forums')) === false)
		{
			$sql = 'SELECT *
				FROM ' . subject_prefix_core::SUBJECT_PREFIX_FORUMS_TABLE;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if (!isset(self::$forumprefixlist[$row['forum_id']]))
				{
					self::$forumprefixlist[$row['forum_id']] = array();
				}
				self::$forumprefixlist[$row['forum_id']][] = $row['prefix_id'];
			}
			$db->sql_freeresult($result);
		}

		if ($fid !== false)
		{
			return self::$forumprefixlist[$fid];
		}
		else
		{
			return self::$forumprefixlist;
		}
	}

	/**
	* Get a list of all prefixes for a given forum
	* @param int $fid The forum id
	* @return Array The data array
	*/
	public function obtain_forum_prefix_list($pid = false)
	{
		global $db;
		
		if (!empty(self::$prefixforumlist))
		{
			if ($pid !== false)
			{
				return self::$prefixforumlist[$pid];
			}
			else
			{
				return self::$prefixforumlist;
			}
		}

		if ((self::$prefixforumlist = $this->get('_subject_forums_prefix')) === false)
		{
			$sql = 'SELECT *
				FROM ' . subject_prefix_core::SUBJECT_PREFIX_FORUMS_TABLE;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if (!isset(self::$prefixforumlist[$row['prefix_id']]))
				{
					self::$prefixforumlist[$row['prefix_id']] = array();
				}

				self::$prefixforumlist[$row['prefix_id']][] = $row['forum_id'];
			}
			$db->sql_freeresult($result);

			$this->put('_subject_forums_prefix', self::$prefixforumlist);
		}

		if ($pid !== false)
		{
			return self::$prefixforumlist[$pid];
		}
		else
		{
			return self::$prefixforumlist;
		}
	}
}