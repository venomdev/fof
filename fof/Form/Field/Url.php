<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

namespace FOF30\Form\Field;

use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Model\DataModel;
use JFormFieldUrl;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('url');

/**
 * Form Field class for the FOF framework
 * Supports a URL text field.
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Url extends JFormFieldUrl implements FieldInterface
{
	/**
	 * A monotonically increasing number, denoting the row number in a repeatable view
	 *
	 * @var  int
	 */
	public $rowid;
	/**
	 * The item being rendered in a repeatable form field
	 *
	 * @var  DataModel
	 */
	public $item;
	/**
	 * @var  string  Static field output
	 */
	protected $static;
	/**
	 * @var  string  Repeatable field output
	 */
	protected $repeatable;
	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var    Form
	 */
	protected $form;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   2.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'static':
				if (empty($this->static))
				{
					$this->static = $this->getStatic();
				}

				return $this->static;
				break;

			case 'repeatable':
				if (empty($this->repeatable))
				{
					$this->repeatable = $this->getRepeatable();
				}

				return $this->repeatable;
				break;

			default:
				return parent::__get($name);
		}
	}

	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getStatic()
	{
		if (isset($this->element['legacy']))
		{
			return $this->getInput();
		}

		$options = [
			'id' => $this->id,
		];

		return $this->getFieldContents($options);
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @return  string  The field HTML
	 * @since 2.0
	 *
	 */
	public function getRepeatable()
	{
		if (isset($this->element['legacy']))
		{
			return $this->getInput();
		}

		$options = [
			'class' => $this->id,
		];

		return $this->getFieldContents($options);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @param   array  $fieldOptions  Options to be passed into the field
	 *
	 * @return  string  The field HTML
	 */
	public function getFieldContents(array $fieldOptions = [])
	{
		$id    = isset($fieldOptions['id']) ? 'id="' . $fieldOptions['id'] . '" ' : '';
		$class = $this->class . (isset($fieldOptions['class']) ? ' ' . $fieldOptions['class'] : '');

		$show_link = $this->element['show_link'] == 'true';

		$empty_replacement = $this->element['empty_replacement'] ? (string) $this->element['empty_replacement'] : '';

		if (!empty($empty_replacement) && empty($this->value))
		{
			$this->value = \Joomla\CMS\Language\Text::_($empty_replacement);
		}

		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');

		$html = $value;

		if ($show_link)
		{
			if ($this->element['url'])
			{
				$link_url = $this->parseFieldTags((string) $this->element['url']);
			}
			else
			{
				$link_url = $value;
			}

			$html = '<a href="' . $link_url . '">' .
				$value . '</a>';
		}

		return '<span ' . ($id ? $id : '') . 'class="' . $class . '"">' .
			$html .
			'</span>';
	}

	/**
	 * Replace string with tags that reference fields
	 *
	 * @param   string  $text  Text to process
	 *
	 * @return  string         Text with tags replace
	 */
	protected function parseFieldTags($text)
	{
		$ret = $text;

		// Replace [ITEM:ID] in the URL with the item's key value (usually:
		// the auto-incrementing numeric ID)
		if (is_null($this->item))
		{
			$this->item = $this->form->getModel();
		}

		$replace = $this->item->getId();
		$ret     = str_replace('[ITEM:ID]', $replace, $ret);

		// Replace the [ITEMID] in the URL with the current Itemid parameter
		$ret = str_replace('[ITEMID]', $this->form->getContainer()->input->getInt('Itemid', 0), $ret);

		// Replace the [TOKEN] in the URL with the Joomla! form token
		$ret = str_replace('[TOKEN]', Factory::getSession()->getFormToken(), $ret);

		// Replace other field variables in the URL
		$data = $this->item->getData();

		foreach ($data as $field => $value)
		{
			// Skip non-processable values
			if (is_array($value) || is_object($value))
			{
				continue;
			}

			$search = '[ITEM:' . strtoupper($field) . ']';
			$ret    = str_replace($search, $value, $ret);
		}

		return $ret;
	}
}
