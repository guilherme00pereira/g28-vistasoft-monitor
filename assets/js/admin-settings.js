(function ($) {
  $("#toggleEnable").change(function (e) {
    let params = {
      action: ajaxobj.action_toggleEnable,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
      enable: this.checked ? 1 : 0,
    };
    $.post(
      ajaxobj.ajax_url,
      params,
      function (res) {
        console.log(res);
      },
      "json"
    );
  });

  $('#btnAdd').click(function (e) {
	$('#spinAdd').show();
	let params = {
		action: ajaxobj.action_Add,
		nonce: ajaxobj.g28_vistasoft_monitor_nonce,
		code: $('#codigo').val(),
	};
	$.post(
		ajaxobj.ajax_url,
		params,
		function (res) {
			console.log(res);
			$('#spinAdd').hide();
		},
		"json"
	);

  $(document).ready(function () {
    if (ajaxobj.enabled === "1") {
      setInterval(function () {
        $("#spinLog").show();
        getLogContent();
        getSummary();
        setTimeout(function () {
          $("#spinLog").hide();
        }, 1000);
      }, 10000);
    }
    $("#toggleEnable").prop("checked", ajaxobj.enabled === "1");
  });

  function getLogContent() {
    const div = $("#logFileContent");
    let params = {
      action: ajaxobj.action_ReadLog,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
    };
    $.get(
      ajaxobj.ajax_url,
      params,
      function (res) {
        div.html(res.message);
      },
      "json"
    );
  }

  function getSummary() {
    const div = $("#logSummary");
    let params = {
      action: ajaxobj.action_ReadSummary,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
    };
    $.get(
      ajaxobj.ajax_url,
      params,
      function (res) {
        div.html(res.message);
      },
      "json"
    );
  }
})(jQuery);
