<?php
/**
 * ------------------------------------------------------------------------
 * JA Multilingual Component for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;


jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');
jimport('joomla.base.adapter');

class JalangHelperTranslator extends JAdapter
{
	protected $ignoreTranslated = true;

	protected $contentType;

	public $params = null;

	/**
	 * @var array - store an item ids association of Joomla Tables
	 */
	public $aAssociation = array();
	/**
	 * @var code of source language (in Translation service system)
	 */
	public $from;
	/**
	 * @var code of destination language (in Translation service system)
	 */
	public $to;
	/**
	 * @var code of source language (in Joomla system)
	 */
	public $fromLangTag;
	/**
	 * @var code of destination language (in Joomla system)
	 */
	public $toLangTag;

	/**
	 * @var language that will be automatically assigned for items that set to All language after it is successfully translated
	 */
	public $convertLangTag = null;

	
	/**
	 * @var    JController  JController instance container.
	 * @since  11.3
	 */
	protected static $instance = array();
	
	public function __construct()
	{
		parent::__construct(dirname(__FILE__), 'JalangHelperTranslator');

		$this->params = JComponentHelper::getParams('com_jalang');
	}
	
	/**
	 * get instance of translation adapter
	 *
	 * @param string $type
	 * @param array $options
	 * @return JalangHelperTranslator
	 * 
	 * @see JalangHelperTranslator
	 */
	public static function getInstance($type, $options = array())
	{
		/*if(!isset($options['from']) || !isset($options['to'])) {
			JError::raiseWarning(400, 'JalangHelperTranslator::getInstance() '.JText::_('MISSING_PARAMS_PASSED'));
			return false;
		}*/
		
		if (isset(self::$instance[$type]) && is_object(self::$instance[$type]))
		{
			return self::$instance[$type];
		} else {
			$translator = new JalangHelperTranslator();
			/**
			 * @todo report Joomla to below issue
			 * must setAdapter first since, getAdapter with options params passed will return incorrect object
			 * 
			 * Expected code for instead: $translator->getAdapter($type, $options);
			 */
			$adapter = null;
			$translator->setAdapter($type, $adapter, $options);
			self::$instance[$type] = $translator->getAdapter($type);
		}
		
		return self::$instance[$type];
	}

	public function translate($sentence) { }
	public function translateArray($sentences, $fields) { }

	final public function translateAllTables($from, $to) {
		$adapters = JalangHelperContent::getListAdapters();
		foreach($adapters as $adapter) {
            $this->translateTable($adapter['name'], $from, $to);
        }
		$this->updateTemplateStyles();
	}

	final public function translateTable($itemtype, $from, $to, $adapter = null, &$count=0) {
		$defaultLanguage = JalangHelper::getDefaultLanguage();
		$params = JComponentHelper::getParams('com_jalang');
		$firstRun = !$adapter ? 1 : 0;
        $re_translate = $params->get('re_translate',array());
		if($firstRun) {
			$this->sendOutput('['.JText::sprintf('STARTED_TRANSLATE_THE_TABLE_VAR', $itemtype).']');
		}
		if(!$from) $from = '*';
		$this->convertLangTag = null;
		if($this->fromLangTag !== $from) {
			JalangHelper::createLanguageContent($from);
			$this->from = $this->getLangCode($from);
			$this->fromLangTag = $from;
		}
		if($this->toLangTag !== $to) {
			JalangHelper::createLanguageContent($to);
			$this->to = $this->getLangCode($to);
			$this->toLangTag = $to;
		}
		if(!$this->from) {
			$this->sendOutput(JText::_('SOURCE_LANGUAGE_IS_NOT_SPECIFIED_OR_NOT_SUPPORTED'));
			return false;
		}
		if(!$this->to || $this->to == '*') {
			$this->sendOutput(JText::_('DESTINATION_LANGUAGE_IS_NOT_SPECIFIED_OR_NOT_SUPPORTED'));
			return false;
		}
		if($this->fromLangTag == $this->toLangTag) {
			$this->sendOutput(JText::_('SOURCE_LANGUAGE_AND_DESTINATION_LANGUAGE_MUST_DIFFERENT'));
			return false;
		}

		if(!is_object($adapter)) {
			$adapter = JalangHelperContent::getInstance($itemtype);
			if(!$adapter) {
				$this->sendOutput(JText::sprintf('CONTENT_TYPE_VAR_IS_NOT_SUPPORTED', $itemtype));
				return false;
			}
		}
		if(!count($adapter->translate_fields)) {
			$this->sendOutput(JText::sprintf('LIST_OF_FIELDS_FOR_TRANSLATING_IS_EMPTY_PLEASE_CHECK_CONFIGURATION', $itemtype));
			return false;
		}

		if($firstRun) {
			//load association data
			if(count($adapter->reference_tables) || count($adapter->reference_fields)) {
				$reference_tables = @array_merge($adapter->reference_tables, array_values($adapter->reference_fields));
				$reference_tables = array_unique($reference_tables);
				foreach($reference_tables as $table) {
					$adapter2 = JalangHelperContent::getInstance($table);
					if($adapter2) {
						$this->loadAssociate($adapter2->table, $adapter2->primarykey, $adapter2->associate_context);
					}
				}
			}
			$this->loadAssociate($adapter->table, $adapter->primarykey, $adapter->associate_context);

			$adapter->beforeTranslate($this);
		}

		//TRANSLATE ITEMS
		$from_table = $to_table = $table = '#__'.$adapter->table;
		if($adapter->table_type == 'table') {
			$from_table = $this->getLangTable($table, $this->fromLangTag);
			$to_table = $this->getLangTable($table, $this->toLangTag);
		}
		$fields = $adapter->translate_fields;
		$context = $adapter->associate_context;
		$alias = $adapter->alias_field;
		$langField = $adapter->language_field;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($from_table);
		if($adapter->nested_field) {
			$query->where($db->quoteName($adapter->nested_field) .'='.$db->quote($adapter->nested_value));
		}

		if(!empty($adapter->translate_filters)) {
			$query->where($adapter->translate_filters);
		}

		if($this->ignoreTranslated && !$adapter->nested_field) {
            //always check all items of nested table to get a new sub-items
			$translatedItems = $this->getTranslatedItems($adapter->table);
            //Suport to re translated item
            $sqlRe = $db->getQuery(true);
            if(count($re_translate) && in_array($adapter->table, $re_translate)){
                switch($adapter->table){
                    case 'mijoshop_product_description':
                        $sqlRe -> select('mid.product_id')
                               -> from ($db->quoteName('#__mijoshop_product','mip'))
                               -> join ('INNER', $db->quoteName($table,'mid') . ' ON mip.product_id = mid.product_id')
                               -> where ('(mip.date_modified > '. '(SELECT date_translate FROM #__jalang_translated WHERE adapter = '.$db->quote($adapter->table).'AND language = '.$db->quote($this->toLangTag).') OR mip.`date_modified` = "0000-00-00 00:00:00") AND mid.language_id = '.$db->quote(JalangHelper::getLanguageIdFromCode($this->fromLangTag)));
                        break;
                    case 'virtuemart_products' :
                        $sqlRe -> select('virtuemart_product_id')
                               -> from ($table)
                               -> where ($db->quoteName('modified_on') . ' > '. '(SELECT date_translate FROM #__jalang_translated WHERE adapter = '.$db->quote($adapter->table).' AND language = '.$db->quote($this->toLangTag).')');
                        break;
                    default:
                        $sqlRe -> select('id')
                               -> from ($table)
                               -> where ($db->quoteName('modified') . ' > '. '(SELECT date_translate FROM #__jalang_translated WHERE adapter = '.$db->quote($adapter->table).' AND language = '.$db->quote($this->toLangTag).') AND language = '.$db->quote($this->fromLangTag));
                        break;
                }
                $db->setQuery($sqlRe);
                $contentIdRe = $db->loadObjectList();
                if(count($contentIdRe) > 0){
                    if(count($translatedItems)){
                        foreach($translatedItems as $k => $item){
                            foreach($contentIdRe as $c => $ct){
                                switch ($adapter->table){
                                    case 'mijoshop_product_description' :
                                        if((int)$ct->product_id == (int)$item){
                                                unset($translatedItems[$k]);
                                        }    
                                        break;
                                    case 'virtuemart_products' :
                                        if((int)$ct->virtuemart_product_id == (int)$item){
                                            unset($translatedItems[$k]);
                                        }
                                        break;
                                    default :
                                        if((int)$ct->id == (int)$item){
                                            unset($translatedItems[$k]);
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            if(count($translatedItems)){
                $query->where($db->quoteName($adapter->primarykey).' NOT IN ('.implode(',', $translatedItems).')');
            }
		}
		if($adapter->table_type == 'alias') {
			if($this->fromLangTag != '*') {
				if($this->fromLangTag == $defaultLanguage) {
					$query->where('('.$db->quoteName($adapter->alias_field) .' LIKE '.$db->quote('%-'.$this->from). ' OR '.$db->quoteName($adapter->alias_field) .' NOT REGEXP '.$db->quote('-[a-z]{2}$').')');
				} else {
					$query->where($db->quoteName($adapter->alias_field) .' LIKE '.$db->quote('%-'.$this->from));
				}
			} else {
				$query->where($db->quoteName($adapter->alias_field) .' NOT REGEXP '.$db->quote('-[a-z]{2}$'));
			}
		} elseif($adapter->table_type == 'table') {

		} else {
			//native + table_ml
			if($adapter->language_mode == 'id') {
				$query->where($langField.' = ' . $db->quote(JalangHelper::getLanguageIdFromCode($this->fromLangTag)));
			} else {
				if($this->fromLangTag == $defaultLanguage) {
					$query->where('('.$langField .'='.$db->quote($this->fromLangTag).' OR '.$langField .'='.$db->quote('*').' OR '.$langField .'='.$db->quote('').')');
				} else {
					$query->where($langField .'='.$db->quote($this->fromLangTag));
				}
			}
		}
//        if($this->toLangTag == 'ja-JP' && $adapter->table == 'easyblog_post'){
//            echo $query;
//        }
		$db->setQuery($query);
		$rows = $db->loadAssocList();

        if(preg_match('/content|easyblog_post|mijoshop_product_description|k2_items|virtuemart_products/', $adapter->table)){
			$query->clear();
			$query->select('elementid')->from('#__jalang_translated')->where('adapter = '.$db->quote($adapter->table).' AND language = '.$db->quote($this->toLangTag));
			$db->setQuery($query);
			$elementid = $db->loadColumn();
			if(!$elementid)
				$elementid = array();
			else
				$elementid = explode(',', $elementid[0]);
        }

		if(count($rows)) {
			foreach ($rows as $row) {
			     //No translate hyperlink in the content of article.
                 if($adapter->table == 'content'){
                    $pattern = '/http:\/\/([^"]*)/';
                    if(preg_match($pattern, $row['introtext'])){
                        preg_match_all($pattern,$row['introtext'],$matches);
                        foreach($matches[0] as $k => $match){
                            if($k % 2 === 0){
                                $row['introtext'] = str_replace('"'.$match.'"', '"'.$match.'" class="notranslate"', $row['introtext']);
                            }
                        }
                    }
                 }

			    //Translate the meta description and the meta keywords of the menu item
			    if($adapter->table === 'menu'){
                    $menuParams = json_decode($row['params'], true);
                    if(isset($menuParams['menu-meta_description'])){
                        $menuParams['menu-meta_description'] = $this->translate($menuParams['menu-meta_description']);
                    }
                    if(isset($menuParams['menu-meta_keywords'])){
                        $menuParams['menu-meta_keywords'] = $this->translate($menuParams['menu-meta_keywords']);
                    }
                    $row['params'] = json_encode($menuParams); 
			    }

				$sourceid = $row[$adapter->primarykey];
				if($adapter->table_type == 'alias') {
					$newalias = $adapter->getNewAlias($row[$alias], $this->fromLangTag, $this->toLangTag, $row, 'alias', false);
					$queryAlias = $db->getQuery(true);
					$queryAlias->select($adapter->primarykey)->from($from_table)
						->where($db->quoteName($alias) .'='.$db->quote($newalias));
					$db->setQuery($queryAlias);
					$newid = $db->loadResult();
				} else {
					$newid = $this->getAssociatedItem($adapter->table, $sourceid, null);
				}

				if(!$newid) {
					$count++;
					$title = isset($row[$adapter->title_field]) ? $row[$adapter->title_field] : '#'.$sourceid;
					$this->sendOutput(str_pad('', 5, '-').' '.JText::sprintf('START_TRANSLATING_VAR_VAR', $adapter->table, $title));
					$iFields = array();
					$sentences = array();

					foreach ($fields as $f) {
						$sentences[] = $row[$f];
					}
					if ($this->from != $this->to) {
						$translated = $this->translateArray($sentences, array_keys($fields));
					} else {
						//clone content if two language has the same language code (E.g: en-GB, en-US or en-AU)
						$translated = $sentences;
					}

					if (!is_array($translated) || !count($translated)) {
						$this->sendOutput('<span class="failed">'.JText::_('FAILED').'</span>: '.JText::sprintf('FAILED_TRANSLATE_VAR', $this->getError()));
					} else {

						if(isset($row[$langField])) {
							//language field
							if($adapter->language_mode == 'id') {
								$iFields[$langField] = JalangHelper::getLanguageIdFromCode($this->toLangTag);
							} else {
								// update the translated item to default (in this case is from) language. Ignore if the current item is default menu item
								/*if($row['language'] == '*') {
									$query = "UPDATE {$table} SET `language` = ".$db->quote($this->fromLangTag)." WHERE `{$adapter->primarykey}` = ".$sourceid;
								}*/
								$iFields[$langField] = $this->toLangTag;
							}
						}

						// item id for table translate
						if ($adapter->table_type == 'table' || $adapter->table_type == 'table_ml') {
							$newid = $row[$adapter->primarykey];
							//in case of database structure for multilingual is designed by using multiple tables for each language
							//then associated items in each tables will have the same id
							$iFields[$adapter->primarykey] = $newid;
						}

						//nested field
						if($adapter->nested_field) {
							$iFields[$adapter->nested_field] = $this->getAssociatedItem ($adapter->table, $row[$adapter->nested_field], $row[$adapter->nested_field]);
						}

						if (is_array($adapter->reference_fields)) {
							foreach ($adapter->reference_fields as $rel_field => $rel_table) {
								$iFields[$rel_field] = $this->getAssociatedItem ($rel_table, $row[$rel_field], $row[$rel_field]);
							}
						}

						//translated fields
						$i=0;
						foreach ($fields as $f) {
							$iFields[$f] = $translated[$i++];
						}

						//clone other fields
						foreach ($row as $of => $ov) {
							if($of != $adapter->primarykey && $of != $adapter->alias_field && !isset($iFields[$of])) {
								$iFields[$of] = $ov;
							}
						}

						//alias
						if(!empty($alias)) {
							$iFields[$alias] = $adapter->getNewAlias($row[$alias], $this->fromLangTag, $this->toLangTag, $iFields, '', true);
						}

						//prepare data
						$adapter->beforeSave($this, $sourceid, $iFields);

						$queryInsert = $db->getQuery(true);
						$queryInsert->insert($to_table);

						//columns
						$colums = array_keys($iFields);
						foreach($colums as &$val) {
							$val = $db->quoteName($val);
						}
						$queryInsert->columns($colums);
						//values
						$values = array_values($iFields);
						foreach($values as &$val){
                            $val = str_replace('&lt; ','<',$val);
                            $val = str_replace(' &gt;','>',$val);
                            $val = preg_replace('/&lt; ?\/ ?/','</',$val);
                            $val = $db->quote($val);
                        }

						$queryInsert->values(implode(',', $values));
						$db->setQuery($queryInsert);
						$result = $db->execute();
						if(!$result) {
							$this->sendOutput('<span class="failed">'.JText::_('FAILED').'</span>'.$db->getErrorMsg());
							continue;
						}

						if ($adapter->table_type != 'table') {
							$newid = $db->insertid();
							$iFields[$adapter->primarykey] = $newid;
						}

						if ($adapter->table_type != 'table' && $adapter->table_type != 'table_ml') {
							$this->addAssociate ($adapter->table, $context, $row[$adapter->primarykey], $newid);
						}

						if($newid) {
                            if(isset($elementid) && !in_array($newid, $elementid)) array_push($elementid, $newid); 
							$adapter->afterSave($this, $sourceid, $iFields);
						}

                        //Update Easyblog_post_category table if EasyBlog 5.x
                        if($adapter->table == 'easyblog_post'){
                            $xml = JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_easyblog/easyblog.xml');
                            $version = (string)$xml->version;
                            if((int)$version >= (int)'5.0.30'){
                                $sql_e = $db->getQuery(true);
                                $sql_e ->select('id, category_id')
                                       ->from($db->quoteName($table))
                                       ->where('id='.$db->quote($newid));
                                $db->setQuery($sql_e);
                                $ePost = $db->loadObject();

                                if($ePost){
                                    $sql_e->clear();
                                    $sql_e->insert('#__easyblog_post_category')
                                          ->columns(array($db->quoteName('post_id'),$db->quoteName('category_id'),$db->quoteName('primary')))
                                          ->values(implode(',',array($db->quote($ePost->id),$db->quote($ePost->category_id),1)));
                                    $db->setQuery($sql_e);
                                    $db->execute();  
                                }
                            }
                        } 
						$this->sendOutput('<span class="success">'.JText::_('SUCCESS').'</span>');
					}

				} else {
					/**
					 * @todo update existing items?
					 */
                     if(count($re_translate)){
                        if(preg_match('/content|easyblog_post|mijoshop_product_description|k2_items|virtuemart_products/', $adapter->table)){
                           if(isset($elementid) && !in_array($newid, $elementid)) array_push($elementid, $newid); 
                           $count++;
					       $title = isset($row[$adapter->title_field]) ? $row[$adapter->title_field] : '#'.$sourceid;
					       $this->sendOutput(str_pad('', 5, '-').' '.JText::sprintf('START_RE_TRANSLATING_VAR_VAR', $adapter->table, $title));
					       $iFields = array();
					       $sentences = array();

					       foreach ($fields as $f) {
						      $sentences[] = $row[$f];
					       }
					       if ($this->from != $this->to) {
						      $translated = $this->translateArray($sentences, array_keys($fields));
					       } else {
						      //clone content if two language has the same language code (E.g: en-GB, en-US or en-AU)
						      $translated = $sentences;
					       }

					       if (!is_array($translated) || !count($translated)) {
						      $this->sendOutput('<span class="failed">'.JText::_('FAILED').'</span>: '.JText::sprintf('FAILED_TRANSLATE_VAR', $this->getError()));
					       } else {

						      if(isset($row[$langField])) {
							     //language field
							     if($adapter->language_mode == 'id') {
								    $iFields[$langField] = JalangHelper::getLanguageIdFromCode($this->toLangTag);
							     } else {
				                    // update the translated item to default (in this case is from) language. Ignore if the current item is default menu item
                                    $iFields[$langField] = $this->toLangTag;
							     }
						      }

						      //nested field
						      if($adapter->nested_field) {
							     $iFields[$adapter->nested_field] = $this->getAssociatedItem ($adapter->table, $row[$adapter->nested_field], $row[$adapter->nested_field]);
						      }

						      if (is_array($adapter->reference_fields)) {
							     foreach ($adapter->reference_fields as $rel_field => $rel_table) {
								    $iFields[$rel_field] = $this->getAssociatedItem ($rel_table, $row[$rel_field], $row[$rel_field]);
							     }
						      }

						      //translated fields
						      $i=0;
						      foreach ($fields as $f) {
							     $iFields[$f] = $translated[$i++];
						      }

						      //clone other fields
						      foreach ($row as $of => $ov) {
							     if($of != $adapter->primarykey && $of != $adapter->alias_field && !isset($iFields[$of])) {
								    $iFields[$of] = $ov;
							     }
						      }

						      //prepare data
						      $adapter->beforeSave($this, $sourceid, $iFields);

						      $queryUpdate = $db->getQuery(true);
						      $queryUpdate->update($to_table);

						      //columns
						      $colums = array_keys($iFields);
						      foreach($colums as &$val) {
							     $val = $db->quoteName($val);
						      }

						      //values
						      $values = array_values($iFields);
                              foreach($values as &$val){
                                $val = str_replace('&lt; ','<',$val);
                                $val = str_replace(' &gt;','>',$val);
                                $val = preg_replace('/&lt; ?\/ ?/','</',$val);
                                $val = $db->quote($val);
                              }
                              $query_setvalues = array();
                              foreach($colums as $ckey => $column){
                                foreach ($values as $vkey => $value){
                                    if($ckey == $vkey){
                                        $query_setvalues[] = $column.'='.$value;
                                    }
                                }
                              }

						      $queryUpdate->set(implode(',', $query_setvalues));
                              switch($adapter->table){
                                case 'mijoshop_product_description' :
                                    $language = 'language_id';
                                    break;
                                case 'virtuemart_products':
                                    break;
                                case 'content':
                                default:
                                    $language = 'language';
                                    $iFields[$language] = $db->quote($iFields[$language]);
                                    break;
                              }
                              if($adapter->table != 'virtuemart_products'){
                              	$queryUpdate->where($db->quoteName($adapter->primarykey).'='.(int) $newid.' AND '.$db->quoteName($language).'='.$iFields[$langField]);  
                              }else{
								$queryUpdate->where($db->quoteName($adapter->primarykey).'='.(int) $newid);
                              }

                              $db->setQuery($queryUpdate);
						      $result = $db->execute();

						      if(!$result) {
							     $this->sendOutput('<span class="failed">'.JText::_('FAILED').'</span>'.$db->getErrorMsg());
							     continue;
						      }

						      $this->sendOutput('<span class="success">'.JText::_('SUCCESS').'</span>');
                              if($adapter->table == 'easyblog_post') {
								$xml = JFactory::getXml(JPATH_ADMINISTRATOR .'/components/com_easyblog/easyblog.xml');
								$version = (string)$xml->version;
								if((int)$version >= (int)'5.0.30') {
									$adapter->afterRetranslate($this, $newid, $iFields);
								}
                              }
					       }
                        }
                    }
				}

				if($adapter->nested_field) {
					//backup data before call recursive
					$nested_value = $adapter->nested_value;
					$adapter->nested_value = $row[$adapter->primarykey];
					$this->translateTable($adapter->table, $this->fromLangTag, $this->toLangTag, $adapter, $count);
					$adapter->nested_value = $nested_value;
				}

			}
			//only fire afterTranslate event if has new items
			if($firstRun && $count) {
				$this->sendOutput(JText::sprintf('RUN_REGISTERED_TASK_AFTER_TRANSLATE_TABLE_VAR', $adapter->table));
				$adapter->afterTranslate($this);
			}
		}
		//Get the translate time.
		if(preg_match('/content|easyblog_post|mijoshop_product_description|k2_items|virtuemart_products/', $adapter->table)){
			$date = JFactory::getDate();
			if ($adapter->table == 'mijoshop_product_description')
				// only mijoshop using server time.we should get the timezone from mijoshop.
				$date_translated = 'NOW()';
			else
				$date_translated = $db->quote($date->toSql());

			//If the item was translated before install JA Multilingual component.
			if(count($elementid) === 0){
				$query_element = $db->getQuery(true);
				switch ($adapter->table){
					case 'mijoshop_product_description' :
						$query_element -> select($db->quoteName('product_id'))
									   -> from ($db->quoteName($table))
									   -> where($db->quoteName('language_id').'='.$db->quote(JalangHelper::getLanguageIdFromCode($this->toLangTag)));
						break;
					case 'virtuemart_products' :
						$query_element -> select($db->quoteName('virtuemart_product_id'))
									   -> from ($db->quoteName($table.'_'.strtolower(str_replace('-','_',$this->toLangTag))));
						break;
					default: 
						$query_element -> select($db->quoteName('id'))
									   -> from ($db->quoteName($table))
									   -> where ($db->quoteName('language') .'='. $db->quote($this->toLangTag));
						break;
				}
				$db->setQuery($query_element);
				$elementid = $db->loadColumn();
				$element = $elementid;
			}
			
			//Save the translate time
			$queryCheck = $db->getQuery(true);
			$queryCheck -> select('jal.id')
						-> from('#__jalang_translated AS jal')
						-> where($db->quoteName('adapter') . ' = '. $db->quote($adapter->table).' AND '.$db->quoteName('language').'='.$db->quote($this->toLangTag));
			$db->setQuery($queryCheck);
			$check = $db->loadResult();
			if(!$check){
				$sqlInsert = $db->getQuery(true);
				$columsInsert = array('adapter','elementid','date_translate','language');
				foreach($columsInsert as &$c){
					$c = $db->quoteName($c);
				}
			
				$element = implode(',', $elementid);
				$valueInserts = array ($db->quote($adapter->table), $db->quote($element), $date_translated, $db->quote($this->toLangTag));
				$sqlInsert -> insert($db->quoteName('#__jalang_translated'))
						   -> columns($columsInsert)
						   -> values(implode(',',$valueInserts));
				$db->setQuery($sqlInsert);
				$db->execute();
			}else{
				$element = implode(',', $elementid);
				$sqlUpdate = $db->getQuery(true);
				$sqlUpdate -> update('#__jalang_translated')
						   -> where($db->quoteName('adapter') . ' = '. $db->quote($adapter->table).' AND language = '.$db->quote($this->toLangTag));
				if(in_array($adapter->table, $re_translate)){
					$sqlUpdate -> set(array($db->quoteName('elementid'). ' = '. $db->quote($element), $db->quoteName('date_translate') . ' = '. $date_translated));
				}else{
					$sqlUpdate -> set(array($db->quoteName('elementid'). ' = '. $db->quote($element)));
				}
				$db->setQuery($sqlUpdate);
				$db->execute();
			}

			if ($adapter->table == 'mijoshop_product_description') {
				// update mijoshop new product date_modified after translate.only mijoshop new product date_modified is 0000-00-00 00:00:00.
				$mjupdate = $db->getQuery(true);
				$mjupdate->update('#__mijoshop_product')
							->where($db->quoteName('date_modified').'= "0000-00-00 00:00:00"')
							->set(array($db->quoteName('date_modified'). ' = NOW()'));
				$db->setQuery($mjupdate);
				$db->execute();
			}
		}
	}

	public function sendOutput($content) {
		echo $content . "<br />";
		@ob_flush();
		@flush();
		/*@ob_end_flush();
		@ob_flush();
		@flush();
		@ob_start();*/
	}

	/**
	 * @param string $langTag - tag code of Joomla language content
	 * @return string - corresponding code of given language in Translation service
	 */
	public function getLangCode($langTag) {
		if($langTag == '*') {
			$langTag = JalangHelper::getDefaultLanguage();

			$this->convertLangTag = $langTag;
		}
		$parts = explode('-', $langTag);
		$code = strtolower(trim($parts[0]));
		return $code;
	}

	/**
	 * @param string $table - table name
	 * @param string $languageTag - Joomla content language tag
	 */
	public function getLangTable($table, $languageTag) {
		return $table . '_' . strtolower(str_replace('-', '_', $languageTag));
	}

	public function createLangTable($table, $languageTag) {
		if($languageTag == 'en-GB' || $languageTag == '*') return;//no need create tables for en-GB, since they are existed

		$to_table = $this->getLangTable($table, $languageTag);

		$db = JFactory::getDbo();
		// check if table $to_table existed
		$tables = $db->getTableList();
		$tname = str_replace('#__', $db->getPrefix(), $to_table);

		if (!in_array($tname, $tables)) {
			$queryCreate = 'CREATE TABLE '.$db->quoteName($to_table).' LIKE '.$db->quoteName($this->getLangTable($table, 'en-GB'));
			$db->setQuery($queryCreate);
			$db->execute();
		}
	}


	public function loadAssociate ($table, $id='id', $context, $reload = false, $filter = array()) {
		$defaultLanguage = JalangHelper::getDefaultLanguage();
		if($this->fromLangTag == '*' && $this->convertLangTag) {
			$fromLangTag = $this->convertLangTag;
		} else {
			$fromLangTag = $this->fromLangTag;
		}

		$adapter = JalangHelperContent::getInstance($table);
		if($adapter->table_type == 'table') {
			$this->createLangTable('#__'.$table, $fromLangTag);
			$this->createLangTable('#__'.$table, $this->toLangTag);
		}

		if(!isset($this->aAssociation[$table]) || $reload || ($adapter->table_type == 'table')) {
			if(!isset($this->aAssociation[$table])) {
				$this->aAssociation[$table] = array();
			}
			$aMap = &$this->aAssociation[$table];

			$db = JFactory::getDbo();

			if($adapter->table_type == 'table') {

				$from_table = $this->getLangTable('#__'.$table, $fromLangTag);
				$to_table = $this->getLangTable('#__'.$table, $this->toLangTag);

				$query = $db->getQuery(true);
				$query->select('st.'.$adapter->primarykey.' AS sourceid');
				$query->from($from_table .' AS st');
				if (!preg_match('/virtuemart/', $table)) {
					$query->select('dt.'.$adapter->primarykey.' AS newid');
					$query->innerJoin($to_table. ' AS dt ON (st.'.$adapter->primarykey.' = dt.'.$adapter->primarykey.')');
				}
				if(count($filter)) {
					$query->where($filter);
				}

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				if(count($rows)) {
					foreach($rows as $row) {
						if(!isset($aMap[$row->sourceid])) $aMap[$row->sourceid] = array();
						$aMap[$row->sourceid][$fromLangTag] = $row->sourceid;
						$aMap[$row->sourceid][$this->toLangTag] = !empty($row->newid) ? $row->newid : $row->sourceid;
					}
				}

			} elseif($adapter->table_type == 'table_ml') {
				$query = $db->getQuery(true);
				// content association
				$langField = $db->quoteName($adapter->language_field);

				$query->select('a.'.$adapter->primarykey." AS sourceid, GROUP_CONCAT(l.lang_code, ',', a.{$adapter->primarykey} SEPARATOR '|') AS `data`")
					->from('#__'.$adapter->table.' AS a');
				if($adapter->language_mode == 'id') {
					$query->innerJoin('#__languages AS l ON l.lang_id = a.'.$adapter->language_field);
				} else {
					$query->innerJoin('#__languages AS l ON l.lang_code = a.'.$adapter->language_field);
				}
				$query->group('a.'.$adapter->primarykey);
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if(count($rows)) {
					foreach($rows as $row) {
						if(!$row->data) continue;
						$aMap[$row->sourceid] = array();
						$data = explode('|', $row->data);
						$assoc = array();
						foreach($data as $d) {
							list($language, $contentid) = explode(',', $d);
							if($language != $fromLangTag) {
								$aMap[$row->sourceid][$language] = $contentid;
							}
						}
					}
				}
			} elseif($adapter->table_type == 'alias') {
				$query	= $db->getQuery(true);
				$query->select('a.lang_code, a.title, a.title_native')
					->from('#__languages AS a');
				$db->setQuery($query);
				$list = $db->loadObjectList();
				$languages = array();
				foreach($list as $item) {
					$lang_code = preg_replace('/\-.*/', '', $item->lang_code);
					$languages[$lang_code] = $item;
				}
				//

				$query = $db->getQuery(true);
				$query->select('*')->from('#__'.$adapter->table);

				if($this->fromLangTag != '*') {
					$lang_code = preg_replace('/\-.*/', '', $this->fromLangTag);
					if($this->fromLangTag == $defaultLanguage) {
						$query->where('('.$db->quoteName($adapter->alias_field).' LIKE '.$db->quote('%-'.$lang_code).' OR '.$db->quoteName($adapter->alias_field).' NOT REGEXP '.$db->quote('-[a-z]{2}$').')');
					} else {
						$query->where($db->quoteName($adapter->alias_field).' LIKE '.$db->quote('%-'.$lang_code));
					}
				} else {
					$query->where($db->quoteName($adapter->alias_field).' NOT REGEXP '.$db->quote('-[a-z]{2}$'));
				}
				$db->setQuery($query);
				$rows = $db->loadObjectList();

				if(count($rows)) {
					foreach($rows as $row) {
						$sourceid = $row->{$adapter->primarykey};
						if(!isset($aMap[$sourceid])) $aMap[$sourceid] = array();
						$aMap[$sourceid][$this->fromLangTag] = $sourceid;

						$alias = $row->{$adapter->alias_field};
						if($this->fromLangTag != '*') {
							$lang_code = preg_replace('/\-.*/', '', $this->fromLangTag);
							$alias = preg_replace('/\-'.$lang_code.'$/', '', $alias);
						}

						$query = $db->getQuery(true);
						$query->select('*')->from('#__'.$adapter->table);
						$where = array();
						$where[] = $db->quoteName($adapter->alias_field).' = '.$db->quote($alias);
						$where[] = $db->quoteName($adapter->alias_field).' REGEXP '.$db->quote($alias.'-[a-z]{2}$');
						$query->where($where, 'OR');

						$db->setQuery($query);
						$rows2 = $db->loadObjectList();

						if(count($rows2)) {
							foreach($rows2 as $row2) {
								if($alias == $row2->{$adapter->alias_field}) {
									$aMap[$sourceid]['*'] = $row2->{$adapter->primarykey};
									if(!isset($aMap[$sourceid][$defaultLanguage])) {
										$aMap[$sourceid][$defaultLanguage] = $row2->{$adapter->primarykey};
									}
								} else {
									foreach($languages as $lang_code => $item) {
										if($alias . '-'.$lang_code == $row2->{$adapter->alias_field}) {
											$aMap[$sourceid][$item->lang_code] = $row2->{$adapter->primarykey};
										}
									}
								}
							}
						}
					}
				}
			} else {
				//table_type = native
				$query = $db->getQuery(true);
				// content association
				$langField = $db->quoteName($adapter->language_field);
				$query->select("a.key, GROUP_CONCAT(c.{$langField}, ',', c.id SEPARATOR '|') AS `data`");
				$query->from('#__associations AS a');
				$query->innerJoin('#__'.$table. ' AS c ON  (a.id=c.'.$id.' AND a.context = '.$db->quote($context).')');
				$query->group('a.key');

				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if(count($rows)) {
					foreach($rows as $row) {
						if(!$row->data) continue;
						$data = explode('|', $row->data);
						$assoc = array();
						foreach($data as $d) {
							list($language, $contentid) = explode(',', $d);
							$assoc[$language] = $contentid;
							if($language == '*' && !isset($assoc[$defaultLanguage])) {
								$assoc[$defaultLanguage] = $contentid;
							}
						}
						if(isset($assoc[$fromLangTag])) {
							$aMap[$assoc[$fromLangTag]] = $assoc;
						}
					}
				}
			}
		}

		//return @$this->aAssociation[$table];
	}

	public function getAssociatedItem($table, $sourceid, $default = null) {
		if (isset($this->aAssociation[$table]) && isset($this->aAssociation[$table][$sourceid])) {
			foreach($this->aAssociation[$table][$sourceid] as $lang => $itemid) {
				if($this->toLangTag == $lang) {
					return $itemid;
				}
			}
		}

		return $default;
	}

	public function getTranslatedItems($table) {
		$list = array();
		if (isset($this->aAssociation[$table]) && count($this->aAssociation[$table])) {
			foreach($this->aAssociation[$table] as $sourceid => $assoc) {
				if(isset($assoc[$this->toLangTag])) {
					$list[] = $sourceid;
				}
			}
		}
		return $list;
	}

	private function addAssociate ($table, $context, $sourceid, $newid) {
		$db = JFactory::getDbo();
		$associations = array();
		if (isset($this->aAssociation[$table]) && isset($this->aAssociation[$table][$sourceid])) {
			$associations = $this->aAssociation[$table][$sourceid];
		}

		//create associations
		$associations[$this->fromLangTag] = $sourceid;
		$associations[$this->toLangTag] = $newid;

		$associations = array_unique($associations);

		//delete old asociation before create new ones
		$query = $db->getQuery(true);
		$query->delete('#__associations');
		$query->where($db->quoteName('context') .'='.$db->quote($context));
		$query->where($db->quoteName('id') . ' IN (' . implode(',', $associations) . ')');
		$db->setQuery($query);
		$db->execute();

		//update associations
		$key = md5(json_encode($associations));

		$query = $db->getQuery(true);
		$query->insert('#__associations');
		$query->columns(array($db->quoteName('id'), $db->quoteName('context'), $db->quoteName('key')));
		foreach ($associations as $language => $itemid) {
			$query->values($db->quote($itemid).','.$db->quote($context).','.$db->quote($key));
		}
		$db->setQuery($query);
		$db->execute();

		// update map array
		$this->aAssociation[$table][$sourceid] = $associations;
	}

	public function updateTemplateStyles () {
		// get all template styles
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__template_styles')->where('client_id=0');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$mm_config = null;
		$fromAlias = JalangHelper::getAliasSuffix($this->fromLangTag);
		foreach ($rows as $row) {
			$params = json_decode($row->params, true);
			if (!$params || !isset($params['mm_config'])) continue;

			$mmca = json_decode($params['mm_config'], true);
			$update = 0;
			if (is_array($mmca)) {
				foreach ($mmca as $mmt => $mmc) {
					if (preg_match('/-'.$this->from.'$/', $mmt) || !preg_match('/-[a-z]{2}$/', $mmt)) {
						//convert from configuration of megamenu in default language
						$mmt = preg_replace('/-('.$this->from.'|'.$fromAlias.')$/', '', $mmt);
						$mmc1 = json_encode($mmc);
						$mmt2 = $mmt.'-'.JalangHelper::getAliasSuffix($this->toLangTag);
						//if(isset($mmca[$mmt2])) continue;
						$mmc2 = preg_replace_callback ('/(")(item|position)(["\-:]+)(\d+)([^\d]?)/', array($this, 'updateTemplateStyles_callback'), $mmc1);
						$mmca[$mmt2] = json_decode($mmc2, true);
						$update = 1;
					}
				}
			}
			if($update) {
				$mm_config = json_encode($mmca);
				$params['mm_config'] = $mm_config;
				// update template style
				$query->clear();
				$query->update('#__template_styles')->set($db->quoteName('params').'='.$db->quote(json_encode($params)))
					->where('`id`=' . $row->id);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	public function updateTemplateStyles_callback ($matches) {
		$oldid = $matches[4];
		$table = $matches[2]=='item' ? 'menu' : 'modules';
		$newid = $this->getAssociatedItem($table, $oldid, $oldid);
		return $matches[1].$matches[2].$matches[3].$newid.$matches[5];
	}
}