<?php

/**
 * @version    0.1.0
 * @package    Com_Frontendusermanager
 * @author	   Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;

/**
 * Frontendusermanager helper.
 * @since 0.6.0
 */
class FumHelpersForm
{
	/**
	 * Method to create the query according to the type of field
	 *
	 * @param	object	$field	FormField object
	 * @param	string	$search	Search string
	 *
	 * @return	object	Query object
	 */
	public static function createFilterQuery($field, $search)
	{
		$db = Factory::getDbo();
		$searchQuery = new stdClass;

		switch ($field->type)
		{
			case 'Radio':
				$searchQuery->key = $field->fieldname;

				if ($search == 'Yes')
				{
					$searchQuery->value = " = Yes";
				}
				else
				{
					$searchQuery->value = ' != "Yes"';
				}
				break;
			case 'List':
				if (($search == '--Select--') || ($search == ' '))
				{
					break;
				}
				else
				{
					$searchQuery->key = $field->fieldname;
					$searchQuery->value = ' = \'"' . $search . '"\'';
				}
				break;
			case 'hepta.CustomDateRange':

				list($startDate, $endDate) = explode(' - ', $search);

				if ($startDate && $endDate)
				{
					$startDate = JFactory::getDate(strtotime($startDate . " 00:00:00"))->toSql();
					$endDate = JFactory::getDate(strtotime($endDate . " 23:59:59"))->toSql();
					$searchQuery->key = $field->fieldname;
					$searchQuery->value = ' BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
				}

				break;
			case 'UserGroupList':
				$searchQuery->table = 'ug';
				$searchQuery->key = 'group_id';
				$searchQuery->value = ' = ' . $search;
				break;
			case 'Language':
				$searchQuery->key = 'params';

				if (ComponentHelper::getParams('com_languages')->get('site') == $search)
				{
					$search = "(" . $search . ")*";
				}

				$searchQuery->value = ' REGEXP ' . $db->q("\"language\":\"" . $search . '"');
				break;
			case 'Timezone':
				$searchQuery->key = 'params';

				if (Factory::getConfig()->get('offset') == $search)
				{
					$search = "(" . $search . ")*";
				}

				$searchQuery->value = ' REGEXP ' . $db->q("\"timezone\":\"" . $search . '"');
				break;
			default:
				$searchQuery->key = $field->fieldname;
				$searchQuery->value = ' LIKE \'"%' . $search . '%"\'';
				break;
		}

		return $searchQuery;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_frontendusermanager';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Method to get All Profile plugins forms
	 *
	 * @return array
	 **/
	public static function getProfilePlugins()
	{
		$userPlugin = array();

		$profilePlugins = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('folder AS type, element AS name, params')
			->from('#__extensions')
			->where('enabled = 1')
			->where('type =' . $db->quote('plugin'))
			->where('folder=' . $db->quote('user'))
			->where('state IN (0,1)')
			->order('ordering');
		$db->setQuery($query);

		$publishedUserPlugins = $db->loadObjectList();

		foreach ($publishedUserPlugins as $userPlugin)
		{
			$pluginPath = JPATH_PLUGINS . "/" . $userPlugin->type . "/" . $userPlugin->name . "/profiles";

			if (is_dir($pluginPath))
			{
				$profileXML = Folder::files($pluginPath, ".xml", array(), true);

				if (!empty($profileXML))
				{
					$userPlugin->profileXML = $profileXML;
					$profilePlugins[] = $userPlugin;
				}
			}
			else
			{
				$pluginPath = JPATH_PLUGINS . "/" . $userPlugin->type . "/" . $userPlugin->name;
				$possibleFormPaths = Folder::folders($pluginPath, '.', true, true, array('language', 'languages'), array('[a-z]{2}\-[A-Z]{2}'));

				foreach ($possibleFormPaths as $formPath)
				{
					$profileXML = Folder::files($formPath, ".xml", array(), true);

					if (!empty($profileXML))
					{
						$userPlugin->profileXML = $profileXML;
						$profilePlugins[] = $userPlugin;
					}
				}
			}
		}

		return $profilePlugins;
	}

	/**
	 * Get Fields form XML File
	 *
	 * @param	string	$xmlFile	XML definition
	 *
	 * @return	array	Array of fields
	 **/
	public static function getFields($xmlFile)
	{
		$form = new JForm(basename($xmlFile));

		$form->loadFile($xmlFile);

		$fields = $form->getFieldset();

		return $fields;
	}

	public static function getFormXML($xmlFile)
	{
		$form = new JForm(basename($xmlFile));

		$form->loadFile($xmlFile);

		$fields = $form->getFieldset();

		return $form->getXml();
	}

	/**
	 * Get an array with all form fields
	 *
	 * @return array	Fields array
	 **/
	public static function getFieldsArray()
	{
		$profileFields = array();
		$profilePlugins = static::getProfilePlugins();

		$customForm = new JForm('com_users.user');
		$customForm->load('<?xml version="1.0" encoding="utf-8"?><form></form>');

		// We get form fields from plugin call
		foreach ($profilePlugins as $profilePlugin)
		{
			JPluginHelper::importPlugin($profilePlugin->name);

			Factory::getApplication()->triggerEvent('onContentPrepareForm', array($customForm, array()));

			$pluginFields = $customForm->getFieldset();

			foreach ($pluginFields as $field)
			{
				$profileFields[$field->fieldname] = $field;
			}
		}

		return $profileFields;
	}

	/**
	 * Get array of xml files to create the filters
	 *
	 * @return	filters array
	 */
	public static function getFilterForms()
	{
		$filters = array();
		$filtersFolder = JPATH_COMPONENT_SITE . '/models/forms/';

		if (JFolder::exists($filtersFolder))
		{
			$filters = Folder::files($filtersFolder, "filter_(.*).xml", true, true);
		}

		return $filters;
	}

	public static function getFilters()
	{
		$filterFields = array();
		$filters = self::getFilterForms();

		foreach ($filters as $filterFile)
		{
			$formFields = static::getFields($filterFile);

			foreach ($formFields as $field)
			{
				$filterFields[$field->fieldname] = $field;
			}
		}

		return $filterFields;
	}

	public static function loadPluginLanguage($plugin)
	{

	}

	public static function loadProfileStrings()
	{
		// Load the users plugins.
		JPluginHelper::importPlugin('user');

		// Trigger language load.
		$results = Factory::getApplication()->triggerEvent('loadLanguage');
	}

	/**
	 * Method to get the xml definition of a field
	 * @deprecated
	 * @param	object	$field	Field definitions
	 *
	 * @return	string	XML definition of the field
	 */
	public static function getFieldBaseDefinition($field)
	{
		$xml = new SimpleXMLElement('<field></field>');

		$xml->addAttribute('name', $field->getAttribute('name'));
		$xml->addAttribute('type', $field->getAttribute('name'));
		$xml->addAttribute('label', $field->getAttribute('label'));

		return $xml;
	}


	/**
	 * Method to get the xml definition of a field
	 * @param	object	$field	Field stdClass object
	 *
	 * @return	string	XML definition of the field
	 */
	public static function getFieldXMLDefinition($field)
	{
		$xml = new SimpleXMLElement('<field></field>');

		$xml->addAttribute('name', $field->id);
		$xml->addAttribute('type', $field->type);
		$xml->addAttribute('label', $field->title);

		return $xml;
	}

	/**
	 * Get xml fields from the forms
	 *
	 * @param	array	$filterForms	Array of xml files to extract the fields from
	 *
	 * @return	array	Array of fields to use for the filters
	 */
	public static function getFieldsXML($filterForms = null)
	{
		$fieldsXML = array();

		if (!$filterForms)
		{
			$filterForms = self::getFilterForms();
		}

		foreach ($filterForms as $filterForm)
		{
			$formFields = self::getFields($filterForm);
			$formXML = self::getFormXML($filterForm);

			foreach ($formFields as $field)
			{
				$fieldXML = $formXML->xpath('//field[@name="' . $field->fieldname . '"]');
				$fieldsXML[] = $fieldXML[0];
			}
		}

		return $fieldsXML;
	}

	/**
	 * Check if the string is a json object
	 *
	 * @param	string	$string	String to check
	 *
	 * @return	boolean
	 **/
	public static function isJson($string)
	{
		json_decode($string);

		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Wrapper to Get Available Custom Fields
	 *
	 * @param	object	$item	Item to get the fields from
	 * @return	array	Array of current Custom Fields
	 **/
	public static function getCustomFields($item = null )
	{
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		return FieldsHelper::getFields('com_users.user',  $item, true);
	}


}
