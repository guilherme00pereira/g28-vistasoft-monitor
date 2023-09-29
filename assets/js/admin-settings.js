(function ($) {
  
  function getLogContent() {
    let params = {
      action: ajaxobj.action_ReadLog,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
    };
    $.get(
      ajaxobj.ajax_url,
      params,
      function (res) {
        $("#logFileContent").html(res.message);
      },
      "json"
    );
  }

  function getSummary() {
    let params = {
      action: ajaxobj.action_ReadSummary,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
    };
    $.get(
      ajaxobj.ajax_url,
      params,
      function (res) {
        $("#logSummary").html(res.message);
      },
      "json"
    );
  }

  function startInterval() {
    setInterval(function () {
      $("#spinLog").show();
      getLogContent();
      getSummary();
      setTimeout(function () {
        $("#spinLog").hide();
      }, 1000);
    }, 10000);
  }

  $(document).ready(function () {
    if (ajaxobj.enabled === "1") {
      startInterval();
    }
    $("#toggleEnable").prop("checked", ajaxobj.enabled === "1");
  });

  $("#toggleEnable").change(function (e) {
    startInterval();
    let params = {
      action: ajaxobj.action_toggleEnable,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
      enable: this.checked ? 1 : 0,
    };
    $.post(
      ajaxobj.ajax_url,
      params,
      function (res) {
        
      },
      "json"
    );
  });

  $("#btnAdd").click(function (e) {
    $("#spinAdd").show();
    let params = {
      action: ajaxobj.action_AddRealState,
      nonce: ajaxobj.g28_vistasoft_monitor_nonce,
      code: $("#codigo").val(),
    };
    $.post(
      ajaxobj.ajax_url,
      params,
      function (res) {
        console.log(res);
        $("#addFileContent").html(res.message);
        $("#spinAdd").hide();
      },
      "json"
    );
  });

})(jQuery);
