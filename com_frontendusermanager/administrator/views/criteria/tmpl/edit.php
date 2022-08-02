<?php
/**
 * @version    CVS: 0.1.0
 * @package    Com_Frontendusermanager
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  2016 Hepta Technologies SL
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_frontendusermanager/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {

	});

	Joomla.submitbutton = function (task) {
		if (task == 'criteria.cancel') {
			Joomla.submitform(task, document.getElementById('criteria-form'));
		}
		else {

			if (task != 'criteria.cancel' && document.formvalidator.isValid(document.id('criteria-form'))) {

				Joomla.submitform(task, document.getElementById('criteria-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_frontendusermanager&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="criteria-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'criteria')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'criteria', JText::_('COM_FRONTENDUSERMANAGER_CRITERIA_TAB_TITLE_CRITERIA', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<?php echo $this->form->renderField('name'); ?>
					<?php echo $this->form->renderField('usergroups'); ?>
					<?php echo $this->form->renderField('excludedUsers'); ?>
					<?php echo $this->form->renderField('languages'); ?>
					<?php echo $this->form->renderField('customfields'); ?>
					<?php echo $this->form->renderField('profilefields'); ?>
					<div class="alert alert-info">
						<?php echo JText::_('COM_FRONTENDUSERMANAGER_CRITERIA_EDIT_MANAGED_USERS_WARNING'); ?>
					</div>
					<?php echo $this->form->renderField('managed_list'); ?>
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'manager', JText::_('COM_FRONTENDUSERMANAGER_CRITERIA_TAB_TITLE_MANAGER', true)); ?>
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
						<?php echo $this->form->renderField('managers_list'); ?>
					</fieldset>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_FRONTENDUSERMANAGER_PERMISSIONS_TAB_TITLE_MANAGER', true)); ?>
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<?php echo $this->form->renderField('permissions'); ?>
						<?php echo $this->form->renderField('excludedFields'); ?>
					</fieldset>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
