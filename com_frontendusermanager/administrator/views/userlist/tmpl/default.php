<?php

/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
Use Joomla\CMS\Factory;
Use \Joomla\CMS\Router\Route;
Use \Joomla\CMS\HTML\HTMLHelper;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::script('com_frontendusermanager/footable/footable.min.js', false, true);
JHtml::stylesheet('com_frontendusermanager/footable/footable.standalone.min.css', false, true, false, false, true);
$document = Factory::getDocument();
$document->addStyleSheet('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

$document = Factory::getDocument();
$document->addStyleSheet('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$headingFields = $this->params->get('header_fields', array("username","name","email","groups",'registerDate','siteLanguage','timezone'));


$profileFields = FumHelpersForm::getFieldsArray();

$profileFieldsId = array_keys($profileFields);

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

$excludedFields = $this->excludedFields;

$headingFields = array_diff($headingFields, $excludedFields);

$allowedFields = array_diff($coreFields, $excludedFields);

$allowedProfileFields = array_diff($profileFieldsId, $excludedFields);

$allFields = array_merge($allowedFields, $allowedProfileFields);

$fields = array_diff($allFields, $headingFields);

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
<form action="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=userlist'); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
		<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
		<?php if( count($this->items) > 0 ): ?>
			<div class="fum_userstable">
				<table id="fum_userstable" class="table table-hover table-condensed footable" data-toggle-column="last">
					<thead>
						<tr>
							<th width="1%" class">
									<input type="checkbox" name="checkall-toggle" value=""
										title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
							</th>
							<?php foreach($tableHeader as $field): ?>
								<th><?php echo JText::_($field->label); ?></th>
							<?php endforeach ?>
							<?php foreach ($fields as $field): ?>
								<?php if(isset ($profileFields[$field])) : ?>
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
			<?php $i = 0; ?>
			<?php foreach($this->items as $userData) : ?>			
				<tr>
					<td>
						<?php echo JHtml::_('grid.id', $i, $userData->id); ?>
					</td>
					<?php foreach($tableHeader as $field): ?>
						<?php if(FumHelpersForm::isJson($userData->{$field->name})) : ?>
							<td><?php echo json_decode($userData->{$field->name}); ?></td>
						<?php else :?>
							<td><?php echo $userData->{$field->name} ?></td>
						<?php endif; ?>
					<?php endforeach ?>

					<?php foreach ($fields as $field): ?>

						<?php if ($field == 'block') :?>
							<td>
								<?php echo ($userData->block == '0') ? '<span class="fa fa-unlock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_ENABLED') . '"></span>' : '<span class="fa fa-lock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_BLOCKED') . '"></span>'; ?>
							</td>
						<?php elseif (FumHelpersForm::isJson($userData->$field)) : ?>
							<td><?php echo json_decode($userData->{$field}); ?></td>
						<?php else :?>
							<td><?php echo $userData->{$field} ?></td>
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
</div>
</form>
<script type="text/javascript">
	jQuery(function () {
		jQuery('.footable').footable();
	});
</script>
