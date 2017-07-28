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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

define('__ROOT__', dirname(dirname(dirname(__FILE__))));
require_once (__ROOT__.'/3rparty/Netatmo-API-PHP/src/Netatmo/autoload.php');

class netatmopro extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

      public static function synchronize($update = true) {
          log::add('netatmopro', 'debug', 'synchronize:: update:' . var_export($update, true));

          $config = array("client_id" => config::byKey('client_id', 'netatmopro'),
                          "client_secret" => config::byKey('client_secret', 'netatmopro'),
                          "username" => config::byKey('username', 'netatmopro'),
                          "password" => config::byKey('password', 'netatmopro'),
                          "scope" => 'read_station read_homecoach');

          $scope = ( $update === true ? config::byKey('scope', 'netatmopro') : [] );
          log::add('netatmopro', 'debug', 'synchronize:: scope:' . var_export($scope, true));

          if ( $update === false || ( $update === true && preg_match("/read_station/", $scope) ) ) {
              $client = new Netatmo\Clients\NAWSApiClient($config);
              $tokens = $client->getAccessToken();

              $data = $client->getData();

              log::add('netatmopro', 'debug', 'synchronize:: WS:' . var_export($data, true));

	      foreach ($data['devices'] as $device) {
                  foreach ($device['modules'] as $module) {
                      $eqLogic = eqLogic::byLogicalId($module['_id'], 'netatmopro');
                      if ( !is_object($eqLogic) && $update === false ) {
                          log::add('netatmopro', 'debug', 'synchronize:: M_CREATE_IN_PROGRESS:' . var_export($module, true));
                          $eqLogic = new netatmopro();
                          $eqLogic->createEqLogic($module);
                          $eqLogic->createCmd();
                      }

                      if ( $module['type'] == 'NAModule3' ) {
                          log::add('netatmopro', 'error', 'synchronize:: M_UPDATE_IN_PROGRESS:' . var_export($module, true));
                      }

                      if ( is_object($eqLogic) && ( $update === false || ( $update === true && $eqLogic->getConfiguration('last_message') < $module['last_message'] ) ) ) {
                          log::add('netatmopro', 'debug', 'synchronize:: M_UPDATE_IN_PROGRESS:' . var_export($module, true));
                          foreach ($eqLogic->getConfiguration('dashboard') as $key) {
                              $eqLogic->checkAndUpdateCmd($key, $module['dashboard_data'][$key]);
                          }
                          $eqLogic->checkAndUpdateCmd('battery_percent', $module['battery_percent']);

                          $eqLogic->setConfiguration('station_name', $device['station_name']);
                          $eqLogic->setConfiguration('firmware', $module['firmware']);
                          $eqLogic->setConfiguration('rf_status', $module['rf_status']);
                          $eqLogic->setConfiguration('battery_percent', $module['battery_percent']);
                          $eqLogic->setConfiguration('last_message', $module['last_message']);
                          $eqLogic->batteryStatus($module['battery_percent']);
                          $eqLogic->save();
                      }
                  }

                  $eqLogic = eqLogic::byLogicalId($device['_id'], 'netatmopro');
                  if (!is_object($eqLogic) && $update === false) {
                      log::add('netatmopro', 'debug', 'synchronize:: D_CREATE_IN_PROGRESS:' . var_export($device, true));
                      $eqLogic = new netatmopro();
                      $eqLogic->createEqLogic($device);
                      $eqLogic->createCmd();
                  }

                  if ( is_object($eqLogic) && ( $update === false || ( $update === true && $eqLogic->getConfiguration('last_status_store') < $device['last_status_store'] ) ) ) {
                      log::add('netatmopro', 'debug', 'synchronize:: D_UPDATE_IN_PROGRESS:' . var_export($device, true));
                      foreach ($eqLogic->getConfiguration('dashboard') as $key) {
                          $eqLogic->checkAndUpdateCmd($key, $device['dashboard_data'][$key]);
                      }
                      $eqLogic->setConfiguration('station_name', $device['station_name']);
                      $eqLogic->setConfiguration('firmware', $device['firmware']);
                      $eqLogic->setConfiguration('wifi_status', $device['wifi_status']);
                      $eqLogic->setConfiguration('last_status_store', $device['last_status_store']);
                      $eqLogic->save();
                  }

                  if ($update === false) {
                      $scope[] = 'read_station';
                  }
              }
          }

          if ( $update === false || ( $update === true && preg_match("/read_homecoach/", $scope) ) ) {
              $client = new Netatmo\Clients\NAHomeApiClient($config);
              $tokens = $client->getAccessToken();

              $data = $client->getData();

              log::add('netatmopro', 'debug', 'synchronize:: HOME:' . var_export($data, true));

	      foreach ($data['devices'] as $device) {
                  $eqLogic = eqLogic::byLogicalId($device['_id'], 'netatmopro');
                  if (!is_object($eqLogic) && $update === false) {
                      log::add('netatmopro', 'debug', 'synchronize:: D_CREATE_IN_PROGRESS:' . var_export($device, true));
                      $eqLogic = new netatmopro();
                      $eqLogic->createEqLogic($device);
                      $eqLogic->createCmd();
                  }

                  if ( is_object($eqLogic) && ( $update === false || ( $update === true && $eqLogic->getConfiguration('last_status_store') < $device['last_status_store'] ) ) ) {
                      log::add('netatmopro', 'debug', 'synchronize:: D_UPDATE_IN_PROGRESS:' . var_export($device, true));
                      foreach ($eqLogic->getConfiguration('dashboard') as $key) {
                          $eqLogic->checkAndUpdateCmd($key, $device['dashboard_data'][$key]);
                      }
                      $eqLogic->setConfiguration('firmware', $device['firmware']);
                      $eqLogic->setConfiguration('wifi_status', $device['wifi_status']);
                      $eqLogic->setConfiguration('last_status_store', $device['last_status_store']);
                      $eqLogic->save();
                  }

                  if ($update === false) {
                      $scope[] = 'read_homecoach';
                  }
              }
          }

          if ( $update === false && !empty($scope) ) {
              log::add('netatmopro', 'debug', 'synchronize:: scope:' . var_export(implode(';', $scope), true));
              config::save('scope', implode(';', $scope), 'netatmopro');
          }
      }

      public static function getmeasure($device_id = null, $module_id = null, $type = null) {
          log::add('netatmopro', 'debug', 'getmeasure::');

          $config = array("client_id" => config::byKey('client_id', 'netatmopro'),
                          "client_secret" => config::byKey('client_secret', 'netatmopro'),
                          "username" => config::byKey('username', 'netatmopro'),
                          "password" => config::byKey('password', 'netatmopro'));
          $client = new Netatmo\Clients\NAWSApiClient($config);
          $tokens = $client->getAccessToken();

          $data = $client->getMeasure($device_id, $module_id, 'max', $type, time()-(3600*24), time(), null, false);
      }


    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
     */
      public static function cron() {
          netatmopro::synchronize(true);
          //netatmopro::getmeasure('70:ee:50:20:c9:02', null, 'Temperature');
      }

    /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
          netatmopro::synchronize(true);
      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
        
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    public function getIconFile() {
        log::add('netatmopro', 'debug', 'getIconFile::');

        $type = $this->getConfiguration('type');
        $filename = __ROOT__.'/core/config/devices/'.$type.'/'.$type.'.png';

        return (file_exists($filename) === true ? ('plugins/netatmopro/core/config/devices/'.$type.'/'.$type.'.png') : ('plugins/netatmopro/core/config/devices/default.png'));
    }

    private function loadConfigFile() {
        log::add('netatmopro', 'debug', 'loadConfigFile::');

        $type = $this->getConfiguration('type');
        $filename = __ROOT__.'/core/config/devices/'.$type.'/'.$type.'.json';
        if ( file_exists($filename) === false ) {
            throw new Exception('Impossible de trouver le fichier de configuration pour l\'équipement de type ' . $this->getConfiguration('type'));
        }
        $content = file_get_contents($filename);
        if (!is_json($content)) {
            throw new Exception('Le fichier de configuration \'' . $filename . '\' est corrompu');
        }

        $data = json_decode($content, true);
        if (!is_array($data) || !isset($data['configuration']) || !isset($data['commands'])) {
            throw new Exception('Le fichier de configuration \'' . $filename . '\' est invalide');
        }

        return $data;
    }

    private function createEqLogic($data = null) {
        log::add('netatmopro', 'debug', 'createEqLogic:: data:' . var_export($data, true));

        $this->setName($data['type'] == 'NHC' ? $data['name'] : $data['module_name']);
        $this->setLogicalId($data['_id']);
        $this->setEqType_name('netatmopro');
        $this->setIsEnable(1);
        $this->setConfiguration('type', $data['type']);

        $config = $this->loadConfigFile();

        foreach ($config['configuration'] as $key => $value) {
            $this->setConfiguration($key, $value);
        }

        $this->save();
    }

    private function createCmd() {
        log::add('netatmopro', 'debug', 'createCmd::');

        $config = $this->loadConfigFile();

        $dashboard = array();
        $i = 0;

	foreach ($config['commands'] as $command) {
            $cmd = new netatmoproCmd();
            $cmd->setOrder($i++);
            $cmd->setEqLogic_id($this->getId());
            utils::a2o($cmd, $command);
            $cmd->save();

            $dashboard[] = $command['logicalId'];
        }

        if(($key = array_search('battery_percent', $dashboard)) !== false) {
            unset($dashboard[$key]);
        }
        $this->setConfiguration('dashboard', $dashboard);
        $this->save();
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {
          $replace = $this->preToHtml($_version);
          if (!is_array($replace)) {
              return $replace;
          }
          $version = jeedom::versionAlias($_version);

          return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, strtolower($this->getConfiguration('type')), 'netatmopro')));
      }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class netatmoproCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
