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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('text');

/**
 * Form Field class for the FOF framework
 * Displays a field generated by a callback
 *
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
class Callback extends FormField implements FieldInterface
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
			case 'input':
				if (empty($this->input))
				{
					$this->input = $this->getInput();
				}

				return $this->input;
				break;

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
		return $this->getCallbackResults();
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
		return $this->getCallbackResults();
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		return $this->getCallbackResults();
	}

	/**
	 * Returns the rendered view template
	 *
	 * @return string
	 */
	protected function getCallbackResults()
	{
		$source_file   = empty($this->element['source_file']) ? '' : (string) $this->element['source_file'];
		$source_class  = empty($this->element['source_class']) ? '' : (string) $this->element['source_class'];
		$source_method = empty($this->element['source_method']) ? '' : (string) $this->element['source_method'];

		if (empty($source_class) || empty($source_method))
		{
			return '';
		}

		// Maybe we have to load a file?
		if (!empty($source_file))
		{
			$source_file = $this->form->getContainer()->template->parsePath($source_file, true);

			if ($this->form->getContainer()->filesystem->fileExists($source_file))
			{
				include_once $source_file;
			}
		}

		// Make sure the class exists
		if (class_exists($source_class, true))
		{
			// ...and so does the option
			if (in_array($source_method, get_class_methods($source_class)))
			{
				return $source_class::$source_method([
					'model'        => $this->form->getModel(),
					'form'         => $this->form,
					'formType'     => $this->form->getAttribute('type', 'edit'),
					'fieldValue'   => $this->value,
					'fieldElement' => $this->element,
				]);
			}
		}

		return '';
	}
}
