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

<div class="container bookmark">
    <div class="row">
        <form name="add-bookmark" >
            <div class="form-row">
                <div class="col-md-12 mb-4">
                    <label for="url-bookmark">URL закладки</label>
                    <input type="url" class="form-control" id="url-bookmark" name="url">
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </form>
    </div>
</div>
