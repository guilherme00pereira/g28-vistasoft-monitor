(function ($) {

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
		let params = {
			action: ajaxobj.action_toggleAuto,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
			auto: this.checked ? 1 : 0
		}
		$.post(ajaxobj.ajax_url, params, function(res){
			console.log(res);
		}, 'json');
		this.checked ? $('#btnProcess').hide() : $('#btnProcess').show();
	});

	$(document).ready(function () {
		getLogContent()
		$('#toggleStatus').prop('checked', ajaxobj.autoProcess == 1 ? true : false);
		ajaxobj.autoProcess == 1 ? $('#btnProcess').hide() : $('#btnProcess').show();
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