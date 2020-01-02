<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace FOF40\Platform\Base;

use FOF40\Container\Container;
use FOF40\Platform\FilesystemInterface;

defined('_JEXEC') or die;

abstract class Filesystem implements FilesystemInterface
{
	/** @var  Container  The component container */
	protected $container = null;

	/**
	 * Public constructor.
	 *
	 * @param \FOF40\Container\Container $c The component container
	 */
	public function __construct(Container $c)
	{
		$this->container = $c;
	}

	/**
	 * The list of paths where platform class files will be looked for
	 *
	 * @var  array
	 */
	protected static $paths = [];

	/**
	 * This method will crawl a starting directory and get all the valid files that will be analyzed by getInstance.
	 * Then it organizes them into an associative array.
	 *
	 * @param string $path          Folder where we should start looking
	 * @param array  $ignoreFolders Folder ignore list
	 * @param array  $ignoreFiles   File ignore list
	 *
	 * @return  array   Associative array, where the `fullpath` key contains the path to the file,
	 *                  and the `classname` key contains the name of the class
	 */
	protected static function getFiles(string $path, array $ignoreFolders = [], array $ignoreFiles = []): array
	{
		$return = [];

		$files = self::scanDirectory($path, $ignoreFolders, $ignoreFiles);

		// Ok, I got the files, now I have to organize them
		foreach ($files as $file)
		{
			$clean = str_replace($path, '', $file);
			$clean = trim(str_replace('\\', '/', $clean), '/');

			$parts = explode('/', $clean);

			// If I have less than 3 fragments, it means that the file was inside the generic folder
			// (interface + abstract) so I have to skip it
			if (count($parts) < 3)
			{
				continue;
			}

			$return[] = [
				'fullpath'  => $file,
				'classname' => 'F0FPlatform' . ucfirst($parts[0]) . ucfirst(basename($parts[1], '.php')),
			];
		}

		return $return;
	}

	/**
	 * Recursive function that will scan every directory unless it's in the ignore list. Files that aren't in the
	 * ignore list are returned.
	 *
	 * @param string $path          Folder where we should start looking
	 * @param array  $ignoreFolders Folder ignore list
	 * @param array  $ignoreFiles   File ignore list
	 *
	 * @return  array   List of all the files
	 */
	protected static function scanDirectory(string $path, array $ignoreFolders = [], array $ignoreFiles = []): array
	{
		$return = [];

		$handle = @opendir($path);

		if (!$handle)
		{
			return $return;
		}

		while (($file = readdir($handle)) !== false)
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}

			$fullpath = $path . '/' . $file;

			if ((is_dir($fullpath) && in_array($file, $ignoreFolders)) || (is_file($fullpath) && in_array($file, $ignoreFiles)))
			{
				continue;
			}

			if (is_dir($fullpath))
			{
				$return = array_merge(self::scanDirectory($fullpath, $ignoreFolders, $ignoreFiles), $return);
			}
			else
			{
				$return[] = $path . '/' . $file;
			}
		}

		return $return;
	}

	/**
	 * Gets the extension of a file name
	 *
	 * @param string $file The file name
	 *
	 * @return  string  The file extension
	 */
	public function getExt(string $file): string
	{
		$dot = strrpos($file, '.') + 1;

		return substr($file, $dot);
	}

	/**
	 * Strips the last extension off of a file name
	 *
	 * @param string $file The file name
	 *
	 * @return  string  The file name without the extension
	 */
	public function stripExt(string $file): string
	{
		return preg_replace('#\.[^.]*$#', '', $file);
	}
}
