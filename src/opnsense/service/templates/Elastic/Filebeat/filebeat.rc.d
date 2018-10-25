{% if helpers.exists('Elastic.Filebeat') %}
{% if helpers.exists('Elastic.Filebeat') and Elastic.Filebeat.general.enabled|default('0') == '1' %}
filebeat_enable="YES"
{% endif %}
{% endif %}
