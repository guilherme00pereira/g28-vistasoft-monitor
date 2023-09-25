(function ($) {

	let toggleStatus = false

	$(document).on('click', '#btnProcess', function (e) {
		const div = $('#logFileContent');
		const loading = $('#loadingLogs');
		loading.show()
		const logInterval = setInterval(() => {
			getLogContent()
		}, 3000);
		let params = {
			action: ajaxobj.action_Process,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
		}
		$.get(ajaxobj.ajax_url, params, function(res){
			loading.hide()
			div.html(res.message);
			clearInterval(logInterval);
		}, 'json');

	});

	$('#toggleStatus').change(function (e) {
		console.log(this.checked ? 1 : 0);
	});

	$(document).ready(function () {
		getLogContent()
	});

	function getLogContent() {
		const div = $('#logFileContent');
		let params = {
			action: ajaxobj.action_ReadLog,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
		}
		$.get(ajaxobj.ajax_url, params, function(res){
			div.html(res.message);
		}, 'json');
	}


}(jQuery));