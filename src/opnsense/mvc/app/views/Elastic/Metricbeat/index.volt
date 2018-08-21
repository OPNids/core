<div class="content-box">
  {{ partial("layout_partials/base_form",['fields':mainForm, 'id':'frm_metricbeat']) }}

  <div style="margin-top: 30px;">
    <button class="btn btn-primary" id="save_metricbeat" type="button">
      <b>Apply Settings</b>
      <i id="frm_metricbeat_progress" class=""></i>
    </button>
    <button class="btn btn-primary" id="test_metricbeat_config" type="button">
      <b>Test Configuration</b>
    </button>
    <button class="btn btn-primary" id="test_metricbeat_connection" type="button">
      <b>Test Connection</b>
    </button>
    <br/><br/>
    <div id="metricbeat-status"></div>
  </div>
</div>

<script>
  $(document).ready(function () {
    var saveButton = '#save_metricbeat';
    var configButton = '#test_metricbeat_config';
    var connectButton = '#test_metricbeat_connection';
    var formData = {};
    var mappings = { 'frm_metricbeat': "/api/metricbeat/settings/get" };
    var html = '<div class="alert alert-warning" role="alert"><span id="metricbeat-message">Please wait...</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" >&times;</span></button ></div >';

    // load initial data
    mapDataToFormUI(mappings).done(function (data) {
      formData = data;
      formatTokenizersUI();
      // dropdown refresh
      $('.selectpicker').selectpicker('refresh');
      updateServiceControlUI('metricbeat');
    });

    $(configButton).click(function (e) {
      $('#metricbeat-status').html(html);
      ajaxCall('/api/metricbeat/service/testConfig', {}, function (data, status) {
        $('#metricbeat-message').text(data.status);
      });
    });

    $(connectButton).click(function (e) {
      $('#metricbeat-status').html(html);
      ajaxCall('/api/metricbeat/service/testConnection', {}, function (data, status) {
        $('#metricbeat-message').text(data.status);
      });
    });

    // link save button to API set action
    $(saveButton).click(function (e) {
      $('#metricbeat-status').html(html);
      var formId = e.currentTarget.getAttribute('id').replace('save', 'frm');

      // save settings
      saveFormToEndpoint('/api/metricbeat/settings/set', formId, function () {
        $('#metricbeat-message').text('Settings saved.');

        ajaxCall('/api/metricbeat/service/reload', {}, function (data, status) {
          if (data.status === 'OK' && !$('.has-error').length) {
            mapDataToFormUI(mappings).done(function (data) {
              var action = didEnabledToggle(formData, data);
              formData = data;

              if (action === 'start') {
                ajaxCall('/api/metricbeat/service/start', {}, function (data, status) {
                  updateServiceControlUI('metricbeat');
                });
              } else if (action === 'stop') {
                ajaxCall('/api/metricbeat/service/stop', {}, function (data, status) {
                  updateServiceControlUI('metricbeat');
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
      if (prev.frm_metricbeat.metricbeat.general.enabled === '0') {
        if (next.frm_metricbeat.metricbeat.general.enabled === '1') {
          return 'start';
        }
      } else if (prev.frm_metricbeat.metricbeat.general.enabled === '1') {
        if (next.frm_metricbeat.metricbeat.general.enabled === '0') {
          return 'stop';
        }
      }
    } catch (err) { }

    return false;
  }
</script>