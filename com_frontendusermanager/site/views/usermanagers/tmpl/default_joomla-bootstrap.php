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


HTMLHelper::_('stylesheet', 'com_frontendusermanager/list.css', array('relative' => true));

$document = Factory::getDocument();
$document->addStyleSheet('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');


$headingFields = $this->params->get('header_fields', array("username","name","email","groups"));


$profileFields = FumHelpersForm::getFieldsArray();
$cf = FieldsHelper::getFields('com_users.user', null, true);

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

$customFields = array();

$excludedFields = $this->excludedFields;

foreach ($cf as $field)
{
	if (!in_array($field->name, $excludedFields))
	{
		$customFields[$field->id] = $field;
	}
}

$headingFields = array_diff($headingFields, $excludedFields);

$allowedFields = array_diff($coreFields, $excludedFields);

$allowedProfileFields = array_diff($profileFieldsId, $excludedFields);

$allFields = array_merge($allowedFields, $allowedProfileFields);


$fields = array_diff($allFields, $headingFields);

foreach ($headingFields as $field)
{
	if (is_numeric($field))
	{
		$headLabel = new stdClass;

		$headLabel->label = $customFields[$field]->title;
		$headLabel->name = $customFields[$field]->name;

		$tableHeader[] = $headLabel;
	}
	elseif (isset($profileFields[$field]))
	{
		$tableHeader[] = (object) array('label' => $profileFields[$field]->getAttribute('label'), 'name' => $field);
	}
	else
	{
		$tableHeader[] = (object) array('label' => $field, 'name' => $field);
	}
}
$user = Factory::getUser();

$canEdit = false;
$canBlock = false;
$canActivate = false;
$canExport = false;

if (!empty($this->criteria))
{
	if (in_array('edit', $this->criteria['permissions']))
	{
		$canEdit = true;
		$canActivate = true;
	}

	if (in_array('block', $this->criteria['permissions']))
	{
		$canBlock = true;
	}

	if (in_array('export', $this->criteria['permissions']))
	{
		$canExport = true;
	}
}
else
{
	$canEdit    = $user->authorise('core.edit', 'com_frontendusermanager');
	$canActivate    = $user->authorise('core.edit.state', 'com_frontendusermanager');
	$canBlock    = $user->authorise('core.edit.state', 'com_frontendusermanager');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers'); ?>" method="post" name="adminForm" id="adminForm">
<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

<?php if( count($this->items) > 0 ): ?>
	<div class="fum_userstable">
		<table id="fum_userstable" class="table table-hover table-condensed footable" data-toggle-column="last">
			<thead>
			<tr>
				<?php if ($canEdit) : ?>
				<th data-type="html"><?php echo Text::_('COM_FRONTENDUSERMANAGER_LIST_ACTIONS');?></th>
				<?php endif;?>

				<?php foreach ($tableHeader as $field): ?>
					<th><?php echo Text::_($field->label); ?></th>
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
			<?php foreach ($this->items as $userData) : ?>
				<tr>
				<?php if ($canEdit || $canBlock || $canActivate || $canExport) : ?>
					<td>
					<?php if ($canEdit) : ?>
						<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanager&layout=edit&id=' . $userData->id);?>" title="<?php echo JText::_('COM_FRONTENDUSERMANAGER_EDIT'); ?>"><span class="fa fa-edit"></span></a>
					<?php endif; ?>
					<?php if ($canBlock) : ?>
						<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanager&task=usermanager.toggleblock&layout=edit&id=' . $userData->id . '&block=' . $userData->block . '&' . JSession::getFormToken() . '=1');?>" alt="<?php echo JText::_('COM_FRONTENDUSERMANAGER_TOGGLE'); ?>">
								<?php echo ($userData->block == '0') ? '<span class="fa fa-lock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_USERMANAGERS_BUTTON_BLOCK') . '"></span>' : '<span class="fa fa-unlock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_USERMANAGERS_BUTTON_BLOCK') . '"></span>'; ?>
								</a>
					<?php endif; ?>
					<?php if (!empty($userData->activation)): ?>
					<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanager&task=usermanager.sendactivationlink&layout=edit&activation=' . $userData->id . '&' . JSession::getFormToken() . '=1');?>"><?php echo JText::_('COM_FRONTENDUSERMANAGER_SEND_ACTIVATION_LINK'); ?></a>
						<?php if ($canBlock) : ?>
							<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanager&task=usermanager.activateuser&layout=edit&activation=' . $userData->id . '&' . JSession::getFormToken() . '=1');?>"><?php echo JText::_('COM_FRONTENDUSERMANAGER_ACTIVATE'); ?></a>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ($canExport) : ?>
						<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanager&task=usermanager.export&id=' . $userData->id);?>" title="<?php echo JText::_('COM_FRONTENDUSERMANAGER_EXPORT'); ?>"><span class="fa fa-download"></span></a>
					<?php endif; ?>
					</td>
				<?php endif; ?>

					<?php foreach ($tableHeader as $field): ?>

						<?php if ($field->name == 'block') :?>
							<td>
								<?php echo ($userData->block == '0') ? '<span class="fa fa-unlock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_ENABLED') . '"></span>' : '<span class="fa fa-lock" aria-label="' . JText::_('COM_FRONTENDUSERMANAGER_BLOCKED') . '"></span>'; ?>
								</a>
							</td>
						<?php elseif (isset($userData->customFields[$field->name])) : ?>
							<td><?php echo $this->escape($userData->customFields[$field->name]->value); ?>
						<?php elseif (FumHelpersForm::isJson($userData->{$field->name})) : ?>
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
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="alert alert-block alert-error"><?php echo JText::_('COM_FRONTENDUSERMANAGER_USERMANAGERS_NOUSERS'); ?></div>
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
