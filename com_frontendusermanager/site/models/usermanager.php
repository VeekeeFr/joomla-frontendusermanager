<?php
/**
 * @package   FrontendUserManager
 *
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.hepta.es
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Dispatcher;
use Joomla\CMS\User\UserHelper;

defined('_JEXEC') or die;

/**
 * UserManager model.
 *
 * @package FrontendUserManager
 * @since    1.0
 */
class FrontendUserManagerModelUserManager extends FormModel
{
	/**
	 * @var Item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_frontendusermanager')) && (!$user->authorise('core.edit', 'com_frontendusermanager')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->input->get('id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_frontendusermanager.edit.user.id', $id);
		}

		// Load the parameters.
		$params       = $app->getParams();

		$this->setState('user.id', $id);

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer $id The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @throws Exception
	 **/
	public function &getData($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('user.id');
			}

			// Get a level row instance.
			$this->item = Factory::getUser($id);
		}

		return $this->item;
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array   $data     An optional array of data for the form to interogate.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app  = Factory::getApplication();
		$params = $app->getParams();

		$criteria = FumHelperCriteria::getCriteria(JFactory::getUser()->id);

		$excludedFields = array_merge($params->get('excluded_fields', array()), array('password', 'password2', 'lastResetTime', 'lastresetDate', 'resetCount', 'sendEmail', 'requireReset', 'block'), $criteria['excludedFields']);
		$groups = array();

		// Get the form.
		$form = $this->loadForm('com_users.user', 'user', array(
				'control'   => 'jform',
				'load_data' => $loadData
			)
		);

		$xml = $form->getXml();
		$elements = $xml->xpath('//fields[@name]/@name');

		$groups[] = null;
		$groups[] = "profile";

		foreach ($elements as $xmlElement)
		{
			$groups[] = (string) $elements[0];
		}

		foreach ($groups as $group)
		{
			foreach ($excludedFields as $index => $fieldName)
			{
				if ($fieldName != "id")
				{
					if ($form->removeField($fieldName, $group))
					{
						unset($excludedFields[$index]);
					}
				}
			}
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_frontendusermanager.edit.user.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		$this->preprocessData('com_users.user', $data, 'user');

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
		
		$editor  = Factory::getUser();
		$user = Factory::getUser($id);

		if ($id)
		{
			// Check the editor can edit this user
			$authorised = $editor->authorise('core.edit', 'com_frontendusermanager');
		}
		else
		{
			// Check the editor can create new users in this section
			$authorised = $editor->authorise('core.create', 'com_frontendusermanager');
		}

		if ($authorised !== true)
		{	
			if ($id)
			{
				throw new Exception(JText::_('COM_FRONTENDUSERMANAGER_NOT_ALLOWED_TO_EDIT_USER'), 403);
			}
			else
			{
				throw new Exception(JText::_('COM_FRONTENDUSERMANAGER_NOT_ALLOWED_TO_CREATE_USER'), 403);
			}
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError($user->getError());

			return false;
		}

		// Store the data.
		if (!$user->save())
		{
			$this->setError($user->getError());

			return false;
		}

		return true;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}
}
