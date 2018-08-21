<div class="content-box">
  {{ partial("layout_partials/base_form",['fields':mainForm, 'id':'frm_filebeat']) }}

  <div style="margin-top: 30px;">
    <button class="btn btn-primary" id="save_filebeat" type="button">
      <b>Apply Settings</b>
      <i id="frm_filebeat_progress" class=""></i>
    </button>
    <button class="btn btn-primary" id="test_filebeat_config" type="button">
      <b>Test Configuration</b>
    </button>
    <button class="btn btn-primary" id="test_filebeat_connection" type="button">
      <b>Test Connection</b>
    </button>
    <br/><br/>
    <div id="filebeat-status"></div>
  </div>
</div>

<script>
  $(document).ready(function () {
    var saveButton = '#save_filebeat';
    var configButton = '#test_filebeat_config';
    var connectButton = '#test_filebeat_connection';
    var formData = {};
    var mappings = { 'frm_filebeat': "/api/filebeat/settings/get" };
    var html = '<div class="alert alert-warning" role="alert"><span id="filebeat-message">Please wait...</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" >&times;</span></button ></div >';

    // load initial data
    mapDataToFormUI(mappings).done(function (data) {
      formData = data;
      formatTokenizersUI();
      // dropdown refresh
      $('.selectpicker').selectpicker('refresh');
      updateServiceControlUI('filebeat');
    });

    $(configButton).click(function (e) {
      $('#filebeat-status').html(html);
      ajaxCall('/api/filebeat/service/testConfig', {}, function (data, status) {
        $('#filebeat-message').text(data.status);
      });
    });

    $(connectButton).click(function (e) {
      $('#filebeat-status').html(html);
      ajaxCall('/api/filebeat/service/testConnection', {}, function (data, status) {
        $('#filebeat-message').text(data.status);
      });
    });

    // link save button to API set action
    $(saveButton).click(function (e) {
      $('#filebeat-status').html(html);
      var formId = e.currentTarget.getAttribute('id').replace('save', 'frm');

      // save settings
      saveFormToEndpoint('/api/filebeat/settings/set', formId, function () {
        $('#filebeat-message').text('Settings saved.');

        ajaxCall('/api/filebeat/service/reload', {}, function (data, status) {
          if (data.status === 'OK' && !$('.has-error').length) {
            mapDataToFormUI(mappings).done(function (data) {
              var action = didEnabledToggle(formData, data);
              formData = data;

              if (action === 'start') {
                ajaxCall('/api/filebeat/service/start', {}, function (data, status) {
                  updateServiceControlUI('filebeat');
                });
              } else if (action === 'stop') {
                ajaxCall('/api/filebeat/service/stop', {}, function (data, status) {
                  updateServiceControlUI('filebeat');
                });
              }
            });
          }
        });
      });
    });
  });

  function didEnabledToggle(prev, next) {
    try {
      if (prev.frm_filebeat.filebeat.general.enabled === '0') {
        if (next.frm_filebeat.filebeat.general.enabled === '1') {
          return 'start';
        }
      } else if (prev.frm_filebeat.filebeat.general.enabled === '1') {
        if (next.frm_filebeat.filebeat.general.enabled === '0') {
          return 'stop';
        }
      }
    } catch (err) { }

    return false;
  }
</script>