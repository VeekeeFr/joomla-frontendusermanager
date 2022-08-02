<?php

/**
 * @version    0.1.0
 * @package    Com_Frontendusermanager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;

/**
 * Frontendusermanager helper.
 *
 * @since	0.1.0
 */
class FumHelpersExport
{
	/**
	 * Format CSV export
	 *
	 * @param	iterator	$data	Data to export as iterator
	 *
	 * @return	array
	 */
	public static function getCSVData($data)
	{
		$rows = array();

		$fields = array('name', 'username', 'email', 'registerDate');

		foreach ($fields as $field)
		{
			$header[$field] = Text::_('COM_FRONTENDUSERMANAGER_EXPORT_HEADER_FIELD_' . strtoupper($field));
		}

		$rows[] = $header;

		foreach ($data as $user)
		{
			$date      = new JDate($user->registerDate, new DateTimeZone('UTC'));

			foreach ($fields as $field)
			{
				$row[$field] = $user->{$field};
			}

			$rows[] = $row;
		}

		return $rows;
	}
}
