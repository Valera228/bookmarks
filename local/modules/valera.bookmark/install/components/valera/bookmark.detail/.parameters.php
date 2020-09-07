<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("valera.bookmark"))
    return;

$arComponentParameters = array(
    "PARAMETERS" => array(
        "ID" => Array(
            "PARENT" => "BASE",
            "NAME" => "ID закладки",
            "TYPE" => "INTEGER"
        ),
        "CACHE_TIME" => Array("DEFAULT"=>3600),
    ),
);
