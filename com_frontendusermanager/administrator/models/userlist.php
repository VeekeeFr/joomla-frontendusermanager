<?php

/**
 * @version     1.0.0
 * @package     FrontendUserManager
 * @copyright   2015 Hepta Technologies SL All rights reserved.
 *  * @author      Carlos <carlos@hepta.es> - http://extensions.hepta.es
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.folder');

JLoader::register('FumHelperCriteria', JPATH_COMPONENT_SITE . '/helpers/criteria.php');

/**
 * Methods supporting a list of Frontendusermanager records.
 *
 * @since 1.0.0
 */
class FrontendusermanagerModelUserlist extends JModelList
{
	/**
	 * @var Fields to be excluded
	 */
	protected $excludedFields = array();

	/**
	 * Constructor.
	 *
	 * @param	array	$config    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get form for the filtering
	 *
	 * @param	array	$data		$data submitted in the form
	 * @param	boolean	$loadData	Should sent data be reloaded
	 *
	 * @return	object
	 */
	public function getFilterForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();
		$params = ComponentHelper::getParams('com_frontendusermanager');

		$filtersToHide = array_merge($params->get('excluded_fields', array()), $params->get('filters_to_hide', array()));

		$filtersToHide = array_unique($filtersToHide);

		foreach ($filtersToHide as &$filterName)
		{
			$filterName = str_replace('groups', 'usergroup', $filterName);
		}

		$this->filterFormName = 'filter_usermanagerall';

		$filterForms = FumHelpersForm::getFilterForms();

		$filterFieldsXML = FumHelpersForm::getFieldsXML($filterForms);

		$profileFields = FumHelpersForm::getFieldsArray();

		$customForm = new JForm($this->filterFormName);
		$customForm->load('<?xml version="1.0" encoding="utf-8"?><form><fields name="filter"></fields></form>');

		foreach ($filterFieldsXML as $field)
		{
			$key = 'filter.' . $field->attributes()->name;

			if (property_exists($this->state, $key))
			{
				$field->addAttribute('default', $this->state->$key);
			}

			$customForm->setField($field, 'filter');
		}

		foreach ($profileFields as $jField)
		{
			if (!in_array($jField->getAttribute('name'), $filtersToHide))
			{
				$key = 'filter.' . $jField->getAttribute('name');

				$field = FumHelpersForm::getFieldBaseDefinition($jField);

				if (property_exists($this->state, $key))
				{
					$field->addAttribute('default', $this->state->$key);
				}

				$customForm->setField($field, 'filter');
			}
		}

		return $customForm;
	}

	/**
	 * Get users data as a database iterator
	 *
	 * @param   integer[]|null  $pks  An optional array of log record IDs to load
	 *
	 * @return  JDatabaseIterator
	 */
	public function getUserDataAsIterator($pks = null)
	{
		$db = $this->getDbo();
		$query = $this->getListQuery();

		if ($pks)
		{
			$query->where('u.id IN (' . implode(',', $pks) . ')');
		}

		$db->setQuery($query);

		return $db->getIterator();
	}


	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		FumHelpersForm::loadProfileStrings();

		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');
		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');
		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		if (empty($list['ordering']))
		{
			$list['ordering'] = 'ordering';
		}

		if (empty($list['direction']))
		{
			$list['direction'] = 'asc';
		}

		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);

	}

	/**
	 * Get criteria that applies to current user
	 *
	 * @return	array
	 **/
	public function getCriteria()
	{
		$cache = Factory::getCache();

		$criteria = $cache->call(array('FumHelperCriteria', 'getCriteria'), Factory::getUser()->id);

		return $criteria;
	}

	/**
	 * Get fields which should not be shown
	 */
	public function getExcludedFields()
	{
		return $this->excludedFields;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{ 
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_frontendusermanager');

		$excludedUsers = "";

		// Criteria
		$criteria = $this->getCriteria();

		$activeFields = $this->filter_fields;

		$profileFields = array();
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);

		$profileFields = FumHelpersForm::getFieldsArray();

		$profileGroup = "profile";
		$coreFields = array('id', 'name', 'username', 'email', 'block', 'activation', 'registerDate', 'lastvisitDate', 'params');

		$excludedFields = $params->get('excluded_fields', array());

		if (isset($criteria['excludedFields']))
		{
			$excludedFields = array_merge($excludedFields, $criteria['excludedFields']);
		}

		$excludedFields = array_unique($excludedFields);

		$this->excludedFields = $excludedFields;

		if ($allowedFields = array_diff($coreFields, $excludedFields))
		{
			if (!in_array('id', $allowedFields))
			{
				$allowedFields[] = 'id';
			}

			if (!in_array('block', $allowedFields))
			{
				$allowedFields[] = 'block';
			}

			$query->select('u.' . implode(',u.', $allowedFields));
		}

		if (!in_array('groups', $excludedFields))
		{
			$query->select('GROUP_CONCAT(DISTINCT gr.title ORDER BY gr.id DESC SEPARATOR \',\') AS groups');
		}

		$profileFieldsId = array_keys($profileFields);
		$allowedProfileFields = array_diff($profileFieldsId, $excludedFields);

		foreach ($allowedProfileFields as $field)
		{
			$query->select('max(case when up.profile_key LIKE "%.' . $field . '" then up.profile_value end) AS ' . str_replace('-', "", $field));
		}

		$query->from($db->quoteName('#__users') . ' AS u');
		$query->join('LEFT', $db->quoteName('#__user_profiles') . ' AS up ON up.' . $db->quoteName('user_id') . ' = u.' . $db->quoteName('id'));
		$query->join('LEFT', $db->quoteName('#__user_usergroup_map') . ' AS ug ON ug.' . $db->quoteName('user_id') . ' = u.' . $db->quoteName('id'));
		$query->join('LEFT', $db->quoteName('#__usergroups') . ' AS gr ON gr.' . $db->quoteName('id') . ' = ug.' . $db->quoteName('group_id'));
		$query->group('u.' . $db->quoteName('id'));


		if ($criteria)
		{
			if (isset($criteria['managedList']) && $criteria['managedList'])
			{
				$managedList = $criteria['managedList'];
			}
			else
			{
				if (isset($criteria['usergroups']) && ( in_array('*', $criteria['usergroups'])) !== false)
				{
					$groupsToInclude = $criteria['usergroups'];
				}

				if (!empty($criteria['excludedUsers']))
				{
					$excludedUsers = $criteria['excludedUsers'];
				}

				if (isset($criteria['languages']) && !empty($criteria['languages']))
				{
					$userLanguages = $criteria['languages'];

					foreach ($userLanguages as $language)
					{
						if ($language === "*")
						{
							$languageFilter = array();
							break;
						}

						$languageFilter[] = $language;
					}
				}

				if (isset($criteria['fields']) && !empty($criteria['fields']))
				{
					$fields = $criteria['fields'];

					foreach ($fields as $index => $field)
					{
						if (!in_array($field->profilefield, $coreFields))
						{
							$tableId = "cup" . $index;

							$query->join('LEFT', $db->quoteName('#__user_profiles') . ' AS ' . $tableId . ' ON ' . $tableId . '.' . $db->quoteName('user_id') . ' = u.' . $db->quoteName('id'));

							$keyWhere = $tableId . '.profile_key = "profile.' . $field->profilefield . '"';
							$valueWhere = $tableId . '.profile_value ' . $field->profilecomparison . " " . $db->q($field->profilevalue);

							$whereClause[] = $keyWhere . " AND " . $valueWhere;
						}
						else
						{
							$query->where('( u.block = ' . $field->profilevalue . ')');
						}
					}
				}
			}
		}

		// Filter by User Criteria
		$search = $this->getState('filter.builtin-block');

		if (!empty($search) || $search === "0")
		{
				$activeFields['builtin-block'] = 'builtin-block';
				$search = $db->Quote($db->escape($search, true));
				$query->where('( u.block = ' . $search . ')');
		}

		$builtinBlocks = array("name_username_search", "builtin-block","builtin-usergroup", "builtin-siteLanguage", "builtin-timezone");

		// Filter by profile search
		$filterFields = FumHelpersForm::getFilters();
		$filterProfileFields = FumHelpersForm::getFieldsArray();

		$filterFields = $filterFields + $filterProfileFields;

		$builtinSearch = array();
		$profileSearch = array();
		$whereClause = array();

		foreach ($filterFields as $field)
		{
			$search = $this->getState('filter.' . $field->fieldname);

			if (!empty($search) || $search === "0")
			{
				$activeFields[$field->fieldname] = $field->fieldname;
				$searchQuery = new stdClass;

				if (in_array($field->fieldname, $builtinBlocks))
				{
					$field->fieldname = str_replace('builtin-', "", $field->fieldname);

					$searchQuery = FumHelpersForm::createFilterQuery($field, $search);

					if ($field->fieldname == "name_username_search")
					{
						$searchQuery->key = "username";
					}

					if (isset($searchQuery->key))
					{
						$builtinSearch[] = $searchQuery;
					}
				}
				else
				{
					$searchQuery = FumHelpersForm::createFilterQuery($field, $search);

					if (isset($searchQuery->key))
					{
						$profileSearch[] = $searchQuery;
					}
				}
			}
		}

		// Check for main text search
		$mainSearch = $this->getState('filter.name_username_search');

		if (!empty($mainSearch))
		{
			$textSearchFields = array('name', 'email', 'username');

			foreach ($textSearchFields as $textSearchField)
			{
				if (!in_array($textSearchField, $excludedFields))
				{
					$textSearchParts[] = 'u.' . $textSearchField . ' LIKE "%' . $mainSearch . '%"';
				}
			}

			if (!empty($textSearchParts))
			{
				$query->where(implode(' OR ', $textSearchParts));
			}
		}

		foreach ($builtinSearch as $index => $searchQuery)
		{
			$table = 'u';

			if (!empty($searchQuery->table))
			{
				$table = $searchQuery->table;
			}

			$query->where($table . '.' . $searchQuery->key . $searchQuery->value);
		}

		foreach ($profileSearch as $index => $searchQuery)
		{
			$tableId = "up" . $index;

			$query->join('LEFT', $db->quoteName('#__user_profiles') . ' AS ' . $tableId . ' ON ' . $tableId . '.' . $db->quoteName('user_id') . ' = u.' . $db->quoteName('id'));

			$keyWhere = $tableId . '.profile_key = "profile.' . $searchQuery->key . '"';
			$valueWhere = $tableId . '.profile_value ' . $searchQuery->value;

			$whereClause[] = $keyWhere . " AND " . $valueWhere;
		}

		// After filtering by User Search we apply Component configured Criteria
		$languageFilter = array();

		if (isset($managedList))
		{
			$query->where("u.id IN (" . implode(',', $managedList) . ")");
		}
		else
		{
			$excludedGroups = $params->get('excludedgroups', array());

			if (!isset($groupsToInclude))
			{
				$groupsToInclude = $params->get('groups_filter', array());
			}

			if ($excludedGroups && !$groupsToInclude)
			{
				$query->where("(group_id NOT IN (" . implode(",", $excludedGroups) . ") )");
			}

			if ($excludedUsers)
			{
				$query->where('u.id NOT IN (' . implode(',', $excludedUsers) . ')'); 
			}

			$filteredGroup = $this->getState('filter.builtin-usergroup', '');

			if (!$groupsToInclude)
			{
				$groupsToInclude = array();

				if ($filterBuiltinUsergroup = $this->getState('filter.builtin-usergroup'))
				{
					$groupsToInclude = array($filterBuiltinUsergroup);
				}
			}
			elseif ($filteredGroup)
			{
				$groupsToInclude = array_intersect(array($filteredGroup), $groupsToInclude);
			}

			if ($groupsToInclude && !in_array('*', $groupsToInclude))
			{
					$query->where("(group_id IN (" . implode(",", $groupsToInclude) . ") )");
			}

			if ($languageFilter)
			{
				foreach ($languageFilter as $language)
				{
					$languageQuery[] = 'u.params LIKE "%\"language\":\"' . $language . '\"%"';
				}

				$query->where("(" . implode(" OR ", $languageQuery) . ")");
			}
		}

		if (!empty($whereClause))
		{
			$query->where("(" . implode(" ) AND (", $whereClause) . ")");
		}

		$this->filter_fields = $activeFields;

		return $query;
	}

	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $item)
		{
			$params = new Joomla\Registry\Registry;
			$params->loadString($item->params);
			$item->siteLanguage = $params->get('language', ComponentHelper::getParams('com_languages')->get('site') . "(Default)");
			$item->timezone = $params->get('timezone', Factory::getConfig()->get('offset') . "(Default)");
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;
		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}
		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_FRONTENDUSERMANAGER_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in an specified format (YYYY-MM-DD)
	 *
	 * @param string Contains the date to be checked
	 *
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}

	

}
