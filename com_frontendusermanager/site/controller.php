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

jimport('joomla.application.component.controller');

class FrontendusermanagerController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
		JLoader::registerPrefix('Fum', JPATH_COMPONENT_ADMINISTRATOR);

		FumHelpersForm::loadProfileStrings();

		$user = JFactory::getUser();
/*
		if(!$user->authorise('core.edit','com_users'))
		{
			$this->setRedirect(JRoute::_('/', false));
			return false;
		}
*/
        $view = JFactory::getApplication()->input->getCmd('view', 'usermanagers');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

}
