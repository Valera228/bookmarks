<?php

use Bitrix\Main,
	Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class Bookmark
 */
class Bookmark extends \CBitrixComponent
{
	/** @var  Main\ErrorCollection $errorCollection*/
	protected $errorCollection;

    /**
     * Проверка и проставление параметров компонента
     * @param $arParams
     * @return mixed[] Checked and valid parameters
     * @throws Main\LoaderException
     */
	public function onPrepareComponentParams($arParams)
	{
        $this->errorCollection = new Main\ErrorCollection();

		if (!$this->checkModules())
		{
			return $arParams;
		}

		if (empty($arParams["FOLDER"]))
		{
            $arParams["FOLDER"] = "/bookmarks/";
        }

        if (empty($arParams["PAGE_NAV_ID"]))
        {
            $arParams["PAGE_NAV_ID"] = "nav-bookmark";
        }

        if (empty($arParams["PAGE_SIZE"]))
        {
            $arParams["PAGE_SIZE"] = 3;
        }

        if (empty($arParams["CACHE_ID"]))
        {
            $arParams["CACHE_ID"] = "bookmark-list-cache-page";
        }

        if (empty($arParams["CACHE_TIME"]))
        {
            $arParams["CACHE_TIME"] = 3600;
        }

		return $arParams;
	}

    /**
     * Инициализация компонента
     * @return mixed|void
     */
    public function executeComponent()
    {
        $arVariables = [];
        $sefFolder = $this->arParams["FOLDER"];
        $arUrlTemplates = [
            "list" => "",
            "detail" => "#ELEMENT_ID#/",
            "add" => "add-bookmark/"
        ];

        $engine = new CComponentEngine($this);
        $componentPage = $engine->guessComponentPath($sefFolder, $arUrlTemplates, $arVariables);

        if (empty($componentPage)) {
            $componentPage = 'list';
        }

        $this->arResult = [
            "FOLDER" => $sefFolder,
            "URL_TEMPLATES" => $arUrlTemplates,
            "VARIABLES" => $arVariables
        ];

        $this->includeComponentTemplate($componentPage);
    }

    /**
     * Проверка подключения обязательных модулей
     * @return bool
     * @throws Main\LoaderException
     */
	protected function checkModules()
	{
		if (!Loader::includeModule('valera.bookmark'))
		{
			$this->errorCollection->setError(new Main\Error("Модуль Закладки не установлен"));
			return false;
		}

		return true;
	}
}