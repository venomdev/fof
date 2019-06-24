<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace  FOF40\Render;

use FOF40\Container\Container;

defined('_JEXEC') or die;

/**
 * Renderer class for use with Joomla! 4.x
 *
 * Renderer options
 *
 * linkbar_style           Style for linkbars: joomla3|classic. Default: joomla3
 * remove_wrapper_classes  Comma-separated list of classes to REMOVE from the container
 * add_wrapper_classes     Comma-separated list of classes to ADD to the container
 *
 * @package FOF40\Render
 */
class Joomla4 extends Joomla
{
	public function __construct(Container $container)
	{
		$this->priority	 = 40;
		$this->enabled	 = version_compare(JVERSION, '3.9.999', 'gt');

		parent::__construct($container);
	}

	/**
	 * Opens the FEF styling wrapper element. Our component's output will be inside this wrapper.
	 *
	 * @param   array  $classes  An array of additional CSS classes to add to the outer page wrapper element.
	 *
	 * @return  void
	 */
	protected function openPageWrapper($classes)
	{
		$classes[] = 'akeeba-renderer-joomla4';

		parent::openPageWrapper($classes);
	}

}