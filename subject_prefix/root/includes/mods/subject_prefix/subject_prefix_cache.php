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
	* @var Array Array that is used to store all prefix -> forum and forum -> prefix relationships
	*/
	static private $prefixforumlist = array(
		'fid'	=> array(),
		'pid'	=> array(),
	);

	/**
	* @var Array Array containing all prefixes from the database
	*/
	static private $prefixlist = array();

	/**
	* Get all the prefixes from the database
	* @return Array Array containing all prefixes from the database
	*/
	public function obtain_prefix_list()
	{
		if (empty(self::$prefixlist))
		{
			global $db;

			if ((self::$prefixlist = $this->get('_subject_prefix')) === false)
			{
				$sql = 'SELECT *
					FROM ' . SUBJECT_PREFIX_TABLE;
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
		}

		return self::$prefixlist;
	}

	/**
	* Get a list of consisting of all forums for a given prefix, or all
	* prefixes for a given forum.
	* @param	mixed	$fid	Forum id of which all prefixes will be returned
	* @param	mixed	$pid	Prefix id of which all forums will be returned
	* @return	array			The data array
	*/
	public function obtain_prefix_forum_list($fid = false, $pid = false)
	{
		// Both false?
		if ($fid === false && $pid === false)
		{
			return array();
		}

		// Where to look in the array
		$sub_ary		= ($fid !== false) ? 'fid' : 'pid';
		$sub_ary_key	= ($fid !== false) ? $fid : $pid;

		// Need to do a lookup?
		if (empty(self::$prefixforumlist[$sub_ary]))
		{
			global $db;

			if ((self::$prefixforumlist[$sub_ary] = $this->get('_subject_prefix_list_' . $sub_ary)) === false)
			{
				$resultkey		= ($fid !== false) ? 'forum_id' : 'prefix_id';
				$resultvalue	= ($fid !== false) ? 'prefix_id' : 'forum_id';

				$sql = 'SELECT *
					FROM ' . SUBJECT_PREFIX_FORUMS_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					if (!isset(self::$prefixforumlist[$sub_ary][$row[$resultkey]]))
					{
						self::$prefixforumlist[$sub_ary][$row[$resultkey]] = array();
					}

					self::$prefixforumlist[$sub_ary][$row[$resultkey]][] = $row[$resultvalue];
				}
				$db->sql_freeresult($result);

				$this->put('_subject_prefix_list_' . $sub_ary, self::$prefixforumlist[$sub_ary]);
			}
		}

		if (isset(self::$prefixforumlist[$sub_ary][$sub_ary_key]))
		{
			return self::$prefixforumlist[$sub_ary][$sub_ary_key];
		}
		else
		{
			return array();
		}
	}

	/**
	* Quick way to clear the whole subject_prefix cache
	*/
	public function destroy_all()
	{
		$this->destroy('_subject_prefix');
		$this->destroy('_subject_prefix_list_fid');
		$this->destroy('_subject_prefix_list_pid');
	}
}