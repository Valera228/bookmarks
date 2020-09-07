<?

use Bitrix\Main\Web\Uri,
    \Bitrix\Main\HttpRequest;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

class Pagenavigation extends CBitrixComponent
{
    /**
     * Определение параметров компонента
     * @param $arParams
     * @return array
     */
	public function onPrepareComponentParams($arParams)
	{
        $arParams["PAGE_WINDOW"] = ((int)$arParams["PAGE_WINDOW"] > 0? (int)$arParams["PAGE_WINDOW"] : 10);
		return $arParams;
	}

    /**
     * Инициальзация компонента
     * @return mixed|void
     */
	public function executeComponent()
	{
		if (!is_object($this->arParams["NAV_OBJECT"]) || !($this->arParams["NAV_OBJECT"] instanceof \Bitrix\Main\UI\PageNavigation))
		{
			return;
		}
		/** @var \Bitrix\Main\UI\PageNavigation $nav */
		$nav = $this->arParams["NAV_OBJECT"];

		$this->arResult["RECORD_COUNT"] = $nav->getRecordCount();
		$this->arResult["PAGE_COUNT"] = $nav->getPageCount();
		$this->arResult["CURRENT_PAGE"] = $nav->getCurrentPage();
		$this->arResult["ALL_RECORDS"] = $nav->allRecordsShown();
		$this->arResult["PAGE_SIZE"] = $nav->getPageSize();
		$this->arResult["PAGE_SIZES"] = $nav->getPageSizes();
		$this->arResult["SHOW_ALL"] = $nav->allRecordsAllowed();
		$this->arResult["ID"] = $nav->getId();
		$this->arResult["REVERSED_PAGES"] = ($nav instanceof \Bitrix\Main\UI\ReversePageNavigation);

		$this->makeUrl();
		$this->calculatePages();
		$this->calculateRecords();

		$this->IncludeComponentTemplate();
	}

    /**
     * Создание урла
     */
	protected function makeUrl()
	{
		/** @var \Bitrix\Main\UI\PageNavigation $nav */
		$nav = $this->arParams["NAV_OBJECT"];

        $uri = new Uri($this->request->getRequestUri());
        $uri->deleteParams(HttpRequest::getSystemParameters());
        $nav->clearParams($uri, false);
		$this->arResult["URL"] = $uri->getUri();
		$this->arResult["URL_TEMPLATE"] = $nav->addParams(
		    $uri,
            false,
            "--page--",
            (count($this->arResult["PAGE_SIZES"]) > 1? "--size--" : null)
        )->getUri();
	}

    /**
     * Создание урла для станицы пагинации
     * @param $page
     * @param string $size
     * @return string|string[]
     */
	public function replaceUrlTemplate($page, $size = "")
	{
		return str_replace(array("--page--", "--size--"), array($page, $size), $this->arResult["URL_TEMPLATE"]);
	}

    /**
     * Расчет страниц
     */
	protected function calculatePages()
	{
        if ($this->arResult["CURRENT_PAGE"] > floor($this->arParams["PAGE_WINDOW"]/2) + 1 &&
            $this->arResult["PAGE_COUNT"] > $this->arParams["PAGE_WINDOW"])
        {
            $startPage = $this->arResult["CURRENT_PAGE"] - floor($this->arParams["PAGE_WINDOW"]/2);
        }
        else
        {
            $startPage = 1;
        }

        if ($this->arResult["CURRENT_PAGE"] <= $this->arResult["PAGE_COUNT"] - floor($this->arParams["PAGE_WINDOW"]/2) &&
            $startPage + $this->arParams["PAGE_WINDOW"]-1 <= $this->arResult["PAGE_COUNT"])
        {
            $endPage = $startPage + $this->arParams["PAGE_WINDOW"] - 1;
        }
        else
        {
            $endPage = $this->arResult["PAGE_COUNT"];
            if($endPage - $this->arParams["PAGE_WINDOW"] + 1 >= 1)
            {
                $startPage = $endPage - $this->arParams["PAGE_WINDOW"] + 1;
            }
        }

		$this->arResult["START_PAGE"] = $startPage;
		$this->arResult["END_PAGE"] = $endPage;
	}

    /**
     * Расчет записей
     */
	protected function calculateRecords()
	{
		/** @var \Bitrix\Main\UI\PageNavigation $nav */
		$nav = $this->arParams["NAV_OBJECT"];

		$this->arResult["FIRST_RECORD"] = $nav->getOffset() + 1;

        if ($this->arResult["CURRENT_PAGE"] == $this->arResult["PAGE_COUNT"]) {
            $this->arResult["LAST_RECORD"] = $this->arResult["RECORD_COUNT"];
        }
        else {
            $this->arResult["LAST_RECORD"] = $this->arResult["FIRST_RECORD"] + $nav->getLimit() - 1;
        }
	}
}
