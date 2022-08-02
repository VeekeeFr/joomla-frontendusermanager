<?php
/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

JLoader::registerPrefix('Fum', JPATH_ADMINISTRATOR . "/components/com_frontendusermanager");
JFormHelper::loadFieldClass('list');

/**
 * Sample list form field
 *
 * @package     Sample.Library
 * @subpackage  Field
 * @since       1.0
 */
class HeptaFormFieldProfilefields extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'hepta.profilefields';

	/**
	 * Cached array of the category items.
	 *
	 * @var  array
	 */
	protected static $options = array();


	/**
	 * Translate options labels ?
	 *
	 * @var  boolean
	 */
	protected $translate = true;

	protected function getInput()
	{
		HtmlHelper::script('com_frontendusermanager/field_profilefields.js', array('relative' => true), array('defer' => 'defer'));

		$html[] = parent::getInput();

		if ($this->multiple)
		{
			$html[] = '<button type="button" data-field="' . $this->id . '" class="" onclick="selectAll(this)">Select all</button>';
		}

		return implode('', $html);
	}

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Hash for caching
		$hash = md5($this->element);
		$type = strtolower($this->type);

		$fieldsGroup = explode(',', str_replace(' ', '', $this->getAttribute('fieldsGroup', 'custom,core')));

		if (!isset(static::$options[$type][$hash]))
		{
			static::$options[$type][$hash] = parent::getOptions();

			$options = array();

			if (in_array('core', $fieldsGroup))
			{
				$coreFields = array('id',
					'name',
					'username',
					'email',
					'groups',
					'registerDate',
					'lastvisitDate',
					'block',
					'siteLanguage',
					'timezone'
				);

				foreach ($coreFields as $fieldName)
				{
					$options[] = (object) array(
						'value' => $fieldName,
						'text'  => Text::_($fieldName)
					);
				}
			}

			if (in_array('profile', $fieldsGroup))
			{
				$profileFields = FumHelpersForm::getFieldsArray();
				FumHelpersForm::loadProfileStrings();

				foreach ($profileFields as $fieldName => $field)
				{
					$options[] = (object) array(
						'value' => $fieldName,
						'text'  => JText::_($field->getAttribute('label'))
					);
				}
			}

			if (in_array('custom', $fieldsGroup))
			{
				$customFields = FieldsHelper::getFields('com_users.user', null, true);

				foreach ($customFields as $field)
				{
					$option = new stdClass;
					$option->value = $field->id;
					$option->text = $field->title;
					$options[] = $option;
				}
			}


			static::$options[$type][$hash] = array_merge(static::$options[$type][$hash], $options);
		}

		return static::$options[$type][$hash];
	}

	/**
	 * Wrapper method for getting attributes from the form element
	 *
	 * @param string $attr_name Attribute name
	 * @param mixed  $default   Optional value to return if attribute not found
	 *
	 * @return mixed The value of the attribute if it exists, null otherwise
	 */
	public function getAttribute($attr_name, $default = null)
	{
		if (!empty($this->element[$attr_name]))
		{
			return $this->element[$attr_name];
		}
		else
		{
			return $default;
		}
	}
}
