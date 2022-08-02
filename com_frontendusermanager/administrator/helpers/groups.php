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
class FumHelpersGroups {
	public static function getGroupNameByGroupId($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('title')
			->from('#__usergroups')
			->where('id =' . $db->q($id));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
