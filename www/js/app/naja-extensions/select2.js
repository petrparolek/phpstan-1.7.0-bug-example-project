class Select2Extension {
	initialize(naja) {
		naja.addEventListener('success', this.select2.bind(this));
	}

	select2(event) {
		initSelect2();
	}
}
