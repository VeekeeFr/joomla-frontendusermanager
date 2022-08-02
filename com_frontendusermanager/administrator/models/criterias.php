<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Frontendusermanager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Frontendusermanager records.
 *
 * @since  1.6
 */
class FrontendusermanagerModelCriterias extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'managers_list', 'a.`managers_list`',
				'managed_list', 'a.`managed_list`',
				'usergroups', 'a.`usergroups`',
				'languages', 'a.`languages`',
				'profilefields', 'a.`profilefields`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering managers_list
		$this->setState('filter.managers_list', $app->getUserStateFromRequest($this->context.'.filter.managers_list', 'filter_managers_list', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_frontendusermanager');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.profilefields', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__frontendusermanager_criterias` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.profilefields LIKE ' . $search . ' )');
			}
		}

		// Filtering managers_list
		$filter_managers_list = $this->state->get("filter.managers_list");

		if ($filter_managers_list)
		{
			$query->where("a.`managers_list` = " . $db->quote($db->escape($filter_managers_list)));
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $oneItem)
		{
			$managers = array();

			if ( isset($oneItem->usergroups) && !empty($oneItem->usergroups) )
			{
				// Get the title of that particular user group
				$groups = explode(',',$oneItem->usergroups);

				$group_names = array();

				foreach ((array) $groups as $group)
				{
					$group_names[] = FumHelpersGroups::getGroupNameByGroupId($group);
				}

				$oneItem->usergroups = !empty($group_names) ? implode(', ', $group_names) : $oneItem->usergroups;
			}

			if (isset($oneItem->managers_list) && !empty($oneItem->managers_list))
			{
				$oneItem->managers_list = $this->decodeUsersJson($oneItem->managers_list);
			}

			if (isset($oneItem->managed_list) && !empty($oneItem->managed_list))
			{
				$oneItem->managed_list = $this->decodeUsersJson($oneItem->managed_list);
			}

		}
		return $items;
	}

	protected function decodeUsersJson($list)
	{
		$usersArray = json_decode($list);

		foreach($usersArray as $userId)
		{
			$users[] = JFactory::getUser($userId)->get('username');
		}

		if(!empty($users))
		{
			$list = $users;
		}

		return $list;
	}
}
