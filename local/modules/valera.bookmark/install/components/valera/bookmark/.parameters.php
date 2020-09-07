<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("valera.bookmark"))
    return;

$arUrlData = parse_url($_SERVER["HTTP_REFERER"]);
if (empty($arUrlData["path"])) {
    $arUrlData["path"] = '/';
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "FOLDER" => Array(
            "PARENT" => "BASE",
            "NAME" => "Каталог (относительно корня сайта)",
            "TYPE" => "STRING",
            "DEFAULT" => $arUrlData["path"],
        ),
        "PAGE_SIZE" => Array(
            "PARENT" => "BASE",
            "NAME" => "Количество элементов на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "5",
        ),
        "CACHE_TIME" => Array("DEFAULT"=>3600),
    ),
);
?>
