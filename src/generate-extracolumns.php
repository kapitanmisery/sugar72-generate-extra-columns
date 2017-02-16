<?php


if (!defined('sugarEntry')) define('sugarEntry', true);

ini_set("display_errors", "On");
require_once("include/entryPoint.php");
require_once ("modules/Administration/QuickRepairAndRebuild.php");

global $sugar_config, $db;

define("BILLING_LIST_VIEW_METADATA_PATH", "modules/bbcrm_Billing/clients/base/views/list/list.php");

$sql= 'SELECT
"my_field_1" as field_1,
"My Field" as field_1_label,
"my_field_2" as field_2,
"New Label" as field_2_label,
"my_field_3" as field_3,
"Something Label" as field_3_label';

$results  = $db->fetchOne($sql);

$additionalDefs = array();

foreach($results as $key => $value) {


    if(substr($key, -6) != "_label") {
        $additionalDefs[] = array(
            "name" => $key,
            "type" => "base-dynamic",
            "sortable" => false,
            "label" => $results[$key . "_label"],
        );
    }
}

$currentDefs = include_once(BILLING_LIST_VIEW_METADATA_PATH);
echo "<pre>";
print_r($currentDefs);
$currentDefs["panels"][0]["fields"] = array_merge($currentDefs["panels"][0]["fields"], $additionalDefs);


write_return_array_to_file(
    "viewdefs[\"bbcrm_Billing\"][\"base\"][\"view\"][\"list\"]",
    $currentDefs,
    BILLING_LIST_VIEW_METADATA_PATH);


ob_flush();
$current_user = new User();
$current_user->retrieve(1);
$randc = new RepairAndClear();
$actions = array();
$actions[] = 'clearAll';
$randc->repairAndClearAll($actions, array(translate('LBL_ALL_MODULES')), false,true,'');


echo "<br /><br /><a href=\"index.php?module=Administration&action=index\">{$GLOBALS['mod_strings']['LBL_DIAGNOSTIC_DELETE_RETURN']}</a>";
unset($current_user);
