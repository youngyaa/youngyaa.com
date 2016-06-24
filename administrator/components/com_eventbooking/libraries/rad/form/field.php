<?php

/**
 * Abstract Form Field class for the RAD framework
 *
 * @package     Joomla.RAD
 * @subpackage  Form
 */
abstract class RADFormField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type;

	/**
	 * The name (and id) for the form field.
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * Title of the form field
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Description of the form field
	 * @var string
	 */
	protected $description;

	/**
	 * The current value of the form field.
	 *
	 * @var    mixed
	 */
	protected $value;

	/**
	 * The object store form field definition
	 *
	 * @var JTable
	 */
	protected $row;

	/**
	 * The html attributes of the field
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The label for the form field.
	 *
	 * @var    string
	 */
	protected $label;

	/**
	 * The input for the form field.
	 *
	 * @var    string
	 */
	protected $input;

	/**
	 * This field is used in fee calculation or not
	 *
	 * @var bool
	 */
	protected $feeCalculation;

	/**
	 * This field will be hided on first display or not
	 *
	 * @var bool
	 */
	protected $hideOnDisplay = false;

	/**
	 * This field is a master field or not
	 *
	 * @var bool
	 */
	protected $isMasterField = false;

	/**
	 * Id of the event this custom field belong to
	 *
	 * @var null
	 */
	protected $eventId = null;

	/**
	 * Field suffix
	 *
	 * @var string
	 */
	protected $suffix = null;

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JTable $row   the table object store form field definitions
	 * @param    mixed $value the initial value of the form field
	 *
	 */
	public function __construct($row, $value = null)
	{
		$this->name        = $row->name;
		$this->title       = JText::_($row->title);
		$this->description = $row->description;
		$this->row         = $row;
		$this->value       = $value;
		$cssClasses        = array();
		if ($row->css_class)
		{
			$cssClasses[] = $row->css_class;
		}
		if ($row->validation_rules)
		{
			$cssClasses[] = $row->validation_rules;
		}
		if (count($cssClasses))
		{
			$this->attributes['class'] = implode(' ', $cssClasses);
		}
		if ($row->validation_error_message)
		{
			$this->attributes['data-errormessage'] = $row->validation_error_message;
		}
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string $name The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'type':
			case 'name':
			case 'title':
			case 'description':
			case 'value':
			case 'row':
			case 'hideOnDisplay':
			case 'isMaterField':
			case 'eventId':
				return $this->{$name};
				break;
			case 'fee_field':
			case 'fee_formula':
			case 'id':
			case 'depend_on_field_id':
			case 'depend_on_options':
			case 'quantity_field':
				return $this->row->{$name};
				break;
			case 'input':
				// If the input hasn't yet been generated, generate it.
				if (empty($this->input))
				{
					$this->input = $this->getInput();
				}

				return $this->input;
				break;
			case 'label':
				// If the label hasn't yet been generated, generate it.
				if (empty($this->label))
				{
					$this->label = $this->getLabel();
				}

				return $this->label;
				break;
		}

		return null;
	}

	/**
	 * Simple method to set the value for the form field
	 *
	 * @param   mixed $value Value to set
	 *
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * Set suffix for the form field
	 *
	 * @param string $suffix
	 */
	public function setFieldSuffix($suffix)
	{
		$this->suffix = $suffix;
		$this->name   = $this->name . '_' . $suffix;
	}

	/**
	 * Remove the suffix from name of the field
	 */
	public function removeFieldSuffix()
	{
		$pos = strrpos($this->name, '_');
		if ($pos !== false)
		{
			$this->name = substr($this->name, 0, $pos);
		}

		$this->suffix = null;
	}

	/**
	 * Add attribute to the form field
	 *
	 * @param string $name
	 */
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * Get data of the given attribute
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getAttribute($name)
	{
		return $this->attributes[$name];
	}

	/**
	 * Remove the attribute
	 *
	 * @param $name
	 */
	function removeAttribute($name)
	{
		if (isset($this->attributes[$name]))
		{
			unset($this->attributes[$name]);
		}
	}

	/**
	 * Mark this field as a fee-affected custom field
	 *
	 * @param int $feeCalculation
	 */
	public function setFeeCalculation($feeCalculation)
	{
		$this->feeCalculation = $feeCalculation;
	}


	public function setMasterField($isMasterField)
	{
		$this->isMasterField = $isMasterField;
	}

	/**
	 * Associate this custom field with an event for quantity control
	 *
	 * @param $eventId
	 */
	public function setEventId($eventId)
	{
		$this->eventId = $eventId;
	}

	/**
	 *
	 */
	public function hideOnDisplay()
	{
		$this->hideOnDisplay = true;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	abstract protected function getInput($bootstrapHelper = null);

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 */
	protected function getLabel()
	{
		$label = '';
		$text  = $this->title;
		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTooltip hasTip' : '';
		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->name . '-lbl" for="' . $this->name . '" class="' . $class . '"';
		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			JHtml::_('bootstrap.tooltip');
			JFactory::getDocument()->addStyleDeclaration(".hasTip{display:block !important}");
			$label .= ' title="' . JHtml::tooltipText(trim($text, ':'), $this->description, 0) . '"';
		}

		// Add the label text and closing tag.
		if ($this->row->required)
		{
			$label .= '>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label>';
		}

		return $label;
	}

	/**
	 * Method to get a control group with label and input.
	 *
	 * @return  string  A string containing the html for the control goup
	 *
	 */
	public function getControlGroup($bootstrapHelper = null)
	{
		if ($this->type == 'hidden')
		{
			return $this->getInput();
		}
		else
		{
			$controlGroupClass      = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-group') : 'control-group';
			$controlLabelClass      = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-label') : 'control-label';
			$controlsClass          = $bootstrapHelper ? $bootstrapHelper->getClassMapping('controls') : 'controls';
			$controlGroupAttributes = 'id="field_' . $this->name . '" ';

			if ($this->hideOnDisplay)
			{
				$controlGroupAttributes .= ' style="display:none;" ';
			}
			$classes = array();
			if ($this->feeCalculation)
			{
				$classes[] = 'payment-calculation';
			}

			if ($this->isMasterField)
			{
				if ($this->suffix)
				{
					$classes[] = 'master-field-' . $this->suffix;
				}
				else
				{
					$classes[] = 'master-field';
				}
			}

			$class = implode(' ', $classes);
			if (!empty($class))
			{
				$class = ' ' . $class;
			}
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				return '<div class="' . $controlGroupClass . $class . '" ' . $controlGroupAttributes . '>' . '<div class="' . $controlLabelClass . '">' . $this->getLabel() . '</div>' . '<div class="' . $controlsClass . '">' .
				$this->getInput() . '</div>' . '</div>';
			}
			else
			{
				return '<div class="' . $controlGroupClass . $class . '" ' . $controlGroupAttributes . '>' . '<div class="' . $controlLabelClass . '">' . $this->title .
				($this->row->required ? '<span class="star">&#160;*</span>' : '') . '</div>' . '<div class="' . $controlsClass . '">' . $this->getInput($bootstrapHelper) . '</div>' .
				'</div>';
			}
		}
	}

	/**
	 * Get output of the field using for sending email and display on the registration complete page
	 *
	 * @param bool                        $tableLess
	 *
	 * @param EventBookingHelperBootstrap $bootstrapHelper
	 *
	 * @return string
	 */
	public function getOutput($tableLess = true, $bootstrapHelper = null)
	{

		if (is_string($this->value) && is_array(json_decode($this->value)))
		{
			$fieldValue = implode(', ', json_decode($this->value));
		}
		else
		{
			$fieldValue = $this->value;
		}
		if ($tableLess)
		{
			$controlGroupClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-group') : 'control-group';
			$controlLabelClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-label') : 'control-label';
			$controlsClass     = $bootstrapHelper ? $bootstrapHelper->getClassMapping('controls') : 'controls';

			return '<div class="' . $controlGroupClass . '">' . '<div class="' . $controlLabelClass . '">' . $this->title . '</div>' . '<div class="' . $controlsClass . '">' .
			$fieldValue . '</div>' . '</div>';
		}
		else
		{
			return '<tr>' . '<td class="title_cell">' . $this->title . '</td>' . '<td class="field_cell">' .
			$fieldValue . '</td>' . '</tr>';
		}
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @return string
	 */
	public function buildAttributes()
	{
		$html = array();
		foreach ((array) $this->attributes as $key => $value)
		{
			if (is_bool($value))
			{
				$html[] = " $key ";
			}
			else
			{

				$html[] = $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8', false) . '"';
			}
		}

		return count($html) > 0 ? ' ' . implode(' ', $html) : '';
	}

	/**
	 * Make current file optional
	 */
	public function makeFieldOptional()
	{
		// This only need to be processed if the field is required
		if (!$this->row->required)
		{
			return;
		}

		$cssClass = $this->getAttribute('class');
		if (strpos($cssClass, 'validate[required,') !== false)
		{
			$cssClass = str_replace('validate[required,', 'validate[', $cssClass);
		}

		if (strpos($cssClass, 'validate[required') !== false)
		{
			$cssClass = str_replace('validate[required', 'validate[', $cssClass);
		}

		if (strpos($cssClass, ' validate[]') !== false)
		{
			$cssClass = str_replace(' validate[]', '', $cssClass);
		}

		if (strpos($cssClass, 'validate[]') !== false)
		{
			$cssClass = str_replace('validate[]', '', $cssClass);
		}

		if ($cssClass)
		{
			$this->setAttribute('class', $cssClass);
		}
		else
		{
			$this->removeAttribute('class');
		}

		$this->row->required = 0;
	}
}
