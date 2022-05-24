function initDatepicker() {
	$(':text[data-provide="datepicker"]').datepicker({
		language: 'cs',
		zIndexOffset: 99999
	}).on('changeDate', function() {
		$(this).removeClass('is-invalid');
		$(this).addClass('is-valid');
	});
}

initDatepicker();
