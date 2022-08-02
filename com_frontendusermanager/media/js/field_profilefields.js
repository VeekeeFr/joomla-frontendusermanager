function selectAll(element)
{
	var $this = jQuery(element);

	var id = $this.data('field');

	jQuery('#' + id + ' option').prop('selected', true);
	jQuery('#' + id + ' option').trigger("liszt:updated");
}
