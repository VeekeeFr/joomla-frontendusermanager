<?php
/**
 * @package    FrontendUserManager
 *
 * @author     Carlos Cámara <carlos@hepta.es>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.hepta.es
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

HTMLHelper::_('stylesheet', 'com_frontendusermanager/user.css', array('relative' => true));

$layout = new FileLayout('user.edit');
$data = array();
$data['user'] = $this->item;
$data['form'] = $this->form;
?>
<div class="fum-edit-user">
	<?php echo $layout->render($data);?>
</div>
