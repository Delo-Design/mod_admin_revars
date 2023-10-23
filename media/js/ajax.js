window.ModAdminRevars = {
	ajaxSubmit: function (event) {
		event.preventDefault();

		let form = event.target,
			formData = new FormData(form);
		formData.set('format', 'json');

		Joomla.request({
			url: form.getAttribute('action'),
			method: 'POST',
			data: formData,
			onSuccess: (response) => {
				try {
					let data = JSON.parse(response);
					Joomla.renderMessages({
						notify: [data.data.message]
					}, form);
				} catch (er) {
					Joomla.renderMessages({
						error: [er.message]
					}, form);
				}
			},
			onError: (e) => {
				try {
					let error = JSON.parse(e.response);
					Joomla.renderMessages({
						error: [error.message]
					}, form);
				} catch (er) {

				}
				console.error(e);
			}
		});

		return false;
	}
}
