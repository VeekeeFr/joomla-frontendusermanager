<?php
/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
jimport('hepta.formfields.formfields');
JLoader::register('FumHelperCriteria', JPATH_COMPONENT_SITE . '/helpers/criteria.php');

//$formFields = new HeptaFormFields();

// Execute the task.
$controller	= JControllerLegacy::getInstance('Frontendusermanager');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
