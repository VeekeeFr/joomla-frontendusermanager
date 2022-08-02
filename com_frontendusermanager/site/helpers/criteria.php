<?php

/**
 * @version    1.0.0
 * @package    Com_FrontendUserManager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2017 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Frontendusermanager Criteria helper.
 *
 * @since  1.0
 *
 **/
class FumHelperCriteria
{
	/**
	 * Method to get Filter criteria
	 *
	 * @param   int $userId The id of the user to get the criteria which applies
	 *
	 * @return array Array with the criteria which applies
	 */
	public static function getCriteria($userId) : array
	{
		$result = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('managed_list AS managedList, managers_list as managersList, excludedUsers, usergroups, languages, customfields, profilefields, excludedFields, permissions');
		$query->from('#__frontendusermanager_criterias');
		$query->where('state = 1');
		$query->order('ordering DESC');

		$db->setQuery($query);

		$criterias = $db->loadObjectList();

		if ($criterias)
		{
			foreach ($criterias as $criteria)
			{
				if (!empty($criteria->managersList))
				{
					$managersList = Joomla\Utilities\ArrayHelper::fromObject(json_decode($criteria->managersList));

					if (in_array($userId, $managersList))
					{
						$result['managedList'] = array();

						if (!empty($criteria->managedList))
						{
							$managedList = json_decode($criteria->managedList);
							$result['managedList'] = $managedList;
						}

						$result['usergroups'] = array();

						if (!empty($criteria->usergroups))
						{
							$userGroups = explode(',', $criteria->usergroups);
							$result['usergroups'] = $userGroups;
						}

						$result['languages'] = array();

						if (!empty($criteria->languages))
						{
							$languages = json_decode($criteria->languages);
							$result['languages'] = $languages;
						}

						$result['excludedUsers'] = array();

						if (!empty($criteria->excludedUsers))
						{
							$excludedUsers = json_decode($criteria->excludedUsers);
							$result['excludedUsers'] = $excludedUsers;
						}

						$result['customFields'] = array();

						if (!empty($criteria->customfields))
						{
							$customFields = json_decode($criteria->customfields);

							foreach ($customFields as $customField)
							{
								$customField = self::translateDbOperator($customField);

								$cFields[] = $customField;
							}

							$result['customFields'] = $cFields;
						}

						$result['profileFields'] = array();

						if (!empty($criteria->profilefields))
						{
							$profileFields = json_decode($criteria->profilefields);

							foreach ($profileFields as $profileField)
							{
								$profileField = self::translateDbOperator($profileField);

								$fields[] = $profileField;
							}

							$result['profileFields'] = $fields;
						}

						$result['excludedFields'] = array();

						if (!empty($criteria->excludedFields))
						{
							$result['excludedFields'] = json_decode($criteria->excludedFields);
						}

						$result['permissions'] = array();

						if (!empty($criteria->permissions))
						{
							$result['permissions'] = json_decode($criteria->permissions);
						}
					}
				}

				continue;
			}
		}

		return $result;
	}

	/**
	 * Get comparison opeartor for DB search
	 * @param	Object	$profileField	Comparison operator
	 *
	 * @return	object
	 */
	public static function translateDbOperator($profileField)
	{
		switch ($profileField->profilecomparison)
		{
			case 'ge':
				$profileField->profilecomparison = ">=";
				break;
			case 'le':
				$profileField->profilecomparison = "<=";
				break;
			case 'contains':
				$profileField->profilecomparison = "LIKE";
				$profileField->profilevalue = "%" . $profileField->profilevalue . "%";
				break;
			case 'doesnotcontain':
				$profileField->profilecomparison = "NOT LIKE";
				$profileField->profilevalue = "%" . $profileField->profilevalue . "%";
				break;
		}

		$profileField->profilevalue = '"' . $profileField->profilevalue . '"';

		return $profileField;
	}

}

