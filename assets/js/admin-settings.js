(function ($) {

	$('#toggleEnable').change(function (e) {
		let params = {
			action: ajaxobj.action_toggleAuto,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
			enable: this.checked ? 1 : 0
		}
		$.post(ajaxobj.ajax_url, params, function(res){
			console.log(res);
		}, 'json');
		this.checked ? $('#btnProcess').hide() : $('#btnProcess').show();
	});

	$(document).ready(function () {
		getLogContent()
		$('#toggleEnable').prop('checked', ajaxobj.autoProcess == 1 ? true : false);
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