<?
if(!$USER->IsAdmin())
    return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
?>
<div>
    У модуля нет настроек
</div>