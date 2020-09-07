<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

CJSCore::Init(array('jquery2'));
$this->addExternalCss("https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css");
$this->addExternalJS("https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js");
?>

<div class="container">

    <div class="row">
        <div class="col-md-12 date-create">
            <?=$arResult["DATE_CREATE"]->format("d.m.Y")?>
        </div>
        <div class="col-md-12 mb-2">
            <?if($arResult["FAVICON"]):?>
                <img class="preview_picture pm-2" src="<?=$arResult["FAVICON"]?>" width="16"/>
            <?endif?>
            <a class="bookmark-url" href="<?=$arResult["URL"]?>" target="_blank">
                <?=$arResult["URL"]?>
            </a>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-12">
            <?=$arResult["META_KEYWORDS"]?>
        </div>
        <div class="col-md-12">
            <?=$arResult["META_DESCRIPTION"]?>
        </div>
    </div>

    <hr/>

    <?if (!empty($arParams["BACK_URL"])):?>
        <div class="row">
            <div class="col-md-12">
                <a href="<?=$arParams["BACK_URL"]?>">
                    Вернуться в список закладок
                </a>
            </div>
        </div>
    <?endif;?>
</div>