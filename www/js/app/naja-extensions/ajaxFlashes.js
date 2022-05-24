class AjaxFlashesExtension {
	initialize(naja) {
		naja.addEventListener('success', this.ajaxFlashes.bind(this));
	}

	ajaxFlashes(event) {
		let payload = event.detail.payload;

		if(payload.flashMessage) {
			var type = payload.flashMessage.type;
			var message = payload.flashMessage.message;

			if(type == 'success') {
				toastr.success(message);
			} else if(type == 'warning') {
				toastr.warning(message);
			} else if(type == 'danger') {
				toastr.error(message);
			}
		}
	}
}
