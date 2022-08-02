<?php

defined('JPATH_BASE') or die;
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '.js-stools-search-string',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';
$filters      = false;
if (isset($data['view']->filterForm))
{
	$filters = $data['view']->filterForm->getGroup('filter');
}

// Load search tools
JHtml::_('searchtools.form', $formSelector, $data['options']);
?>

<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<?php if ($filters) : ?>
				<label for="filter_name_username_search" class="element-invisible"
				       aria-invalid="false"><?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_FILTER_SUBMIT'); ?></label>

				<div class="btn-wrapper input-append">
					<?php echo $filters['filter_name_username_search']->input; ?>
					
				</div>

				<div class="btn-wrapper hidden-phone">
					<button type="button" class="btn hasTooltip js-stools-btn-filter" title=""
					        data-original-title="<?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_TOOLS_DESC'); ?>">
						<?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_TOOLS'); ?> <i class="caret"></i>
					</button>
				</div>

				<div class="btn-wrapper">
					<button type="button" class="btn hasTooltip js-stools-btn-clear" title=""
					        data-original-title="<?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_FILTER_CLEAR'); ?>">
						<?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_FILTER_CLEAR'); ?>
					</button>
				</div>
				<div class="btn-wrapper">
					<button type="submit" class="btn hasTooltip" title=""
					        data-original-title="<?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i> <?php echo JText::_('COM_FRONTENDUSERMANAGER_SEARCH_FILTER_SUBMIT'); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container-filters hidden-phone clearfix" style="">
		<div class="fum-filters">
		<?php if ($filters) : ?>
			<?php
				$filtersNum = count($filters);
				switch($filtersNum)
				{
					case "1":
						$class = 'fum-filters-1-1';
						break;
					case "2":
					case "4":
						$class = 'fum-filters-1-2';
						break;
					case "3":
					case "5":
					case "6":
					default:
						$class = 'fum-filters-1-3';
						break;
				}
			?>
			<?php foreach ($filters as $fieldName => $field) : ?>
				<?php if ($fieldName != 'filter_name_username_search') : ?>
					<div class="fum_filters fum_stacked <?php echo $class ?>">
						<?php echo $field->label; ?>
						<div class="js-stools-field-filter">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		</div>
	</div>
</div>
