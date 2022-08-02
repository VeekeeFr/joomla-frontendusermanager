<?php

/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
/**
 * View class for a list of Frontendusermanager.
 */
class FrontendusermanagerViewUserlist extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;
	protected $model;
	protected $criteria;
	protected $excludedFields;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		$user = Factory::getUser();
        $app = Factory::getApplication();

		$authorised = $user->authorise('core.manage', 'com_users');
		$authorised = $authorised || $user->authorise('core.manage', 'com_frontendusermanager');
		if ($authorised !== true)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->criteria = $this->get('Criteria');
		
        $this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
        $this->params = ComponentHelper::getParams('com_frontendusermanager');
		
		$this->items = $this->get('Items');		
		$this->excludedFields = $this->get('ExcludedFields');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');		
		

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
;
            throw new Exception(implode("\n", $errors));
        }

		FumHelpersBackend::addSubmenu('userlist');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		
        parent::display($tpl);
    }

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = FumHelpersForm::getActions();

		JToolBarHelper::title(Text::_('COM_FRONTENDUSERMANAGER_TITLE_USERLIST'), 'userlist.png');

		if ($canDo->get('core.manage'))
		{
			ToolBarHelper::custom('userlist.export', 'download', '', Text::_('COM_FRONTENDUSERMANAGER_USERLIST_EXPORT'), false);
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'criterias.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('criterias.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_frontendusermanager');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_frontendusermanager&view=criterias');

		$this->extra_sidebar = '';
		//Filter for the field managers_list
		$this->extra_sidebar .= '<div class="other-filters">';
		$this->extra_sidebar .= '<small><label for="filter_managers_list">Managers List</label></small>';
		$this->extra_sidebar .= JHtmlList::users('filter_managers_list', $this->state->get('filter.managers_list'), 1, 'onchange="this.form.submit();"');
		$this->extra_sidebar .= '</div>';
		$this->extra_sidebar .= '<hr class="hr-condensed">';

		JHtmlSidebar::addFilter(

			Text::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);
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
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', Text::_('COM_FRONTENDUSERMANAGER_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
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
