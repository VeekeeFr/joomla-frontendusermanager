<?php
/**
 * @version     1.0.0
 * @package     com_frontendusermanager
 * @copyright   Copyright (C) 2015. Joomla Design Studios Inc All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Carlos <carlos@joomladesigner.com> - http://www.joomladesignstudios.com
 */

defined('_JEXEC') or die;

class FumFormFieldMultipleUsers extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'fum.multipleusers';

	/**
	 * Cached array of the category items.
	 *
	 * @var  array
	 */
	protected static $options = array();


	/**
	 * Translate options labels ?
	 *
	 * @var  boolean
	 */
	protected $translate = true;

	public function getInput()
	{
		$html     = array();
		$recordId = (int) $this->form->getValue('id');
		$size     = ($v = $this->element['size']) ? ' size="' . $v . '"' : '';
		$class    = ($v = $this->element['class']) ? ' class="' . $v . '"' : 'class="text_area"';
		$required = ($v = $this->element['required']) ? ' required="required"' : '';
		$hash = md5($this->fieldname);

		$html[] = '<div class="fum_multipleusers_block ' . $this->fieldname . '" data-field="' . $this->fieldname . '">' . $this->showValues() . '</div>';

		$html[] = '<span class="input-append"><input type="hidden" ' . $required . ' readonly="readonly" id="' . $this->id
			. '" value="' . json_encode($this->value) . '"' . $size . $class . ' />';
		$html[] = '<a href="#' . $this->id .'Modal" role="button" class="btn btn-primary" data-toggle="modal" title="' . JText::_('JSELECT') . '">'
			. '<span class="icon-list icon-white"></span> '
			. JText::_('JSELECT') . '</a></span>';
		$link = JRoute::_('index.php?option=com_frontendusermanager&tmpl=component&view=userlist&layout=modal&inputField=' . $hash );
		$html[] = JHtml::_(
			'bootstrap.renderModal',
			$this->id . 'Modal',
			array(
				'url'        => $link,
				'title'      => JText::_('COM_FRONTENDUSERMANAGER_CHOOSE_USER_LABEL'),
				'width'      => '800px',
				'height'     => '300px',
				'modalWidth' => '80',
				'bodyHeight' => '70',
				'footer'     => '<a type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
						. JText::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</a>'
			)
		);

		JFactory::getDocument()->addScriptDeclaration('
			function fumAddUser_' . $hash . '(id, name, username)
			{				
				$fumBlock = jQuery("div.fum_multipleusers_block[data-field=\'' . $this->fieldname . '\']");
				$div = jQuery("<div/>").attr({
					class: "fum_userblock",
					"data-userid": id,
					"data-field": "' . $this->fieldname . '"
				}).data({
					userid: id,
					field: "' . $this->fieldname . '",
				}).html("<button class=\'btn btn-danger btn-mini\' type=\'button\' onclick=\'fumRemoveUser_' . $hash . '(" + id + ")\'><span class=\'icon icon-cancel\'></span></button>" + name + " (" + username + ")").appendTo($fumBlock);
console.log(id);
				$input = jQuery("<input/>").attr({
					type: "hidden",
					name: "' . $this->name .'",
					value: id,
				}).appendTo($fumBlock);
			}
			function fumRemoveUser_' . $hash . '(id)
			{
				$div = jQuery("div").find("[data-userid=\'" + id +"\'][data-field=\'' . $this->fieldname . '\']");
				$input = jQuery(".' . $this->fieldname . ' input[value=" + id + "]");
				$div.remove();
				$input.remove();
			}

		');

		return implode("\n", $html);
	}

	public function showValues()
	{
		$hash = md5($this->fieldname);
		$html 	= array();

		if($this->value != "")
		{
			$values = $this->value;
				foreach($values as $id)
				{
					$user = JFactory::getUser($id);

					$html[] = '<div class="fum_userblock" data-userid="' . $user->id . '" data-field="' . $this->fieldname . '">
								<button class="btn btn-danger btn-mini" type="button" onclick="fumRemoveUser_' . $hash . '('. $user->id .')">
									<span class="icon icon-cancel"></span>
							   </button> ' . $user->name . '(' . $user->username . ')</div>';
					$html[] = '<input name="' . $this->name . '" value="' . $user->id .'" type="hidden">';
				}
		}

		return implode($html);
	}


	public function getModal()
	{
		$url = 'index.php?option=com_frontendusermanager&view=userlist&tmpl=component';
	}

}
