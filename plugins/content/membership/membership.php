<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class plgContentMembership extends JPlugin
{
	/**
	 * @param    JForm $form The form to be altered.
	 * @param    array $data The associated data for the form.
	 *
	 * @return    boolean
	 * @since    2.1.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!$this->isComponentAvailable() && JFactory::getApplication()->isSite())
		{
			return;
		}

		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		$name = $form->getName();

		if ($name == 'com_content.article')
		{
			JForm::addFormPath(dirname(__FILE__) . '/form');
			$form->loadFile('membership', false);
		}

		return true;
	}

	/**
	 * @param $context
	 * @param $article
	 * @param $isNew
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		if ($context != 'com_content.article')
		{
			return true;
		}

		$articleId = $article->id;
		$input     = JFactory::getApplication()->input;
		$data      = $input->get('jform', array(), 'array');
		if ($articleId)
		{
			try
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->delete('#__osmembership_articles');
				$query->where('article_id = ' . $db->Quote($articleId));
				$db->setQuery($query);
				$db->execute();
				if (!empty($data['plan_ids']))
				{
					$query->clear();
					$query->insert('#__osmembership_articles');
					$query->columns('plan_id,article_id');
					foreach ($data['plan_ids'] as $planId)
					{
						$query->values("$planId,$articleId");
					}
					$db->setQuery($query);
					$db->execute();
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}
		}
	}

	/**
	 * @param    string $context The context for the data
	 * @param    object $data    The user id
	 *
	 * @return    boolean
	 * @since    2.1.0
	 */
	public function onContentPrepareData($context, $data)
	{
		if ($context != 'com_content.article')
		{
			return true;
		}

		if (is_object($data))
		{
			$articleId = isset($data->id) ? $data->id : 0;
			if ($articleId > 0)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('plan_id');
				$query->from('#__osmembership_articles');
				$query->where('article_id = ' . $db->Quote($articleId));
				$db->setQuery($query);
				$results = $db->loadColumn();
				$data->set('plan_ids', $results);
			}
		}
	}

	/**
	 * Check if membership pro extension is installed
	 *
	 * @return bool
	 */
	private function isComponentAvailable()
	{
		return file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php');
	}
}
