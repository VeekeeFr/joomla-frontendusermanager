<?php

/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');

$inputField = JFactory::getApplication()->input->get('inputField','');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$headingFields = $this->params->get('header_fields', array("username","name","email","groups",'registerDate'));


$profileFields = FumHelpersForm::getFieldsArray();

$profileFieldsId = array_keys($profileFields);

$coreFields = array('id',
			'name',
			'username',
			'email',
			'groups',
			'registerDate',
			'lastvisitDate',
			'block'
			);

$fields = array_merge($profileFieldsId, $coreFields);


$fields = array_diff($fields, $headingFields);

foreach($headingFields as $field)
{

	if(isset($profileFields[$field]))
	{
		$tableHeader[] =  (object) array('label' => $profileFields[$field]->getAttribute('label'), 'name'=> $field);
	}
	else
	{
		$tableHeader[] = (object) array('label' => $field, 'name' => $field);
	}
}
if($inputField)
{
	JFactory::getDocument()->addScriptDeclaration('
			var chooseUser = function(id, name, username) {			
				window.parent.fumAddUser_' . $inputField . '(id, name, username);
			};
			jQuery(document).ready(function() {
				jQuery("#fum_userstable tr").click(function(){					
					jQuery(this).prop("onclick", null).off("click");
					this.classList.add("selected");
					this.disabled = true;
					chooseUser(jQuery(this).data("userid"),jQuery(this).data("name"),jQuery(this).data("username"));
				});
			});
	');

	JFactory::getDocument()->addStyleDeclaration('
		.fum_userstable_modal tr:hover{
			cursor: pointer;
		}
		.fum_userstable_modal tr.selected:hover{
			cursor: not-allowed;
		}
		.fum_userstable_modal tr.selected{
			border: 2px solid black;
			font-weight: bold;
		}
	');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=userlist&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
<?php if (count($this->items) > 0): ?>
	<div class="fum_userstable">
		<table id="fum_userstable" class="table table-hover table-condensed footable fum_userstable_modal">
			<thead>
			<tr>
				<?php foreach ($tableHeader as $field): ?>
					<th><?php echo JText::_($field->label); ?></th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php $i = 0; ?>
			<?php foreach ($this->items as $userData) : ?>
				<tr data-userid="<?= $userData->id; ?>" data-name="<?= $userData->name; ?>" data-username="<?= $userData->username; ?>">
					<?php foreach ($tableHeader as $field): ?>
						<?php if (FumHelpersForm::isJson($userData->{$field->name})) : ?>
							<td><?php echo json_decode($userData->{$field->name}); ?></td>
						<?php else :?>
							<td><?php echo $userData->{$field->name} ?></td>
						<?php endif; ?>
					<?php endforeach ?>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="alert alert-block alert-error"><?php echo JText::_('COM_FRONTENDUSERMANAGER_USERMANAGERS_NOUSERS'); ?></div>
<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
