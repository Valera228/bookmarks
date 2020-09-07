<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("valera.bookmark"))
    return;

$arComponentParameters = array(
    "PARAMETERS" => array(
        "PAGE_SIZE" => Array(
            "PARENT" => "BASE",
            "NAME" => "Количество элементов на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "5",
        ),
        "CACHE_TIME" => Array("DEFAULT"=>3600),
    ),
);
