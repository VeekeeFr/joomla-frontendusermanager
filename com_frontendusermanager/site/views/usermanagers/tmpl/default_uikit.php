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


$headingFields = $this->params->get('header_fields');

$profileFields = FumHelpersForm::getFieldsArray();

$profileFieldsId = array_keys($profileFields);

$coreFields = array('id',
			'name',
			'username',
			'email',
			'groups',
			'registerDate',
			'lastvisitDate',
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

?>
<form action="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers'); ?>" method="post" name="adminForm" id="adminForm">
<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
<?php if( count($this->items) > 0 ): ?>
	<div class="fum_userstable">
		<table id="fum_userstable" class="uk-table uk-table-striped uk-table-condensed fum-table">
			<thead>
			<tr>
				<?php foreach($tableHeader as $field): ?>
					<th><?php echo JText::_($field->label); ?></th>
				<?php endforeach ?>
				<?php foreach($fields as $field): ?>
					<?php if(isset($profileFields[$field])) : ?>
						<th data-breakpoints="xs sm md lg"><?php echo JText::_($profileFields[$field]->getAttribute('label')); ?></th>
					<?php else: ?>
						<th data-breakpoints="xs sm md lg"><?php echo JText::_($field); ?></th>
					<?php endif; ?>
				<?php endforeach ?>
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
			<?php foreach($this->items as $userData) : ?>
				<tr>
					<?php foreach($tableHeader as $field): ?>
						<?php if(FrontendusermanagerFrontendHelper::isJson($userData->{$field->name})) : ?>
							<td><?php echo json_decode($userData->{$field->name}); ?></td>
						<?php else :?>
							<td><?php echo $userData->{$field->name} ?></td>
						<?php endif; ?>
					<?php endforeach ?>
					<?php foreach($fields as $field): ?>
						<?php if(FrontendusermanagerFrontendHelper::isJson($userData->$field)) : ?>
							<td><?php echo json_decode($userData->{$field}); ?></td>
						<?php else :?>
							<td><?php echo $userData->{$field} ?></td>
						<?php endif; ?>
					<?php endforeach ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="uk-alert uk-margin"><?php echo JText::_('COM_FRONTENDUSERMANAGER_USERMANAGERS_NOUSERS'); ?></div>
<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">
	jQuery(function () {
		jQuery('.footable').footable();
	});
</script>
