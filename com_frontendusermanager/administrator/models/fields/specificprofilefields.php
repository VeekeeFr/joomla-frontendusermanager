<?php
/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */

defined('_JEXEC') or die;

require_once JPATH_BASE.'/components/com_frontendusermanager/helpers/frontendusermanager.php';

JFormHelper::loadFieldClass('list');

/**
 * Sample list form field
 *
 * @package     Sample.Library
 * @subpackage  Field
 * @since       1.0
 */
class HeptaFormFieldSpecificprofilefields extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'hepta.specificprofilefields';

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

		if (!isset(static::$options[$type][$hash]))
		{
			static::$options[$type][$hash] = parent::getOptions();

			$options = array();

			$profileFields = FrontendusermanagerHelpersFrontendusermanager::getFieldsArray();

			FrontendusermanagerHelpersFrontendusermanager::loadProfileStrings();

			$coreFields = array('id',
				'name',
				'username',
				'email',
				'groups',
				'registerDate',
				'lastvisitDate',
				'block'
				);

			foreach ($coreFields as $fieldName)
			{
					$options[] = (object) array(
						'value' => $fieldName,
						'text'  => $fieldName
					);
			}
			
			foreach ($profileFields as $fieldName => $field)
			{
					$options[] = (object) array(
						'value' => $fieldName,
						'text'  => JText::_($field->getAttribute('label'))
					);
			}

			static::$options[$type][$hash] = array_merge(static::$options[$type][$hash], $options);
		}

		return static::$options[$type][$hash];
	}
}
