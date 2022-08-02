<?php
/**
 * @package   FrontendUserManager
 *
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.hepta.es
 */

Use Joomla\CMS\Uri\Uri;
Use Joomla\CMS\Router\Route;
Use Joomla\CMS\HTML\HTMLHelper;
Use Joomla\CMS\Language\Text;
Use Joomla\CMS\Factory;
Use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

$app = Factory::getApplication();
$menus = $app->getMenu();
$menu = $menus->getActive();
$itemId = $menu->get('id');

$lang = Factory::getLanguage();

$extension = "com_users";
$lang->load($extension, JPATH_SITE, $lang->get('Tag'), true);

extract($displayData);

HtmlHelper::_('formbehavior.chosen', 'select');
$fieldsets = $form->getFieldsets();

?>

<form id="form-user"
	  action="<?php echo JRoute::_('index.php?option=com_frontendusermanagert&task=usermanager.save&id=' . $user->id . '&Itemid=' . $itemId); ?>"
	  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

	<?php foreach ($fieldsets as $fieldset): ?>
		<?php echo $form->renderFieldset($fieldset->name); ?>
	<?php endforeach; ?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary validate">
				<?php echo Text::_('COM_FRONTENDUSERMANAGER_EDIT_USER_SAVE'); ?>
			</button>
			<a class="btn" href="<?php echo JRoute::_('index.php?option=com_frontendusermanager&view=usermanagers&Itemid=' . $itemId); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
				<?php echo Text::_('JCANCEL'); ?>
			</a>
			<input type="hidden" name="option" value="com_frontendusermanager" />
			<input type="hidden" name="task" value="usermanager.save" />
		</div>
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>
