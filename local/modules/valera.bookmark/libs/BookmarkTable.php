<?php
namespace Valera\Bookmark;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

/**
 * Class ExceptionTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_TYPE text optional
 * <li> UF_VALUE text optional
 * </ul>
 *
 * @package Bitrix\Exception
 **/

class BookmarkTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'valera_bookmark';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => "ID"
                ]
            ),
            new TextField(
                'NAME',
                [
                    'required' => true,
                    'title' => "Заголовок страницы"
                ]
            ),
            new TextField(
                'URL',
                [
                    'required' => true,
                    'title' => "URL страницы",
                    'validation' => function() {
                        return array(
                            function ($value) {
                                $filterVar = filter_var($value, FILTER_VALIDATE_URL);
                                if ($filterVar) {
                                    return true;
                                } else {
                                    return 'Введите корректный URL';
                                }
                            }
                        );
                    },
                    'save_data_modification' => function () {
                        return array(
                            function ($value) {
                                return filter_var($value, FILTER_SANITIZE_URL);
                            }
                        );
                    },
                ]
            ),
            new DatetimeField(
                'DATE_CREATE',
                [
                    'default_value' => new DateTime(),
                    'title' => "Дата создания"
                ]
            ),
            new TextField(
                'FAVICON',
                [
                    'title' => "Favicon"
                ]
            ),
            new TextField(
                'META_KEYWORDS',
                [
                    'title' => "META Keywords"
                ]
            ),
            new TextField(
                'META_DESCRIPTION',
                [
                    'title' => "META Description"
                ]
            ),
        ];
    }

    /**
     * Проверика существовария закладки по ее URL
     * @param $url
     * @return bool
     */
    public static function existBookmarkByUrl($url)
    {
        $newsList = BookmarkTable::getList(
            [
                "select" => ['ID'],
                'filter' => ['URL' => $url]
            ]
        )->fetch();

        return ($newsList) ? true : false;
    }
}