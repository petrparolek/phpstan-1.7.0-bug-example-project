function initSelect2() {
	$(document).ready(function() {
		$('.select2').select2({
			width: '100%',
			theme: "bootstrap4",
			//dropdownParent: $('#myModal')
		}).on('change', function (e) {
			let form = $(this).closest('form');
			Nette.toggleForm(form[0], this);
			Nette.validateControl(this);
		});

		$('#myModal').on('shown.bs.modal', function (e) {
			$('.select2').select2({
				dropdownParent: $('#myModal')
			});
		})
	});
}

initSelect2();
