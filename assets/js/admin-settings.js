(function ($) {

	let autoProcess = false;

	$(document).on('click', '#btnProcess', function (e) {
		const div = $('#logFileContent');
		const loading = $('#loadingLogs');
		loading.show()
		let params = {
			action: ajaxobj.action_Process,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
		}
		$.get(ajaxobj.ajax_url, params, function(res){
			loading.hide()
			div.html(res.message);
		}, 'json');
	});

	$(document).on('click', '.toggle-button', function (e) {

	});


}(jQuery));