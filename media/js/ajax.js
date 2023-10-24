window.ModAdminRevars = {
	ajaxSubmit: function (event) {
		event.preventDefault();

		let form = (event.target.getAttribute('mod_admin_revars') === 'form') ? event.target :
			event.target.closest('[mod_admin_revars="form"]');
		if (!form) {
			return;
		}


		let messagesContainer = form,
			messages = form.querySelector('[mod_admin_revars="messages"]');
		form.querySelectorAll('button').forEach(function (button) {
			button.setAttribute('disabled', 'true');
		})

		if (messages) {
			messagesContainer = messages;
			messages.style.display = 'none';
		}

		let formData = new FormData(form);
		formData.set('format', 'json');
		this.sendAjax(form.getAttribute('action'), formData).then(function (response) {
			form.querySelectorAll('button').forEach(function (button) {
				button.removeAttribute('disabled');
			});
			if (messages) {
				messages.style.display = '';
			}
			Joomla.renderMessages({
				notify: [response.message]
			}, messagesContainer);
		}).catch(function (error) {
			form.querySelectorAll('button').forEach(function (button) {
				button.removeAttribute('disabled');
			});

			if (messages) {
				messages.style.display = '';
			}

			Joomla.renderMessages({
				error: [error.message]
			}, messagesContainer);
		});

		return false;
	},

	sendAjax: function (url, data) {
		return new Promise((success, error) => {
			if (!data) {
				error({message: 'Empty data'});
			}
			Joomla.request({
				url: url,
				method: 'POST',
				data: data,
				onSuccess: (response) => {
					try {
						let parse = JSON.parse(response);
						success(parse.data)
					} catch (er) {
						error(er);
					}
				},
				onError: (e) => {
					if (e.response) {
						try {
							let error = JSON.parse(e.response);
							error(error);
						} catch (er) {
							error(er);
							console.error(e);
						}
					} else {
						error({message: 'Unknown error'});
						console.error(e);
					}
				}
			});
		});
	}
}
