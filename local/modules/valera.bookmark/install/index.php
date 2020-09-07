<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

class valera_bookmark extends CModule
{
    var $MODULE_ID = "valera.bookmark";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "Закладки от Валерии Кузнецовой";
        $this->MODULE_DESCRIPTION = "Тестовое задание от компании Lenvendo";
    }

    /**
     * Запускается при установке модуля
     * Содержит методы, которые необходимо выполнить при установке модуля
     */
    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallFiles();
        try {
            $this->InstallDB();
        } catch (\Bitrix\Main\ArgumentException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        } catch (\Bitrix\Main\LoaderException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        } catch (\Bitrix\Main\SystemException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        }
    }

    /**
     * Запускается при удалении модуля
     * Содержит методы, которые необходимо выполнить при удалении модуля
     */
    function DoUninstall()
    {
        try {
            $this->UnInstallDB();
        } catch (\Bitrix\Main\ArgumentException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        } catch (\Bitrix\Main\Db\SqlQueryException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        } catch (\Bitrix\Main\LoaderException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        } catch (\Bitrix\Main\SystemException $e) {
            $GLOBALS["errors"] = $e->getMessage();
        }
        UnRegisterModule($this->MODULE_ID);
    }

    /**
     * Создание таблиц в БД
     *
     * @return bool|void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        if (!Application::getConnection(Valera\Bookmark\BookmarkTable::getConnectionName())
            ->isTableExists(Base::getInstance('Valera\Bookmark\BookmarkTable')->getDBTableName()))
        {
            Base::getInstance('Valera\Bookmark\BookmarkTable')->createDbTable();
        }
    }

    /**
     * Удаление таблиц из БД
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        Application::getConnection(Valera\Bookmark\BookmarkTable::getConnectionName())->queryExecute('drop table if exists ' . Base::getInstance('Valera\Bookmark\BookmarkTable')->getDBTableName());
    }

    /**
     * Установка файлов
     * @return bool|void
     */
    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/valera.bookmark/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/valera.bookmark/install/js", $_SERVER["DOCUMENT_ROOT"]."/local/js", true, true);
        return true;
    }
}