
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

var typeToLib = {'NAMain':'Module intérieur', 'NAModule1':'Module extérieur', 'NAModule2':'Anémomètre', 'NAModule3':'Pluviomètre', 'NAModule4':'Module additionnel', 'NHC':'Healthy Home Coach'};

$('#bt_healthnetatmopro').on('click', function () {
  $('#md_modal').dialog({title: "{{Santé Netatmo}}"});
  $('#md_modal').load('index.php?v=d&plugin=netatmopro&modal=health').dialog('open');
});

$('#bt_syncnetatmopro').on('click', function () {
    $('#div_alert').showAlert({message: '{{Synchronisation en cours}}', level: 'warning'});
    $.ajax({
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/netatmopro/core/ajax/netatmopro.ajax.php",
        data: {
            action: "synchronize",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Synchronisation réalisée avec succès}}', level: 'success'});
            setTimeout( function() {
                location.reload();
            }, 2000);
        }
    });
});

function printEqLogic(_eqLogic) {
    $('#img_device').attr("src", $('.eqLogicDisplayCard[data-eqLogic_id=' + $('.li_eqLogic.active').attr('data-eqlogic_id') + '] img').attr('src'));

    var type = _eqLogic.configuration.type;
    $('.netatmopro[data-l1key=configuration][data-l2key=type]').value(typeToLib[type] + (type == 'NHC' ? '' : (' (' +  _eqLogic.configuration.station_name + ')')));
    $('.netatmopro[data-l1key=configuration][data-l2key=firmware]').value(_eqLogic.configuration.firmware);
    $('.netatmopro[data-l1key=configuration][data-l2key=_status]').value((type == 'NAMain' || type == 'NHC') ? _eqLogic.configuration.wifi_status : _eqLogic.configuration.rf_status);

    if (type == 'NAMain' || type == 'NHC') {
        $('#item-25-1').text('Adresse MAC'); 
        $('#item-25-2').text('Signal Wi-Fi');
        $('.netatmopro[data-l1key=logicalId]').value(_eqLogic.logicalId);
        $('.netatmopro[data-l1key=configuration][data-l2key=battery_percent]').closest('.form-group').hide();
    }
    else if (type == 'NAModule1' || type == 'NAModule2' || type == 'NAModule3' || type == 'NAModule4') {
        $('#item-25-1').text('Numéro de série'); 
        $('#item-25-2').text('Signal Radio');
        $('.netatmopro[data-l1key=logicalId]').value(String.fromCharCode(102+parseInt(_eqLogic.logicalId.slice(0,2))) + _eqLogic.logicalId.replace(/:/g, '').slice(6));
        $('.netatmopro[data-l1key=configuration][data-l2key=battery_percent]').closest('.form-group').show();
        $('.netatmopro[data-l1key=configuration][data-l2key=battery_percent]').value(_eqLogic.configuration.battery_percent + '%');
    }
}


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</div>';
    tr += '<div class="col-sm-6">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icone</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr expertModeVisible" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}">';
    tr += '<input class="cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="margin-top : 5px;"> ';
    tr += '<input class="cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="margin-top : 5px;">';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').prop('disabled', true);
    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').prop('disabled', true);
}
