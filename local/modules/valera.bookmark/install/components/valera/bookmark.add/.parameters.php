<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("valera.bookmark"))
    return;

$arComponentParameters = array(
    "PARAMETERS" => array(
        "FOLDER" => Array(
            "PARENT" => "BASE",
            "NAME" => "Каталог (относительно корня сайта)",
            "TYPE" => "STRING",
            "DEFAULT" => $arUrlData["path"],
        )
    ),
);
