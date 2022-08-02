<?php
/**
 * @package    FrontendUserManager
 *
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright   Copyright (C) 2015. Hepta Technologies SL All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.hepta.es
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/helpers/html');
/**
 * View class for a list of Frontendusermanager.
 * @since 0.0.1
 */
class FrontendusermanagerViewUsermanagers extends JViewLegacy
{
	protected $criteria;
	protected $excludedFields;
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;
	protected $model;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->criteria = $this->get('Criteria');

		if (!empty($this->criteria))
		{
			$authorised = true;
		}
		else
		{
			$authorised = $user->authorise('core.manage', 'com_frontendusermanager');
		}

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_frontendusermanager');

		$this->items = $this->get('Items');
		$this->excludedFields = $this->get('ExcludedFields');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;
		JHtml::_('jquery.framework');

		JHtml::script('com_frontendusermanager/footable/footable.min.js', false, true);
		JHtml::stylesheet('com_frontendusermanager/footable/footable.standalone.min.css', false, true, false, false, true);

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_FRONTENDUSERMANAGER_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	public function loadExtraLanguageFiles($extension)
	{
		$lang = JFactory::getLanguage();
		$languageTag = $lang->getTag();
		$lang->load($extension, JPATH_SITE, $languageTag, true);
	}

}
