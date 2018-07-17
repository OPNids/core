<div class="content-box">
    {{ partial("layout_partials/base_form",['fields':formGeneralSettings, 'id':'frm_mle']) }}

    <div style="margin-top: 30px;">
        <button class="btn btn-primary" id="save_mle" type="button">
            <b>Apply</b>
            <i id="frm_mle_progress" class=""></i>
        </button>
        <br/>
        <br/>
        <div id="mle-status"></div>
    </div>
</div>

<script>
    function updateStatus() {
        updateServiceControlUI('mle');
    }
    $(document).ready(function () {
        var saveButton = '#save_mle';
        var formData = {};
        var mappings = { 'frm_mle': "/api/mle/settings/get" };
        var html = '<div class="alert alert-warning" role="alert"><span id="mle-message">Please wait...</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true" >&times;</span></button ></div >';

        // load initial data
        mapDataToFormUI(mappings).done(function (data) {
            formData = data;
            formatTokenizersUI();
            // dropdown refresh
            $('.selectpicker').selectpicker('refresh');
            updateStatus();
        });

        // link save button to API set action
        $(saveButton).click(function (e) {
            $('#mle-status').html(html);
            var formId = e.currentTarget.getAttribute('id').replace('save', 'frm');

            // save settings
            saveFormToEndpoint('/api/mle/settings/set', formId, function () {

                $('#mle-message').text('Settings saved.');

                ajaxCall('/api/mle/service/reload', {}, function (data, status) {
                    if (data.status === 'ok') {
                        console.log('mapping shit to form')
                        mapDataToFormUI(mappings).done(function (data) {
                            var action = didEnabledToggle(formData, data);
                            formData = data;

                            if (action === 'start') {
                                console.log('calling start service')
                                ajaxCall('/api/mle/service/start', {}, function (data, status) {
                                    updateStatus();
                                });
                            } else if (action === 'stop') {
                                ajaxCall('/api/mle/service/stop', {}, function (data, status) {
                                    updateStatus();
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
            if (prev.frm_mle.mle.general.enabled === '0') {
                if (next.frm_mle.mle.general.enabled === '1') {
                    return 'start';
                }
            } else if (prev.frm_mle.mle.general.enabled === '1') {
                if (next.frm_mle.mle.general.enabled === '0') {
                    return 'stop';
                }
            }
        } catch (err) { }

        return false;
    }
</script>
