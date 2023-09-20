(function ($) {

	$(document).on('click', '#btnProcess', function (e) {
		e.preventDefault();
		let params = {
			action: ajaxobj.action_Process,
			nonce: ajaxobj.g28_vistasoft_monitor_nonce,
		}
		doAjax(params, '#loadingLogs')
	});


	function doAjax(params, loadingField) {
		const div = $('#actionReturn');
		const loading = $(loadingField);
		$.post(ajaxobj.ajax_url, params, function(res){
			loading.show()
			if(res.success) {
				div.addClass('notice notice-success notice-alt')
			} else {
				div.addClass('notice notice-error notice-alt')
			}
			div.html(res.message);
			loading.hide()
		}, 'json');
	}

}(jQuery));