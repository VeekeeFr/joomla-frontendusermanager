<?php

/**
 * @version    0.1.0
 * @package    Com_Frontendusermanager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Frontendusermanager helper.
 */
class FumHelpersBackend {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(JText::_('COM_FRONTENDUSERMANAGER_TITLE_CRITERIAS'),
								'index.php?option=com_frontendusermanager&view=criterias', $vName == 'criterias');
		JHtmlSidebar::addEntry(JText::_('COM_FRONTENDUSERMANAGER_TITLE_USERLIST'),
								'index.php?option=com_frontendusermanager&view=userlist', $vName == 'userlist');
    }
}
