<?php

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Application,
    Valera\Bookmark\BookmarkTable;
use Bitrix\Main\UI\PageNavigation;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class BookmarkDetail
 */
class BookmarkDetail extends \CBitrixComponent
{
	/** @var  Main\ErrorCollection $errorCollection*/
	protected $errorCollection;

    /**
     * Определение параметров компонента
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

        if (empty($arParams["CACHE_ID"]))
        {
            $arParams["CACHE_ID"] = "bookmark-detail-";
        }

        if (empty($arParams["CACHE_TIME"]))
        {
            $arParams["CACHE_TIME"] = 3600;
        }

		return $arParams;
	}

    /**
     * Инициальзация компонента
     * @return bool|void
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function executeComponent()
    {
        if (!$this->checkModules() || empty($this->arParams["ID"]) || $this->arParams["ID"] == 0)
        {
            return false;
        }

        $this->getBookmark();
        $this->setPageProperty();
        $this->includeComponentTemplate(); // подключение шаблона компонента
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

    /**
     * Выборка списка закладок
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
	private function getBookmark()
    {
        $cache = Application::getInstance()->getManagedCache();
        $cacheId = $this->arParams["CACHE_ID"] . $this->arParams["ID"];

        // проверяем есть ли данные в кеше
        if ($cache->read($this->arParams["CACHE_TIME"], $cacheId))
        {
            // достаем данные из кеша
            $vars = $cache->get($cacheId);
            $this->arResult = $vars["ELEMENT"];
        } else {
            $newsList = BookmarkTable::getList(
                [
                    "select" => ['*'],
                    'filter' => ['=ID' => $this->arParams["ID"]]
                ]
            )->fetch();

            if ($newsList) {
                $this->arResult = $newsList;
                // записываем данные в кеш
                $cache->set(
                    $cacheId,
                    [
                        "ELEMENT" => $this->arResult
                    ]
                );
            } else {
                $this->arResult = [];
            }
        }
    }

    private function setPageProperty()
    {
        global $APPLICATION;

        if (!empty($this->arResult["NAME"])) {
            $APPLICATION->SetTitle($this->arResult["NAME"]);
        }

        if (!empty($this->arResult["META_KEYWORDS"])) {
            $APPLICATION->SetPageProperty("keywords", $this->arResult["META_KEYWORDS"]);
        }

        if (!empty($this->arResult["META_DESCRIPTION"])) {
            $APPLICATION->SetPageProperty("description", $this->arResult["META_DESCRIPTION"]);
        }
    }
}