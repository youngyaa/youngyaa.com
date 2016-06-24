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

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_easyblog/models/blogs.php')) {
	//Register if K2 is installed
	JalangHelperContent::registerAdapter(
		__FILE__,
		'easyblog_post',
		4,
		JText::_('EASYBLOG_ENTRIES'),
		JText::_('EASYBLOG_ENTRIES')
	);

	//require_once( JPATH_ADMINISTRATOR . '/components/com_easyblog/models/blogs.php' );
	jimport('joomla.filesystem.file');

	class JalangHelperContentEasyblogPost extends JalangHelperContent
	{
		public function __construct($config = array())
		{
			$this->table = 'easyblog_post';
			$this->edit_context = 'com_easyblog.edit.item';
			$this->associate_context = 'com_easyblog.item';
			$this->alias_field = 'permalink';
			$this->translate_fields = array('title', 'content', 'intro', 'excerpt');
			/**
			 * @TODO anable reference field to category when translate easyblog category task is enabled
			 */
			//$this->reference_fields = array('category_id'=>'easyblog_category');
			//$xml = JFactory::getXml(JPATH_ADMINISTRATOR .'/components/com_easyblog/easyblog.xml');
            //$version = (string)$xml->version;
            $this->translate_filters = array('ispending = 0');
			parent::__construct($config);
		}

		public function getEditLink($id) {
			if($this->checkout($id)) {
			  $xml = JFactory::getXml(JPATH_ADMINISTRATOR .'/components/com_easyblog/easyblog.xml');
                 $version = (string)$xml->version;
                 if((int)$version >= 5.0){
                    return 'index.php?option=com_easyblog&c=blogs&view=composer&tmpl=component&uid='.$id;
                 }else{
                    return 'index.php?option=com_easyblog&c=blogs&task=edit&blogid='.$id;
                 }
			}
			return false;
		}

		/**
		 * Returns an array of fields the table can be sorted by
		 */
		public function getSortFields()
		{
			return array(
				'a.title' => JText::_('JGLOBAL_TITLE'),
				'language' => JText::_('JGRID_HEADING_LANGUAGE'),
				'a.id' => JText::_('JGRID_HEADING_ID')
			);
		}

		/**
		 * Returns an array of fields will be displayed in the table list
		 */
		public function getDisplayFields()
		{
			return array(
				'a.id' => 'JGRID_HEADING_ID',
				'a.title' => 'JGLOBAL_TITLE'
			);
		}

		public function afterSave(&$translator, $sourceid, &$row) {
			//clone tag
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('tag_id')->from('#__easyblog_post_tag')->where('post_id='.$db->quote($sourceid));
			$db->setQuery($query);
			$items = $db->loadObjectList();

			if(count($items)) {
				$targetid = $row[$this->primarykey];
				$date = JFactory::getDate()->toSql();
				$query->clear();
				$query->delete('#__easyblog_post_tag')->where('post_id='.$db->quote($targetid));
				$db->setQuery($query);
				$db->execute();

				foreach ($items as $item) {

					$query->clear();
					$query->insert('#__easyblog_post_tag')->columns('tag_id, post_id, created');
					$query->values($db->quote($item->tag_id).','.$db->quote($targetid).','.$db->quote($date));

					$db->setQuery($query);
					$db->execute();
				}
			}

			//check & update featured entry
			$query->clear();
			$query->select('id')->from('#__easyblog_featured')->where('type="post" AND content_id='.$db->quote($sourceid));
			$db->setQuery($query);
			$item = $db->loadResult();
			if($item){
				$date = JFactory::getDate()->toSql();
				$query->clear();
				$query->insert('#__easyblog_featured')->columns('content_id, type, created');
				$query->values($db->quote($row['id']).',"post",'.$db->quote($date));
				$db->setQuery($query);
				$db->execute();
			}

			// Update language tag from * to default language.
			$default_lang = JFactory::getLanguage();
			$update_lang = $db->getQuery(true);
			$update_lang->update('#__easyblog_post')->set('language="'.$default_lang->getTag().'"')->where('id = '.$sourceid)->where('language = "*"');
			$db->setQuery($update_lang);
			$db->execute();
			// update language tag for revision. // wil be update to new query.
			$update_lang_rev = 'UPDATE 
				`#__easyblog_revisions`
				SET `content` = REPLACE(`content`, \'"language":"*"\', \'"language":"'.$default_lang->getTag().'"\') 
				WHERE `post_id` = '.$sourceid.' ORDER BY `id` DESC LIMIT 1';
			$db->setQuery($update_lang_rev);
			$db->execute();
			// end update language tag.

            //Update Revision if EasyBlog ver 5.x
            $xml = JFactory::getXml(JPATH_ADMINISTRATOR .'/components/com_easyblog/easyblog.xml');
            $version = (string)$xml->version;
            if((int)$version >= (int)'5.0.30'){
                $this->updateRevision($row);
            }

			//
			parent::afterSave($translator, $sourceid, $row);
		}

        public function afterRetranslate($translator, $sourceid, &$row)
        {
            // Add source ID for blog entry
            $row['id'] = $sourceid;
            $row['language'] = str_replace(array('\'', '"'),array('',''),$row['language']); // remove quote for language tag.
            $this->updateRevision($row);
        }

        public function updateRevision($row){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->insert('#__easyblog_revisions')->columns('post_id, title, created, modified, created_by, content, state,ordering');
            $values = array();
            $values['post_id'] = $row['id'];
            $values['title'] = 'Initial Post';
            $values['created'] = $row['created'];
            $values['modified'] = $row['modified'];
            $values['created_by'] = $row['created_by'];
            $values['content'] = json_encode($row);
            $values['state'] = $row['state'];

            //ordering
            $sql_or = $db->getQuery(true);
            $sql_or = "SELECT MAX(ordering) FROM #__easyblog_revisions WHERE post_id = ".$db->quote($row['id']);
            $db->setQuery($sql_or);
            $ordering = $db->loadResult();
            if(!$ordering){
                $values['ordering'] = $row['ordering'];
            }else{
                $values['ordering'] = (int)$ordering + 1;
            }

            foreach($values as $v=>$value){
                $values[$v] = $db->quote($value);
            }
            $query->values(implode(',',$values));
            $db->setQuery($query);
            $db->execute();

            $query->clear();
            $query->select('id')->from('#__easyblog_revisions')->where($db->quoteName('post_id').' = '.$db->quote($row['id']))->order('modified DESC');
            $db->setQuery($query);
            $revisionId = $db->loadResult();

            $query->clear();
            $query->update('#__easyblog_post')->set($db->quoteName('revision_id').' = '.$db->quote($revisionId))->where($db->quoteName('id') . ' = '.(int)($row['id']));
            $db->setQuery($query);
            $db->execute();
        }
	}
}
