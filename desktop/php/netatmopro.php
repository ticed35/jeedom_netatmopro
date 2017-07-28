<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'netatmopro');
$eqLogics = eqLogic::byType('netatmopro');
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
foreach ($eqLogics as $eqLogic) {
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <legend><i class="fa fa-cog"></i> {{Gestion}}
    </legend>

    <div class="eqLogicThumbnailContainer">
      <div class="cursor" id="bt_syncnetatmopro" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-refresh" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Synchronisation}}</center></span>
    </div>
      <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
    </div>
      <div class="cursor" id="bt_healthnetatmopro" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-medkit" style="font-size : 6em;color:#767676;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
    </div>
</div>

    <legend><i class="icon nature-weather1"></i> {{Weather Station}}
    </legend>

    <div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
            if ( $eqLogic->getConfiguration('scope') == 'read_station' ) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                echo "<center>";
                echo '<img class="lazy" src="' . $eqLogic->getIconFile() . '" height="105" width="95" />';
                echo "</center>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                echo '</div>';
            }
        }
        ?>
</div>

    <legend><i class="fa fa-home"></i> {{Healthy Home Coach}}
    </legend>

    <div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
            if ( $eqLogic->getConfiguration('scope') == 'read_homecoach' ) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                echo "<center>";
                echo '<img class="lazy" src="' . $eqLogic->getIconFile() . '" height="105" width="95" />';
                echo "</center>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                echo '</div>';
            }
        }
        ?>
</div>

</div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 90px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                        <div class="row">
                                <div class="col-sm-6" style="margin-top: 20px">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement Netatmo}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" >{{Objet parent}}</label>
                            <div class="col-sm-6">
                                <select class="form-control eqLogicAttr" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Catégorie}}</label>
                            <div class="col-sm-8">
                                <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" ></label>
                            <div class="col-sm-8">
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                            </div>
                        </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{{Commentaire}}</label>
                <div class="col-sm-6">
                    <textarea class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="comment"></textarea>
                </div>
            </div>
                                                </fieldset>
                                                </form>
                                                </div>
                                                <div class="col-sm-6" style="margin-top: 20px">
                                                        <form class="form-horizontal">
                                                        <fieldset>
                                                        <div class="form-group">
                                                                <label class="col-sm-3 control-label">{{Modèle}}</label>
                                                                <div class="col-sm-8">
                                                                        <span class="netatmopro label label-info" style="font-size : 1em;" data-l1key="configuration" data-l2key="type"></span>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                                <label class="col-sm-3 control-label" id="item-25-1">{{Numéro de série}}</label>
                                                                <div class="col-sm-8">
                                                                        <span class="netatmopro label label-info" style="font-size : 1em;" data-l1key="logicalId"></span>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                                <label class="col-sm-3 control-label">{{Logiciel interne}}</label>
                                                                <div class="col-sm-2">
                                                                        <span class="netatmopro label label-info" style="font-size : 1em;" data-l1key="configuration" data-l2key="firmware"></span>
                                                                </div>
                                                                <label class="col-sm-3 control-label" id="item-25-2">{{Signal radio}}</label>
                                                                <div class="col-sm-2">
                                                                        <span class="netatmopro label label-info" style="font-size : 1em;" data-l1key="configuration" data-l2key="_status"></span>
                                                                </div>
                                                        </div>
                                                        <div class="form-group">
                                                                <label class="col-sm-3 control-label">{{Batterie}}</label>
                                                                <div class="col-sm-8">
                                                                        <span class="netatmopro label label-info" style="font-size : 1em;" data-l1key="configuration" data-l2key="battery_percent"></span>
                                                                </div>
                                                        </div>

                                                        <center>
                                                                <img src="core/img/no_image.gif" data-original=".jpg" id="img_device" class="img-responsive" style="max-height : 350px; margin-top : 30px"/>
                                                        </center>
                    </fieldset>
                </form>
            </div>
                        </div>
                        </div>

            <div role="tabpanel" class="tab-pane" id="commandtab">

                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 250px;">{{Nom}}</th>
                            <th style="width: 100px;">{{Type}}</th>
                            <th style="width: 200px;">{{Paramètres}}</th>
                            <th style="width: 50px;">{{Options}}</th>
                            <th style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'netatmopro', 'js', 'netatmopro');?>
<?php include_file('core', 'plugin.template', 'js');?>
