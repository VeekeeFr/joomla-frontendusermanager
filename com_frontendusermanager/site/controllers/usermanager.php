<?php
/**
 * @package   FrontendUserManager
 *
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.hepta.es
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;


defined('_JEXEC') or die;

/**
 * UserManager controller.
 *
 * @package  FrontendUserManager
 * @since    1.0
 */
class FrontendUserManagerControllerUserManager extends FormController
{
	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();

		if ($itemId = $this->input->get('Itemid', null))
		{
			$append .= "&Itemid=" . $itemId;
		}

		return $append;
	}

	/**
	 * Function which toggles the block state of the user
	 */
	public function toggleBlock()
	{

		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$block = Factory::getApplication()->input->get('block');
		$idUser = Factory::getApplication()->input->get('id');
		$user = Factory::getUser($idUser);
		$block ^= 1;
		$user->block = $block;
		$user->save();

		Factory::getApplication()->enqueueMessage(JText::_('COM_FRONTENDUSERMANAGER_USER_UPDATED'));

		$this->setRedirect(JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers'));
	}

	/**
	 * Function that activates the selected user
	 *
	 * @return void
	 */
	public function activateUser()
	{
		$db = Factory::getDbo();

		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$userId = Factory::getApplication()->input->get('activation');
		$user = Factory::getUser($userId);

		if ($user->lastvisitDate != $db->getNullDate() || !$user->block)
		{
			$user->lastvisitDate = $db->getNullDate();
			$user->block = 1;
			$user->save();
		}

		$activation = Factory::getUser($userId)->activation;
		JUserHelper::activateUser($activation);
		Factory::getApplication()->enqueueMessage(JText::_('COM_FRONTENDUSERMANAGER_USER_ACTIVATED'));
		$this->setRedirect(JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers'));

		return;
	}

	/**
	 * Function that resends activation link to the user
	 *
	 * @return void
	 */
	public function sendactivationlink()
	{
		$config = Factory::getConfig();
		$comUserParams = JComponentHelper::getParams('com_users');

		$lang = Factory::getLanguage();
		$lang->load('com_users', JPATH_SITE, $lang->getTag(), true);

		$db = Factory::getDbo();

		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$userId = Factory::getApplication()->input->get('activation');
		$user = Factory::getUser($userId);

		$activation = Factory::getUser($userId)->activation;

		// Compile the notification mail values.
		$emailData = $user->getProperties();
		$emailData['fromname'] = $config->get('fromname');
		$emailData['mailfrom'] = $config->get('mailfrom');
		$emailData['sitename'] = $config->get('sitename');
		$emailData['siteurl'] = JUri::root();

		$linkMode = $config->get('force_ssl', 0) == 2 ? Route::TLS_FORCE : Route::TLS_IGNORE;

		$emailData['activate'] = JRoute::link(
			'site',
			'index.php?option=com_users&task=registration.activate&token=' . $activation,
			false,
			$linkMode,
			true
		);

		$emailSubject = JText::sprintf(
			'COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$emailData['name'],
			$emailData['sitename']
		);

		$emailBody = JText::sprintf(
			'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
			$emailData['name'],
			$emailData['sitename'],
			$emailData['activate'],
			$emailData['siteurl'],
			$emailData['username']
		);

		$bcc = array($emailData['mailfrom']);
		$return = Factory::getMailer()->sendMail($emailData['mailfrom'], $emailData['fromname'], $emailData['email'], $emailSubject, $emailBody, true, null, $bcc);

		Factory::getApplication()->enqueueMessage(JText::_('COM_FRONTENDUSERMANAGER_USER_ACTIVATION_LINK_SENT'));
		$this->setRedirect(JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers'));

		return $return;
	}
	/**
	 * Export user data
	 */
	public function export()
	{
		
	}
}
