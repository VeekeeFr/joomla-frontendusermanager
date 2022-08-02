<?php

/**
 * @version    0.1.0
 * @package    com_frontendusermanager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

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
		$canDo = JHelperContent::getActions('com_users');

		if ($canDo->get('core.edit.state'))
		{			
			$view = JFactory::getApplication()->input->getCmd('view', 'criterias');
			JFactory::getApplication()->input->set('view', $view);

			parent::display($cachable, $urlparams);
		}
		else {
			$this->setMessage(JText::_('COM_FRONTENDUSERMANAGER_ACCESS_FORBIDDEN'));
			$this->setRedirect('index.php');
		}
        return $this;
    }

}
