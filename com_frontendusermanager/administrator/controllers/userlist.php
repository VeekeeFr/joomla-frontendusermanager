<?php
/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_frontendusermanager/helpers/export.php');

/**
 * Usermanagers list controller class.
 * @since 0.1.0
 */
class FrontendusermanagerControllerUserlist extends JControllerAdmin
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Userlist', $prefix = 'FrontendusermanagerModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Export selected users
	 *
	 */
	public function export()
	{
		$this->checkToken();

		$pks = ArrayHelper::toInteger(explode(',', $this->input->post->getString('cids')));

		// We clean pks array as it might be empty		
		if (count($pks) == 1 && empty($pks[0]))
		{
			$pks = array();
		}

		$model = $this->getModel();

		$data = $model->getUserDataAsIterator($pks);

		if (count($data))
		{
			$rows = FumHelpersExport::getCSVData($data);

			unset($data);

			$date     = new JDate('now', new DateTimeZone('UTC'));
			$filename = 'users_' . $date->format('Y-m-d_His_T');

			$csvDelimiter = ComponentHelper::getComponent('com_actionlogs')->getParams()->get('csv_delimiter', ',');

			$app = JFactory::getApplication();
			$app->setHeader('Content-Type', 'application/csv', true)
				->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"', true)
				->setHeader('Cache-Control', 'must-revalidate', true)
				->sendHeaders();

			$output = fopen("php://output", "w");

			foreach ($rows as $row)
			{
				fputcsv($output, $row, $csvDelimiter);
			}

			fclose($output);
			$app->close();
		}
		else
		{
			$this->setMessage(JText::_('COM_FRONTENDUSERMANAGER_USERLIST_EXPORT_NOUSERS_TO_EXPORT'));
			$this->setRedirect(JRoute::_('index.php?option=com_frontendusermanager&view=userlist', false));
		}
	}
}
