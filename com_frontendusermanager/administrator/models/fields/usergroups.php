<?php

/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Hepta Technologies SL All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@hepta.es> - https://www.hepta.es
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class FumFormFieldUserGroups extends JFormFieldUsergroups {
    function getOptions()
    {
        $criteria= fumHelperCriteria::getCriteria(JFactory::getUser()->id);
        if($criteria)
		{
			if(isset($criteria['usergroups']))
			{
				$groupsToInclude = $criteria['usergroups'];
            }
        }

    }

}

