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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_frontendusermanager')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');
JLoader::import('hepta.formfields');

JLoader::registerPrefix('Fum', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Frontendusermanager');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
