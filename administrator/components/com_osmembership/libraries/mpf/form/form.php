<?php
/**
 * @package     MPF
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Form Class for handling custom fields
 *
 * @package        MPF
 * @subpackage     Form
 */
class MPFForm
{

	/**
	 * The array hold list of custom fields
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Form Data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param array  $rows
	 * @param array  $data
	 * @param string $fieldSuffix
	 */
	public function __construct($rows, $data = array(), $fieldSuffix = null)
	{
		foreach ($rows as $row)
		{
			$class = 'MPFFormField' . ucfirst($row->fieldtype);
			if (class_exists($class))
			{
				$this->fields[$row->name] = new $class($row, $row->default_values, $fieldSuffix);
			}
			else
			{
				throw new RuntimeException('The field type ' . $row->fieldType . ' is not supported');
			}
		}
		$this->data = $data;
		if (count($this->data))
		{
			$this->bindData();
		}
	}

	/**
	 * Method to bind data to the fields.
	 *
	 * @param bool $useDefault
	 *
	 * @return $this
	 */
	public function bindData($useDefault = false)
	{
		if (count($this->fields))
		{
			foreach ($this->fields as $field)
			{
				if (isset($this->data[$field->name]))
				{
					$field->setValue($this->data[$field->name]);
				}
				else
				{
					if ($useDefault)
					{
						$field->setValue($field->default_values);
					}
					else
					{
						$field->setValue(null);
					}
				}
			}
		}

		return $this;
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
	 * Set data for the form
	 *
	 * @param $data
	 *
	 * @return $this
	 */
	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Build the custom field dependency
	 *
	 */
	public function buildFieldsDependency($processMasterFields = true)
	{
		$masterFields = array();
		$fieldsAssoc = array();
		foreach ($this->fields as $field)
		{
			if ($field->depend_on_field_id)
			{
				$masterFields[] = $field->depend_on_field_id;
			}
			$fieldsAssoc[$field->id] = $field;
		}
		if (count($masterFields))
		{
			foreach ($this->fields as $field)
			{
				if (in_array($field->id, $masterFields))
				{
					if ($processMasterFields)
					{
						$field->setFeeCalculation(true);
						switch (strtolower($field->type))
						{
							case 'list':
								$field->setAttribute('onchange', "showHideDependFields($field->id, '$field->name', '$field->type');");
								break;
							case 'radio':
							case 'checkboxes':
								$field->setAttribute('onclick', "showHideDependFields($field->id, '$field->name', '$field->type');");
								break;
						}
					}
				}
				elseif ($field->depend_on_field_id && isset($fieldsAssoc[$field->depend_on_field_id]))
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
						$field->setVisibility(false);
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
	 * Prepare form field, add necessary javascript trigger
	 *
	 * @param $calculationFeeMethod
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
	 * Store subscriber data into database
	 *
	 * @param $recordId
	 * @param $data
	 *
	 * @return bool
	 */
	public function storeData($recordId, $data, $excludeFeeFields = false)
	{
		if (!count($this->fields))
		{
			return true;
		}

		jimport('joomla.filesystem.folder');
		$config = OSMembershipHelper::getConfig();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$dateFormat = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$dateFormat = str_replace('%', '', $dateFormat);

		// Don't delete the file upload custom fields
		$fieldIds     = array(0);
		$fileFieldIds = array(0);
		foreach ($this->fields as $field)
		{

			if ($field->type == 'File')
			{
				$fileFieldIds[] = $field->id;
			}
			elseif (!$excludeFeeFields || !$field->fee_field)
			{
				$fieldIds[] = $field->id;
			}
		}

		$query->delete('#__osmembership_field_value')
			->where('subscriber_id = '. (int) $recordId)
			->where('field_id IN ('.implode(',', $fieldIds).')');
		$db->setQuery($query);
		$db->execute();

		$rowFieldValue = JTable::getInstance('OsMembership', 'FieldValue');

		foreach ($this->fields as $field)
		{
			$fieldType = strtolower($field->type);
			if ($field->is_core || $fieldType == 'heading' || $fieldType == 'message')
			{
				continue;
			}

			// Don't update fee field if not needed
			if ($excludeFeeFields && $field->fee_field)
			{
				continue;
			}

			$name = $field->name;
			if ($fieldType == 'file')
			{
				// If there are field, we need to upload the file to server and save it !
				if (isset($_FILES[$name]))
				{
					if ($_FILES[$name]['name'] != '')
					{
						$pathUpload = JPATH_ROOT . '/media/com_osmembership/upload';
						if (!JFolder::exists($pathUpload))
						{
							JFolder::create($pathUpload, 0777);
						}
						$allowedExtensions = OSMembershipHelper::getConfigValue('allowed_file_types');
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
					// Need to delete the old file
					$sql = 'DELETE FROM #__osmembership_field_value WHERE subscriber_id=' . (int) $recordId . ' AND field_id=' .
						$field->id;
					$db->setQuery($sql);
					$db->execute();
				}
				$rowFieldValue->id            = 0;
				$rowFieldValue->field_id      = $field->id;
				$rowFieldValue->subscriber_id = $recordId;
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
	 * Calculate total fee generated by all fields on the form
	 *
	 * @return float total fee
	 */
	public function calculateFee()
	{
		if (!count($this->fields))
		{
			return 0;
		}
		$fee       = 0;
		$this->buildFieldsDependency();
		$fieldsFee = $this->calculateFieldsFee();
		foreach ($this->fields as $field)
		{
			if (!$field->visible)
			{
				continue;
			}
			if (!$field->row->fee_field)
			{
				continue;
			}
			if (strtolower($field->type) == 'text' || $field->row->fee_formula)
			{
				// Maybe we need to check fee formula
				if (!$field->row->fee_formula)
				{
					continue;
				}
				else
				{
					$formula = $field->row->fee_formula;
					$formula = str_replace('[FIELD_VALUE]', $field->value, $formula);
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
					}
				}
			}
			else
			{
				$feeValues = explode("\r\n", $field->row->fee_values);
				$values    = explode("\r\n", $field->row->values);
				$values    = array_map('trim', $values);
				if (is_array($field->value))
				{
					$fieldValues = $field->value;
				}
				elseif (is_string($field->value) && is_array(json_decode($field->value)))
				{
					$fieldValues = json_decode($field->value);
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
				for ($j = 0, $m = count($fieldValues); $j < $m; $j++)
				{
					$fieldValue      = trim($fieldValues[$j]);
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
			$fieldType = strtolower($field->type);
			if (in_array($fieldType, $feeFieldTypes))
			{
				if ($fieldType == 'text')
				{
					$fieldsFee[$fieldName] = floatval($field->value);
				}
				elseif ($fieldType == 'checkboxes' || ($fieldType == 'list' && $field->row->multiple))
				{
					$feeValues = explode("\r\n", $field->row->fee_values);
					$values    = explode("\r\n", $field->row->values);
					$feeAmount = 0;

					if (is_array($field->value))
					{
						$selectedOptions = $field->value;
					}
					elseif (strpos($field->value, "\r\n"))
					{
						$selectedOptions = explode("\r\n", $field->value);
					}
					elseif (is_string($field->value) && is_array(json_decode($field->value)))
					{
						$selectedOptions = json_decode($field->value);
					}
					else
					{
						$selectedOptions = array($field->value);
					}
					if (is_array($selectedOptions))
					{
						foreach ($selectedOptions as $selectedOption)
						{
							$index = array_search($selectedOption, $values);
							if ($index !== false)
							{
								if (isset($feeValues[$index]))
								{
									$feeAmount += floatval($feeValues[$index]);
								}
							}
						}
					}

					$fieldsFee[$fieldName] = $feeAmount;
				}
				else
				{
					$feeValues  = explode("\r\n", $field->row->fee_values);
					$values     = explode("\r\n", $field->row->values);
					$values     = array_map('trim', $values);
					$valueIndex = array_search(trim($field->value), $values);
					if ($valueIndex !== false && isset($feeValues[$valueIndex]))
					{
						$fieldsFee[$fieldName] = floatval($feeValues[$valueIndex]);
					}
					else
					{
						$fieldsFee[$fieldName] = 0;
					}
				}
			}
		}

		return $fieldsFee;
	}
}
