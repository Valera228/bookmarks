<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

/** @var PageNavigationComponent $component */
$component = $this->getComponent();

$this->setFrameMode(true);

CJSCore::Init(array('jquery2'));
$this->addExternalCss("https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css");
$this->addExternalJS("https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js");
?>

<nav>

    <ul class="pagination justify-content-center">
        <?if ($arResult["PAGE_COUNT"] > 1):?>
            <?if ($arResult["CURRENT_PAGE"] > 1):?>
                <li class="page-item">
                    <a href="<?=htmlspecialcharsbx($arResult["URL"])?>" class="page-link">
                        1
                    </a>
                </li>
            <?else:?>
                <li class="page-item active" aria-current="page">
                    <span class="page-link">
                        1
                    </span>
                </li>
            <?endif?>
        <?endif;?>

        <?
        $page = $arResult["START_PAGE"] + 1;
        while($page <= $arResult["END_PAGE"]-1):
        ?>
            <?if ($page == $arResult["CURRENT_PAGE"]):?>
                <li class="page-item active" aria-current="page">
                    <span class="page-link">
                        <?=$page?>
                    </span>
                </li>
            <?else:?>
                <li class="page-item">
                    <a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($page))?>" class="page-link">
                        <?=$page?>
                    </a>
                </li>
            <?endif?>
            <?$page++?>
        <?endwhile?>

        <?if($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]):?>
            <?if($arResult["PAGE_COUNT"] > 1):?>
                <li class="page-item">
                    <a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["PAGE_COUNT"]))?>" class="page-link">
                        <span><?=$arResult["PAGE_COUNT"]?></span>
                    </a>
                </li>
            <?endif?>
        <?else:?>
            <?if($arResult["PAGE_COUNT"] > 1):?>
                <li class="page-item active" aria-current="page">
                    <span class="page-link">
                        <?=$arResult["PAGE_COUNT"]?>
                    </span>
                </li>
            <?endif?>
        <?endif?>
    </ul>
</nav>
