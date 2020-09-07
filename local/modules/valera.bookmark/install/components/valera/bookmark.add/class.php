<?php

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main,
	Bitrix\Main\Loader,
    Bitrix\Main\Application,
    Bitrix\Main\Engine\ActionFilter,
    Valera\Bookmark\BookmarkTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class BookmarkAdd
 */
class BookmarkAdd extends \CBitrixComponent implements Controllerable
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

        if (empty($arParams["FOLDER"]))
        {
            $arParams["FOLDER"] = "/bookmarks/";
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
     * Установка свойств страницы
     */
    private function setPageProperty()
    {
        global $APPLICATION;

        $APPLICATION->SetTitle("Добавление закладки");
    }

    /**
     * Добавление закладки
     * @param $post - данные ajax запроса
     * @return array
     * @throws Exception
     */
    public function addBookmarkAction($post)
    {
        if ($post['ajax'] == "Y" && !empty($post['url']))
        {
            $filterVar = filter_var($post['url'], FILTER_VALIDATE_URL, ['FILTER_FLAG_HOST_REQUIRED']);
            if ($filterVar) {
                $url = filter_var($filterVar, FILTER_SANITIZE_URL);
                $arUrl = $this->getFindUrl($url);
                $findUrl = $arUrl['url'];

                // Если URL корректный и его нет в базе, то собираем данные со страницы
                if (!empty($findUrl) && !BookmarkTable::existBookmarkByUrl($findUrl))
                {
                    $arContent = $this->getPageContent($url); // Получение страницы

                    if ($arContent["status"] == "success")
                    {
                        // Получение данных со страницы
                        $arFields = $this->getBookmarkData($arContent["html"], $arUrl['host']);
                        if (!empty($arFields))
                        {
                            // Добавление закладки
                            $bookmark = new BookmarkTable();
                            $arFields["URL"] = $findUrl;
                            $id = $bookmark->add($arFields);
                            if ($id->getId() > 0) {
                                $cache = Application::getInstance()->getManagedCache();
                                $cache->cleanAll();
                                $result = [
                                    'status' => 'success',
                                    'url' => $this->arParams["FOLDER"] . $id->getId() . '/'
                                ];
                            } else {
                                throw new Exception($id->getErrors()[0]->getMessage());
                            }
                        } else {
                            throw new Exception("Нет данных для создания закладки");
                        }
                    } else {
                        throw new Exception($arContent["message"]);
                    }
                } else {
                    throw new Exception('Закладка с таким URL уже существует');
                }
            } else {
                throw new Exception('Вы ввели не корректный URL');
            }
        } else {
            throw new Exception('Неверные параметры запроса');
        }

        return $result;
    }

    /**
     * Конфигурация ajax запросов
     * @inheritDoc
     */
    public function configureActions()
    {
        // Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
        // Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
        return [
            'sendMessage' => [ // Ajax-метод
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ])
                ],
            ],
        ];
    }

    /**
     * Преобразование URL для последующего поиска в БД
     * @param $url
     * @return array
     */
    private function getFindUrl($url)
    {
        $arUrlData = parse_url($url);
        $findUrl = '';

        if (!empty($arUrlData))
        {
            if (empty($arUrlData["path"])) {
                $arUrlData["path"] = '/';
            } elseif (substr($arUrlData["path"], -1) != '/') {
                $arUrlData["path"] .= '/';
            }

            $findUrl = $arUrlData["scheme"] . '://' . $arUrlData["host"] . $arUrlData["path"];

            if (!empty($arUrlData["query"])) {
                $findUrl .= '?' . $arUrlData["query"];
            }
        }

        return [
            'url' => $findUrl,
            'host' => $arUrlData["scheme"] . '://' . $arUrlData["host"]
        ];
    }

    /**
     * Получение данных страницы по URL
     * @param $url
     * @return array
     */
    private function getPageContent($url)
    {
        $curl = curl_init();
        $optarray = array(
            CURLOPT_VERBOSE => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        );

        curl_setopt_array($curl, $optarray);
        $curlResult = curl_exec($curl);

        if(curl_exec($curl) === false)
        {
            $result = [
                'status' => 'error',
                'message' => curl_error($curl)
            ];
        } else {
            $headers = curl_getinfo($curl);
            if ($headers["http_code"] < 400) {
                $result = [
                    'status' => 'success',
                    'html' => $curlResult,
                    'header' => curl_getinfo($curl)
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => "Ошибка доступа к странице закладки. Ошибка: " . $headers["http_code"]
                ];
            }
        }

        curl_close($curl);

        return $result;
    }

    /**
     * Разбор html страницы, для получения данных закладки
     * @param $html
     * @param $url
     * @return array
     */
    private function getBookmarkData($html, $url)
    {
        $arFields = [];
        $dom = new DOMDocument;
        $dom->loadHTML($html);

        // Получение ключевых слов и описания из meta-тегов
        $meta = $dom->getElementsByTagName('meta');
        foreach ($meta as $metaEl) {
            $name = $metaEl->getAttribute('name');
            if ($name == 'keywords') {
                $arFields["META_KEYWORDS"] = $metaEl->getAttribute('content');
            } elseif ($name == 'description') {
                $arFields["META_DESCRIPTION"] = $metaEl->getAttribute('content');
            }

            if (!empty($arFields["META_KEYWORDS"]) && !empty($arFields["META_DESCRIPTION"])) {
                break;
            }
        }

        // Получение favicon
        $links = $dom->getElementsByTagName('link');
        foreach ($links as $link) {
            $rel = $link->getAttribute('rel');
            if ($rel == 'shortcut icon') {
                $arFields["FAVICON"] = $url . $link->getAttribute('href');
                break;
            }
        }

        // Получение заголовока страницы
        $titles = $dom->getElementsByTagName('title');
        foreach ($titles as $title) {
            $arFields["NAME"] = $title->nodeValue;
            break;
        }

        return $arFields;
    }
}