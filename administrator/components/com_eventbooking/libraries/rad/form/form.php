<?php

/**
 * Form Class for handling custom fields
 *
 * @package        RAD
 * @subpackage     Form
 */
class RADForm
{

	/**
	 * The array hold list of custom fields
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 *
	 *
	 * @var string
	 */
	protected $fieldSuffix = null;

	/**
	 * Constructor
	 *
	 * @param array $fields
	 */
	public function __construct($fields, $config = array())
	{
		foreach ($fields as $field)
		{
			$class = 'RADFormField' . ucfirst($field->fieldtype);
			if (class_exists($class))
			{
				$this->fields[$field->name] = new $class($field, $field->default_values);
			}
			else
			{
				throw new RuntimeException('The field type ' . $field->fieldType . ' is not supported');
			}
		}
	}

	/**
	 * Get fields of form
	 *
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Set the form fields
	 *
	 * @param array $fields
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Get the field object from name
	 *
	 * @param string $name
	 *
	 * @return RADFormField
	 */
	public function getField($name)
	{
		return $this->fields[$name];
	}

	/**
	 * Bind data into form fields
	 *
	 * @param array $data
	 * @param bool  $useDefault
	 *
	 * @return $this
	 */
	public function bind($data, $useDefault = false)
	{
		foreach ($this->fields as $field)
		{
			if (isset($data[$field->name]))
			{
				$field->setValue($data[$field->name]);
			}
			else
			{
				if ($useDefault)
				{
					$field->setValue($field->row->default_values);
				}
				else
				{
					$field->setValue(null);
				}
			}
		}

		return $this;
	}

	/**
	 * Get the data of all fields on the form
	 *
	 * @return array
	 */
	public function getFormData()
	{
		$data = array();
		foreach ($this->fields as $field)
		{
			$data[$field->name] = $field->value;
		}

		return $data;
	}

	/**
	 *
	 * Add event handle to the custom fee field
	 *
	 * @param string $calculationFeeMethod
	 */
	public function prepareFormFields($calculationFeeMethod)
	{
		$feeFormula = '';
		foreach ($this->fields as $field)
		{
			if ($field->fee_formula)
			{
				$feeFormula .= $field->fee_formula;
			}
		}
		foreach ($this->fields as $field)
		{
			if ($field->fee_field || strpos($feeFormula, '[' . strtoupper($field->name) . ']') !== false)
			{
				$field->setFeeCalculation(true);
				switch ($field->type)
				{
					case 'List':
					case 'Text':
						$field->setAttribute('onchange', $calculationFeeMethod);
						break;
					case 'Checkboxes':
					case 'Radio':
						$field->setAttribute('onclick', $calculationFeeMethod);
						break;
				}
			}
		}
	}

	/**
	 * Build the custom field dependency
	 */
	public function buildFieldsDependency()
	{
		$masterFields = array();
		$fieldsAssoc  = array();
		foreach ($this->fields as $field)
		{
			if ($field->depend_on_field_id)
			{
				$masterFields[] = $field->depend_on_field_id;
			}
			$fieldsAssoc[$field->id] = $field;
		}
		$masterFields = array_unique($masterFields);
		if (count($masterFields))
		{
			$hiddenFields = array();
			foreach ($this->fields as $field)
			{
				if (in_array($field->id, $masterFields))
				{
					$field->setFeeCalculation(true);
					$field->setMasterField(true);
					switch (strtolower($field->type))
					{
						case 'list':
							$field->setAttribute('onchange', "showHideDependFields($field->id, '$field->name', '$field->type', '$this->fieldSuffix');");
							break;
						case 'radio':
						case 'checkboxes':
							$field->setAttribute('onclick', "showHideDependFields($field->id, '$field->name', '$field->type' , '$this->fieldSuffix');");
							break;
					}
				}

				if ($field->depend_on_field_id && isset($fieldsAssoc[$field->depend_on_field_id]))
				{
					// If master field is hided, then children field will be hided, too
					if (in_array($field->depend_on_field_id, $hiddenFields))
					{
						$field->hideOnDisplay();
						$hiddenFields[] = $field->id;
					}
					else
					{
						$masterFieldValues = $fieldsAssoc[$field->depend_on_field_id]->value;
						if (is_array($masterFieldValues))
						{
							$selectedOptions = $masterFieldValues;
						}
						elseif (strpos($masterFieldValues, "\r\n"))
						{
							$selectedOptions = explode("\r\n", $masterFieldValues);
						}
						elseif (is_string($masterFieldValues) && is_array(json_decode($masterFieldValues)))
						{
							$selectedOptions = json_decode($masterFieldValues);
						}
						else
						{
							$selectedOptions = array($masterFieldValues);
						}
						$dependOnOptions = explode(',', $field->depend_on_options);
						if (!count(array_intersect($selectedOptions, $dependOnOptions)))
						{
							$field->hideOnDisplay();
							$hiddenFields[] = $field->id;
						}
					}
				}
			}
		}
	}

	/**
	 * Check if the form contains fee fields or not
	 *
	 * @return boolean
	 */
	public function containFeeFields()
	{
		$containFeeFields = false;
		foreach ($this->fields as $field)
		{
			if ($field->fee_field)
			{
				$containFeeFields = true;
				break;
			}
		}

		return $containFeeFields;
	}

	/**
	 * Set Event ID for form fields, using for quantity control
	 *
	 * @param $eventId
	 */
	public function setEventId($eventId)
	{
		foreach ($this->fields as $field)
		{
			$field->setEventId($eventId);
		}
	}
	/**
	 * Calculate total fee generated by all fields on the form
	 *
	 * @return float total fee
	 */
	public function calculateFee()
	{
		$fee = 0;
		$this->buildFieldsDependency();
		$fieldsFee = $this->calculateFieldsFee();
		foreach ($this->fields as $field)
		{
			if ($field->hideOnDisplay)
			{
				continue;
			}
			if (!$field->row->fee_field)
			{
				continue;
			}
			if (strtolower($field->type) == 'text' || $field->row->fee_formula)
			{
				//Maybe we need to check fee formula
				if (!$field->row->fee_formula)
				{
					continue;
				}
				else
				{
					$formula = $field->row->fee_formula;
					$formula = str_replace('[FIELD_VALUE]', floatval($field->value), $formula);
					if (count($fieldsFee))
					{
						foreach ($fieldsFee as $fieldName => $fieldFee)
						{
							$fieldName = strtoupper($fieldName);
							$formula   = str_replace('[' . $fieldName . ']', $fieldFee, $formula);
						}
					}
					$feeValue = 0;
					if ($formula)
					{
						@eval('$feeValue = ' . $formula . ';');
						$fee += $feeValue;

						//Use the code below if eval is disabled on server
						//$fee += self::calculateFormula($formula);
					}
				}
			}
			else
			{
				$feeValues = explode("\r\n", $field->row->fee_values);
				$values    = explode("\r\n", $field->row->values);
				if (is_array($field->value))
				{
					$fieldValues = $field->value;
				}
				elseif ($field->value)
				{
					$fieldValues   = array();
					$fieldValues[] = $field->value;
				}
				else
				{
					$fieldValues = array();
				}
				$values      = array_map('trim', $values);
				$fieldValues = array_map('trim', $fieldValues);
				for ($j = 0, $m = count($fieldValues); $j < $m; $j++)
				{
					$fieldValue      = $fieldValues[$j];
					$fieldValueIndex = array_search($fieldValue, $values);
					if ($fieldValueIndex !== false && isset($feeValues[$fieldValueIndex]))
					{
						$fee += $feeValues[$fieldValueIndex];
					}
				}
			}
		}

		return $fee;
	}

	/**
	 * Store object data into database. Before running this method, please make sure you set the proper Table, Tablename, FieldName for the Form object
	 *
	 * @param int   $registrantId ID of the object
	 * @param array $data
	 * @param       JTable        The JTable object used to store field value into database
	 */
	public function storeData($registrantId, $data)
	{
		jimport('joomla.filesystem.folder');
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_eventbooking/table');
		$rowFieldValue = JTable::getInstance('EventBooking', 'Fieldvalue');
		$config = EventbookingHelper::getConfig();
		$dateFormat = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$dateFormat = str_replace('%', '', $dateFormat);
		$fieldIds      = array(0);
		$fileFieldIds  = array(0);
		foreach ($this->fields as $field)
		{
			$fieldType = strtolower($field->type);
			if ($fieldType != 'file')
			{
				$fieldIds[] = $field->id;
			}
			else
			{
				$fileFieldIds[] = $field->id;
			}
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__eb_field_values')->where('registrant_id=' . (int) $registrantId)
			->where('field_id IN (' . implode(',', $fieldIds) . ')');
		$db->setQuery($query);
		$db->execute();
		foreach ($this->fields as $field)
		{
			$fieldType = strtolower($field->type);
			if ($field->row->is_core || $fieldType == 'heading' || $fieldType == 'message')
			{
				continue;
			}
			$name = $field->name;
			if ($fieldType == 'file')
			{
				//If there are field, we need to upload the file to server and save it !
				if (isset($_FILES[$name]))
				{
					if ($_FILES[$name]['name'] != '')
					{
						$pathUpload = JPATH_ROOT . '/media/com_eventbooking/files';
						if (!JFolder::exists($pathUpload))
						{
							JFolder::create($pathUpload);
						}
						$allowedExtensions = EventbookingHelper::getConfigValue('allowed_file_types');
						if (!$allowedExtensions)
						{
							$allowedExtensions = 'doc, docx, ppt, pptx, pdf, zip, rar, jpg, jepg, png, zipx';
						}
						$allowedExtensions = explode(',', $allowedExtensions);
						$allowedExtensions = array_map('trim', $allowedExtensions);
						$fileName          = $_FILES[$field->name]['name'];
						$fileExt           = JFile::getExt($fileName);
						if (in_array(strtolower($fileExt), $allowedExtensions))
						{
							$fileName = JFile::makeSafe($fileName);
							if (JFile::exists($pathUpload . '/' . $fileName))
							{
								$targetFileName = time() . '_' . $fileName;
							}
							else
							{
								$targetFileName = $fileName;
							}
							JFile::upload($_FILES[$field->name]['tmp_name'], $pathUpload . '/' . $targetFileName);
							$data[$field->name] = $targetFileName;
						}
					}
				}
			}

			if ($fieldType == 'date')
			{
				$fieldValue = $data[$field->name];
				if ($fieldValue)
				{
					// Try to convert the format
					try
					{
						$date       = DateTime::createFromFormat($dateFormat, $fieldValue);
						if ($date)
						{
							$fieldValue = $date->format('Y-m-d');
						}
						else
						{
							$fieldValue = '';
						}
					}
					catch (Exception $e)
					{
						$fieldValue = '';
					}
					$data[$field->name] = $fieldValue;
				}
			}

			$fieldValue = isset($data[$field->name]) ? $data[$field->name] : '';
			if ($fieldValue != '')
			{
				if (in_array($field->id, $fileFieldIds))
				{
					$query->clear();
					$query->delete('#__eb_field_values')
						->where('registrant_id=' . (int) $registrantId)
						->where('field_id = ' . $field->id);
					$db->setQuery($query);
					$db->execute();
				}
				$rowFieldValue->id            = 0;
				$rowFieldValue->field_id      = $field->row->id;
				$rowFieldValue->registrant_id = $registrantId;
				if (is_array($fieldValue))
				{
					$rowFieldValue->field_value = json_encode($fieldValue);
				}
				else
				{
					$rowFieldValue->field_value = $fieldValue;
				}
				$rowFieldValue->store();
			}
		}

		return true;
	}

	/**
	 * Set the suffix for the form fields which will change the name of it
	 *
	 * @param string $suffix
	 */
	public function setFieldSuffix($suffix)
	{
		$this->fieldSuffix = $suffix;
		foreach ($this->fields as $field)
		{
			$field->setFieldSuffix($suffix);
		}
	}

	/**
	 * Remove the suffix for the form fields which will change the name of it
	 *
	 * @param string $suffix
	 */
	public function removeFieldSuffix()
	{
		foreach ($this->fields as $field)
		{
			$field->removeFieldSuffix();
		}
	}

	/**
	 * Calculate the fee associated with each field to use in fee formula
	 *
	 * @return array
	 */
	private function calculateFieldsFee()
	{
		$fieldsFee     = array();
		$feeFieldTypes = array('text', 'radio', 'list', 'checkboxes');
		foreach ($this->fields as $fieldName => $field)
		{
			if ($field->hideOnDisplay)
			{
				continue;
			}
			$fieldType = strtolower($field->type);
			if (in_array($fieldType, $feeFieldTypes))
			{
				if ($fieldType == 'text')
				{
					$fieldsFee[$fieldName] = floatval($field->value);
				}
				elseif ($fieldType == 'checkboxes' || ($fieldType == 'list' && $field->row->multiple))
				{
				}
				else
				{
					$feeValues  = explode("\r\n", $field->row->fee_values);
					$values     = explode("\r\n", $field->row->values);
					$values     = array_map('trim', $values);
					$valueIndex = array_search(trim($field->value), $values);
					if ($valueIndex !== false && isset($feeValues[$valueIndex]))
					{
						$fieldsFee[$fieldName] = $feeValues[$valueIndex];
					}
				}
			}
		}

		return $fieldsFee;
	}

	/**
	 * Helper function to calculate fee when eval function is disabled by the hosting
	 *
	 * @param string $formula
	 *
	 * @return number
	 */
	public static function calculateFormula($formula)
	{
		$formula = trim($formula);     // trim white spaces
		$formula = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $formula);    // remove any non-numbers chars; exception for math operators
		$compute = create_function("", "return (" . $formula . ");");

		return 0 + $compute();
	}
}