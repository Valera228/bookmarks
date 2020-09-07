<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$component = $this->getComponent();
?>

<?$APPLICATION->IncludeComponent(
	"valera:bookmark.list",
	"",
	Array(
		"PAGE_SIZE" => $arParams["PAGE_SIZE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"ADD_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["add"]
	),
	$component
);?>
