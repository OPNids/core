{# Macro import #}
{% from 'OPNsense/Macros/interface.macro' import physical_interface %}
{% if helpers.exists('OPNsense.IDS.general') and OPNsense.IDS.general.enabled|default("0") == "1" %}
suricata_var_script="/usr/local/opnsense/scripts/suricata/setup.sh"
suricata_enable="YES"
suricata_netmap="YES"

{% set addFlags=[] %}
{%   for intfName in OPNsense.IDS.general.interfaces.split(',') %}
{#     store additional interfaces to addFlags #}
{%     do addFlags.append(physical_interface(intfName)) %}
{%   endfor %}
suricata_interface="{{ addFlags|join(' ') }}"
{% else %}
suricata_enable="NO"
{% endif %}
