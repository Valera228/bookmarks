!window.VALERA && (window.VALERA = {});

window.VALERA.Bookmark = function () {
    this.__init();
};

window.VALERA.Bookmark.prototype = {

    settings: {
        form: 'form[name="add-bookmark"]',
    },

    __init: function() {
        this.addValidator();
    },

    /**
     * Добавление валидатора формы
     */
    addValidator: function() {
        let self = this;
        $(this.settings.form).validate({
            errorClass: "invalid",
            onfocusout: function(element) {
                $(element).valid();
            },
            onclick: function(element) {
                $(element).valid();
            },
            rules: {
                "url": {
                    required: true,
                    url: true
                },
            },
            messages: {
                "url": {
                    required: "Поле URL закладки обязательно для заполнения",
                    url: "Введите корректный URL закладки"
                },
            },
            errorPlacement: function(error, element) {
                //место для вывода сообщения об ошибке
                error.insertAfter(element);
            },
            submitHandler : function(form)
            {
                // Вызывается при успешной вылидации формы
                self.saveBookmark(form);
            }
        });
    },

    /**
     * Сохранение закладки
     * @param form
     */
    saveBookmark: function (form) {
        let self = this,
            formData = $(form).serializeArray(),
            $addButton = $(form).find('button[type="submit"]');

        $addButton.attr('disabled', 'disabled');
        $addButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Загрузка...');

        BX.ajax.runComponentAction('valera:bookmark.add',
            'addBookmark',
            { // Вызывается без постфикса Action
                mode: 'class',
                data: {
                    post: {
                        ajax: 'Y',
                        url: formData[0].value
                    }
                }, // ключи объекта data соответствуют параметрам метода
            }).then(function(response) {
                // Успешное завершение скрипта
                console.log('response', response);

                if (response.status === 'success') {
                    let data = response.data;
                    console.log('data', data);

                    if (data.status === "success") {
                        location.href = data.url;
                    } else {
                        $addButton.removeAttr('disabled');
                        $addButton.html('Добавить');
                        self.setDangerAlert($(form), data.message);
                    }

                }
        }).catch(function(response) {
            // Обработка ошибок
            console.log('response catch', response);
            $addButton.removeAttr('disabled');
            $addButton.html('Добавить');
            self.setDangerAlert($(form), response.errors[0].message);
        });
    },

    /**
     * Вывод сообщения об ошибке
     * @param $container
     * @param text
     */
    setDangerAlert: function ($container, text) {
        let $dangerAlert = $container.find('#alert-danger-popup');
        if ($dangerAlert.length) {
            $dangerAlert.html(text);
        } else {
            let errorAlert = '<div class="form-row">' +
                    '<div class="col-md-12">' +
                        '<div class="alert alert-danger" id="alert-danger-popup" role="alert">' +
                            text +
                        '</div>' +
                    '</div>' +
                '</div>';
            $container.prepend(errorAlert);
        }
    }
};


BX.ready(function()
{
    $(document).ready(function() {
        BX.loadScript(
            ['/local/js/jquery.validate.min.js'],
            function () {
                new VALERA.Bookmark();
            }
        );
    });

});