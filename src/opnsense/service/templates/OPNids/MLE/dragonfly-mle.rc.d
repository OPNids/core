{% if helpers.exists('OPNids.MLE') %}
{% if helpers.exists('OPNids.MLE.general') and OPNids.MLE.general.enabled|default('0') == '1' %}
# REQUIRE: redis
dragonflymle_enable="YES"
{% endif %}
{% endif %}
