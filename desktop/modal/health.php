<?php

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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('netatmopro');
$eqLogics = netatmopro::byType('netatmopro');

$typeToInfo = array(
    'NAMain'    => array( 'libelle' => 'Module intérieur', 'signal' => 'wifi_status'),
    'NAModule1' => array( 'libelle' => 'Module extérieur', 'signal' => 'rf_status'),
    'NAModule2' => array( 'libelle' => 'Anémomètre', 'signal' => 'rf_status'),
    'NAModule3' => array( 'libelle' => 'Pluviomètre', 'signal' => 'rf_status'),
    'NAModule4' => array( 'libelle' => 'Module additionnel', 'signal' => 'rf_status'),
    'NHC'       => array( 'libelle' => 'Healthy Home Coach', 'signal' => 'wifi_status'),
);
?>

<table class="table table-condensed tablesorter" id="table_healthnetatmopro">
        <thead>
                <tr>
                        <th>{{Image}}</th>
                        <th>{{Module}}</th>
                        <th>{{ID}}</th>
                        <th>{{Modèle}}</th>
                        <th>{{Identfiant}}</th>
                        <th>{{Batterie}}</th>
                        <th>{{Firmware}}</th>
                        <th>{{Signal Wifi/RF}}</th>
                        <th>{{Dernière communication}}</th>
                        <th>{{Date création}}</th>
                </tr>
        </thead>
        <tbody>
         <?php
foreach ($eqLogics as $eqLogic) {
        echo '<tr>';
        echo '<td><img src="' . $eqLogic->getIconFile() . '" height="55" width="55" /></td>';
        echo '<td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getId() . '</span></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $typeToInfo[$eqLogic->getConfiguration('type')][libelle] . '</span></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getLogicalId() . '</span></td>';
        
        $battery = $eqLogic->getCache('batteryStatus', '');
        if ($battery == '') {
                $battery_status = '<span class="label label-primary" style="font-size : 1em;" title="{{Secteur}}"><i class="fa fa-plug"></i></span>';
        } elseif ($battery > 75) {
                $battery_status = '<span class="label label-success" style="font-size : 1em;">' . $battery . '%</span>';
        } elseif ($battery > 50) {
                $battery_status = '<span class="label label-warning" style="font-size : 1em;">' . $battery . '%</span>';
        } else {
                $battery_status = '<span class="label label-danger" style="font-size : 1em;">' . $battery . '%</span>';
        }
        echo '<td>' . $battery_status . '</td>';

        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('firmware') . '</span></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration($typeToInfo[$eqLogic->getConfiguration('type')]['signal']) . '</span></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getStatus('lastCommunication') . '</span></td>';
        echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('createtime') . '</span></td>';
        echo '</tr>';
}
?>
        </tbody>
</table>

