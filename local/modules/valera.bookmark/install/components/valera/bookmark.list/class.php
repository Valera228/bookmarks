<?php

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Application,
    Valera\Bookmark\BookmarkTable;
use Bitrix\Main\UI\PageNavigation;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class BookmarkList
 */
class BookmarkList extends \CBitrixComponent
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
     * Инициальзация компонента
     * @return bool|void
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function executeComponent()
    {
        if (!$this->checkModules())
        {
            return false;
        }

        $this->getBookmarks();
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
	private function getBookmarks()
    {
        $pageSize = $this->arParams["PAGE_SIZE"];
        $nav = new PageNavigation($this->arParams["PAGE_NAV_ID"]);
        $nav->allowAllRecords(false)
            ->setPageSize($pageSize)
            ->initFromUri();
        $currentPage = $nav->getCurrentPage();
        $cache = Application::getInstance()->getManagedCache();
        $cacheId = $this->arParams["CACHE_ID"] . $currentPage;

        // проверяем есть ли данные в кеше
        if ($cache->read($this->arParams["CACHE_TIME"], $cacheId))
        {
            // достаем данные из кеша
            $vars = $cache->get($cacheId);
            $this->arResult["NAV"] = $vars["NAV"];
            $this->arResult["ITEMS"] = $vars["ITEMS"];
        } else {
            $newsList = BookmarkTable::getList(
                [
                    "select" => ['ID', 'NAME', 'URL', 'DATE_CREATE', 'FAVICON'],
                    'count_total' => true,
                    "offset" => $nav->getOffset(),
                    "limit" => $nav->getLimit(),
                ]
            );

            $nav->setRecordCount($newsList->getCount());
            $this->arResult["NAV"] = $nav;

            while($news = $newsList->fetch())
            {
                $news["DETAIL_PAGE_URL"] = CComponentEngine::makePathFromTemplate(
                    "/bookmarks/#ELEMENT_ID#/",
                    ["ELEMENT_ID" => $news["ID"]]
                );
                $this->arResult["ITEMS"][] = $news;
            }

            // записываем данные в кеш
            $cache->set(
                $cacheId,
                [
                    "ITEMS" => $this->arResult["ITEMS"],
                    "NAV" => $this->arResult["NAV"]
                ]
            );
        }
    }
}