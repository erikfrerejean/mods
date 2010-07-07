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
	/**
	 * @var array All prefix data so it will only be fetched once
	 */
	private $subject_prefixes = array();

	/**
	 * Fetch the Subject Prefixes from the database
	 * @return	Array	All Subject Prefixes
	 */
	public function obtain_subject_prefixes()
	{
		if (!empty($this->subject_prefixes))
		{
			return $this->subject_prefixes;
		}

		// In cache?
		if (($this->subject_prefixes = $this->get('_subject_prefixes')) === false)
		{
			$sql = 'SELECT *
				FROM ' . SUBJECT_PREFIX_TABLE;
			$result = sp_phpbb::$db->sql_query($sql);
			while ($prefix = sp_phpbb::$db->sql_fetchrow($result))
			{
				$this->subject_prefixes[$prefix['prefix_id']] = array(
					'id'		=> $prefix['prefix_id'],
					'title'		=> $prefix['prefix_title'],
					'colour'	=> $prefix['prefix_colour'],
				);
			}
			sp_phpbb::$db->sql_freeresult($result);
		}

		return $this->subject_prefixes;
	}

	/**
	 * One method all gone
	 */
	static public function subject_prefix_quick_clear()
	{
		$this->destroy('_subject_prefixes');
	}
}

// Drop the phpBB cache and overwrite it with the custom cache
sp_phpbb::$cache = null;
sp_phpbb::$cache = new sp_cache();
