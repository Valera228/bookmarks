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
$component = $this->getComponent();
?>
<div class="content mt-5">

    <div class="row mb-5">
        <div class="col-md-12">
            <a href="<?=$arParams["ADD_URL"]?>" class="btn btn-primary text-white">
                Добавить закладку
            </a>
        </div>
    </div>

    <?foreach($arResult["ITEMS"] as $arItem):?>
        <div class="row mb-5">
            <div class="col-md-10">
                <h4 class="bookmark-title">
                    <a class="bookmark-title" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                        <?=$arItem["NAME"]?>
                    </a>
                </h4>
            </div>
            <div class="col-md-2 date-create">
                <?=$arItem["DATE_CREATE"]->format("d.m.Y")?>
            </div>
            <div class="col-md-12">
                <?if(!empty($arItem["FAVICON"])):?>
                    <img class="preview_picture pm-2" src="<?=$arItem["FAVICON"]?>" width="16"/>
                <?endif?>
                <a class="bookmark-url" href="<?=$arItem["URL"]?>" target="_blank">
                    <?=$arItem["URL"]?>
                </a>
            </div>
        </div>
    <?endforeach;?>

    <?$APPLICATION->IncludeComponent(
        "valera:pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $arResult["NAV"]
        ),
        $component
    );?>

</div>