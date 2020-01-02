<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace FOF40\JoomlaAbstraction;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory as JoomlaFactory;

defined('_JEXEC') or die;

/**
 * A utility class to help you quickly clean the Joomla! cache
 */
class CacheCleaner
{
	/**
	 * Clears the com_modules and com_plugins cache. You need to call this whenever you alter the publish state or
	 * parameters of a module or plugin from your code.
	 *
	 * @return  void
	 */
	public static function clearPluginsAndModulesCache(): void
	{
		self::clearPluginsCache();
		self::clearModulesCache();
	}

	/**
	 * Clears the com_plugins cache. You need to call this whenever you alter the publish state or parameters of a
	 * plugin from your code.
	 *
	 * @return  void
	 */
	public static function clearPluginsCache(): void
	{
		self::clearCacheGroups(['com_plugins'], [0, 1]);
	}

	/**
	 * Clears the com_modules cache. You need to call this whenever you alter the publish state or parameters of a
	 * module from your code.
	 *
	 * @return  void
	 */
	public static function clearModulesCache(): void
	{
		self::clearCacheGroups(['com_modules'], [0, 1]);
	}

	/**
	 * Clears the specified cache groups.
	 *
	 * @param array $clearGroups      Which cache groups to clear. Usually this is com_yourcomponent to clear your
	 *                                component's cache.
	 * @param array $cacheClients     Which cache clients to clear. 0 is the back-end, 1 is the front-end. If you do not
	 *                                specify anything, both cache clients will be cleared.
	 *
	 * @return  void
	 */
	public static function clearCacheGroups(array $clearGroups, array $cacheClients = [0, 1]): void
	{
		$conf = JoomlaFactory::getConfig();

		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $client_id)
			{
				try
				{
					$options = [
						'defaultgroup' => $group,
						'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
					];

					$cache = Cache::getInstance('callback', $options);
					$cache->clean();
				}
				catch (\Exception $e)
				{
					// suck it up
				}
			}
		}
	}
}
