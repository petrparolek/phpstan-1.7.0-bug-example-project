class DatepickerExtension {
	initialize(naja) {
		naja.addEventListener('success', this.datepicker.bind(this));
	}

	datepicker(event) {
		initDatepicker();
	}
}
