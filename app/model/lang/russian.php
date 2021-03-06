<?php 
    /*
     * Russian localization
     */
     
    $_SESSION[$program]['lang'] = array(
        'access_denied'=>'Доступ запрещен',
        'add_new_ip'=>'Добавить новый IP-адрес',
        'and_press_esc_or_enter'=>'и нажмите на ESC или ENTER',
        'application_files_corrupted'=>'Файлы приложения повреждены. Сообщите администратору.',
        'application_files_restored'=>'Файлы приложения восстановлены',
        'attention'=>'Внимание',
        'auth_change_password'=>'Необходимо сменить пароль.',
        'auth_user_ip_is_not_allowed'=>'Ваш IP-адрес запрещен для данного пользователя',
        'auth_user_is_blocked'=>'Пользователь заблокирован. Обратитесь к администратору по безопасности',
        'auth_user_is_deleted'=>'Пользователь удален.',
        'back'=>'Назад',
        'back_to_list'=>'Назад к списку',
        'back_to_reports'=>'Назад к созданию отчетов',
        'back_to_scen_edit'=>'Назад к редактированию сценария',
        'back_to_user_edit'=>'Назад к свойствам пользователя',
        'back_to_user_ip_list'=>'Назад к списку IP пользователя',
        'back_to_workflow'=>'Назад на рабочий экран',
        'backup_admin_table_header'=>'Дата архива|Создатель',
        'backup_in_progress'=>'Идёт процесс архивации',
        'backup_list'=>'Список предыдущих архивов',
        'backup_tool'=>'Резервное копирование',
        'backup_was_done'=>'Архив был успешно создан',
        'blind_recon'=>'Слепая сверка',
        'call_supervisor'=>'Обратитесь к контролеру.',
        'call_to_admin_for_new_index'=>'Обратитесь к администратору, если необходимо добавить новое имя индекса.',
        'cancel'=>'Отмена',
        'cannot_remove_denom'=>'По номиналу определены классы ценностей. Невозможно убрать из списка',
        'cannot_remove_grade'=>'Класс используется в определении ценностей. Невозможно убрать из списка',
        'cannot_remove_valuable_type'=>'Этот тип денежных знаков используется. Невозможно убрать из списка',
        'cant_edit_correct_indexes_by_card'=>'Невозможно изменить корректные индексы в сверке по карте №',
        'cant_open_recon_by_card'=>'Невозможно открыть сверку по карте №',
        'change_password'=>'Смена пароля',
        'change_password_labels'=>'Cтарый пароль|Новый пароль|Повтор нового пароля|Сохранить новый пароль',
        'change_password_results'=>'Пароль успешно изменен|Ошибка данных. Пароль не был изменен',
        'change_scenario'=>'Сменить сценарий',
        'clear_selection'=>'Отменить выбор',
        'client_delete_labels'=>'был удален|Удалить клиента|Невозможно редактировать удаленного клиента',
        'confirm_backup'=>'Подтвердите создание архива',
        'confirm_client_restoration'=>'Подтвердите восстановление клиента',
        'continue'=>'Продолжить',
        'create_preview_reports_confirm_report_list'=>'Будут отображены следующие отчётоы',
        'create_preview_reports_confirmation_header'=>'Подтвердите отображение отчетов за период с|по',
        'create_reports_confirm_buttons'=>'Отменить|Подтвердить создание',
        'create_reports_confirm_report_list'=>'Будут сформированы следующие отчёты',
        'create_reports_confirmation_header'=>'Подтвердите формирование отчетов за период с|по',
        'currency_add_title'=>'Добавление новой валюты',
        'currency_edit_labels'=>'Обозначение|Код|Год выпуска или модификации|Название|Символ|Длина серии|Длина номера',
        'currency_edit_title'=>'Редактирование валюты',
        'customer_add_alerts'=>'Код (БИК) неправильной длины!|Укажите длину кода от 1 до 99|Поля помеченне звездочкой обязательны к заполнению',
        'customer_add_buttons'=>'Добавить|Отменить|Подтвердить добавление|Назад к списку клиентов',
        'customer_add_labels'=>'Создание клиента|Подтвердите создание клиента|Название организации|Код (БИК)|длина кода|поля обязательны для заполнения|Код КП|Код ОКАТО|Адрес|Адрес эл.почты|Телефон|Факс|Контакт:|Фамилия Имя Отчество|Должность',
        'customer_add_messages'=>'Был добавлен новый клиент|Клиент с таким кодом уже существует, добавить невозможно.',
        'customer_edit_buttons'=>'Сохранить и выйти|Отменить изменения и выйти|Удалить клиента|Заменить название',
        'customer_edit_labels'=>'Редактирование клиента|Название организации|Код (БИК)|Код КП|Код ОКАТО|Адрес|Адрес эл.почты|Телефон|Факс|Контакт:|Должность|Старое название|Новое название|Замена имени клиента',
        'customers_buttons'=>'Найти|Показать все|Назад на рабочий экран|Добавить клиента',
        'customers_labels'=>'Клиенты|Список клиентов|Поиск по имени|или по коду',
        'customers_table_header'=>'Наименование|Код (БИК)|Код КП|Контакт: Должность|ФИО|Телефон|Тел/Факс|Удален',
        'damaged_pack'=>'повреждённая',
        'deffered_reconciliations'=>'В работе',
        'deffered_reconciliations_header'=>'Номер карты|Касса|Оператор сверки|Сверка создана|Последнее изменение',
        'denom'=>'Номинал',
        'denom_add_title'=>'Добавление нового номинала',
        'denom_edit_labels'=>'Номинал|Валюта|Ярлык',
        'denom_edit_title'=>'Редактирование номинала',
        'denoms_buttons'=>'Добавить|Номинал|Валюту|Тип денежных знаков|Класс',
        'denoms_currencies_header'=>'Название|Обозначение|Символ|Код|Год',
        'denoms_currencies_title'=>'Валюты',
        'denoms_denoms_header'=>'Валюта|Номинал|Ярлык',
        'denoms_denoms_title'=>'Номиналы',
        'denoms_grades_header'=>'Название|Ярлык',
        'denoms_grades_title'=>'Классы',
        'denoms_title'=>'Номиналы, валюты, классы, типы',
        'denoms_valuable_types_title'=>'Тип денежных знаков',
        'denoms_valuables_header'=>'Название|Ярлык',
        'deposit_card_number'=>'Номер карты',
        'deposit_index_edit_title'=>'Изменение/добавление индекса системы',
        'deposit_manager_actions'=>'Отменить|Изменить номер|Объединить депозиты|Перевести в сервис|Освободить пересчеты',
        'deposit_manager_buttons'=>'Создать сервисный депозит|Объединить депозиты|Изменить номер карты|Освободить пересчеты',
        'deposit_manager_edit_number_messages'=>'Номер разделительной карты депозита был изменен|Номер разделительной карты депозита №|будет изменен|Отменить будет невозможно|Введите новый номер|Номера пересчетов №|будут изменены|Номер в пересчетах был изменен на',
        'deposit_manager_join_messages'=>'Депозиты были объединены|Депозиты №|будут объеденены|Отменить операцию будет невозможно|Выберите номер карты',
        'deposit_manager_recs_header'=>'Номер карты|Касса пересчёта|Оператор сверки|Создана|Последнее изменение',
        'deposit_manager_recs_title'=>'Депозиты',
        'deposit_manager_release_messages'=>'Сверка преобразована в сервисную|Пересчеты освобождены|Пересчеты по депозиту №|будут освобождены|Отменить будет невозможно',
        'deposit_manager_runs_header'=>'Номер карты|Касса пересчёта|Имя машины|Индекс|Начало обработки|Завершение обработки',
        'deposit_manager_runs_title'=>'Пересчёты',
        'deposit_manager_service_messages'=>'Сверка преобразована в сервисную|Депозит №| будет переведен в сервисную категорию|Отменить будет невозможно|Пересчеты №|будут собраны в сервисный депозит|Отменить будет невозможно',
        'deposit_was_reconciled_by_another_user'=>'Депозит был сверен другим пользователем',
        'deposit_by_card'=>'Депозит по карте №',
        'was_reconciled'=>' уже сверен',
        'discrepancy_form_labels'=>'Код клиента (БИК)|Клиент|Дата упаковки|сформированной кассиром|Упаковка|Оттиск|Бандероль',
        'discrepancy_states'=>'пачка|мешок|пломба|клише|поперечная|полной длины|целая|поврежденная|кассета',
        'discrepancy_tables'=>'Излишки|Недостача|Сомнительные|Комментарий',
        'discrepancy_tables_headers'=>'Валюта|Номинал|Кол-во',
        'discrepancy_value_tables_headers'=>'Валюта|Номинал|Сумма',
        'discrepancy_variants'=>'Без расхождений|С расхождениями|Сервисный',
        'edit_indexes'=>'Редактировать индексы',
        'edit_user_ip_list'=>'Редактировать список IP-адресов',
        'error'=>'Ошибка',
        'fields_are_required'=>'Поля, помеченные звездочкой, обязательны к заполнению',
        'files_were_copied_successfully'=>'Файлы XML отчетов были удачно скопированы',
        'fill_required_fields'=>'Необходимо заполнить обязательные поля',
        'finish'=>'Завершить',
        'for_select_more_than_one_signers'=>'для выбора более чем одного подписанта удерживайте кнопку CTRL',
        'go_to'=>'Переход',
        'good_pack'=>'целая',
        'goto_scens_button'=>'Перейти к сценариям',
        'grade_add_title'=>'Добавление нового класса',
        'grade_edit_labels'=>'Название|Ярлык',
        'grade_edit_title'=>'Редактирование класса',
        'index_correction'=>'Корректировка индексов',
        'index_was_changed'=>'Индексы были успешно изменены.',
        'indexes_add_deposit_index'=>'Добавить новый индекс',
        'indexes_add_sorter_index'=>'Добавить новое имя',
        'indexes_deposit_index'=>'Индексы системы',
        'indexes_deposit_index_headers'=>'Значение|Описание',
        'indexes_sorter_index'=>'Индексы машин',
        'indexes_sorter_index_headers'=>'Имя|Значение',
        'indexes_title'=>'Конфигурирование индексов',
        'invert_selection'=>'Инвертировать выбор',
        'ip_was_added'=>'Добавлен IP адрес со значением %',
        'login'=>'Вход',
        'logout'=>'Выход',
        'menu_admin'=>'Машины|Номиналы|Ценности|Индексы|Клиенты|Пользователи|Сценарии|Система|Мой профиль|Архивирование|Обновление',
        'menu_operator'=>'Рабочий экран|Сверенное|Кассиры|Мой профиль',
        'menu_security'=>'Ситемный журнал|Пользователи|Мой профиль',
        'menu_supervisor'=>'Рабочий экран|Сверенное|Корректировки|Отчеты|Клиенты|Пользователи|Подписанты|Мой профиль',
        'more_than_one_index'=>'Более одного индекса в данных пересчета.',
        'new_category_header'=>'Название категории|Кол-во|Класс',
        'new_report_was_created_by_another_user'=>'Отчет был создан другим пользователем',
        'new_valuables_in_deposit'=>'В депозите обнаружена неизвестная категория. Вызовите администратора.',
        'next_step'=>'Следующий шаг',
        'no'=>'Нет',
        'no_current_scenario'=>'Не установлен рабочий сценарий, обратитесь к контроллеру',
        'no_data_for_reports'=>'Нет данных пересчета по сверенным депозитам для отчетов.',
        'no_data_for_view'=>'Нет данных для просмотра',
        'no_reports_for_print'=>'Нет отчетов для печати в этом наборе отчетов',
        'one_or_more_signers_needed_in_report'=>'Необходимо выбрать хотя бы одного подписанта',
        'operator_has_unfinished_recon'=>'У данного пользователя имеются в работе депозиты с другим сценарием',
        'operator_workflow'=>'Рабочий экран оператора',
        'pass'=>'Пароль',
        'previous_step'=>'Предыдущий шаг',
        'profile_labels'=>'Фамилия|Имя|Отчество|Должность|Логин|Язык интерфейса|Пароль действителен до|Сменить пароль|Телефон',
        'profile_title'=>'Личные данные пользователя',
        'recon_by_card_number'=>'Сверка по карте №',
        'recon_for_check'=>'Для подтверждения расхождений',
        'recon_input_data'=>'Данные ручного ввода',
        'recon_report_act'=>'Печать акта сверки',
        'recon_report_buttons'=>'Закрыть отчет|Печать Акта',
        'recon_report_header'=>'Отчет по сверке',
        'reconcile'=>'Сверить',
        'reconcile_with_discrep'=>'Сверить с расхождениями',
        'reconciled_deposits'=>'Сверенные депозиты',
        'reconciled_deposits_header'=>'Номер карты|Касса|Имя машины|Индекс|Оператор сверки|Сверен|Расхождения',
        'reconciliation'=>'Сверка',
        'record_edit_buttons'=>'Отмена|Сохранить|Клонировать|Удалить|Подтвердить сохранение|Подтвердить клонирование|Подтвердить удаление|Назад к списку|Редактировать новую запись|Добавить новую запись|Подтвердить добавление',
        'record_edit_headers'=>'Подтвреждение клонирования|Подтверждение удаления|Подтверждение обновления|Подтверждение добавления',
        'record_edit_warning'=>'Запись изменил другой пользователь|Запись была добавлена|Нельзя клонировать. Неверные данные|Запись была удалена|Нельзя удалить. Неверные данные|Запись была обновлена|Операция запрещена|Запись содержит неуникальные поля|Запись изменена|Выйти без сохранения',
        'refresh_button'=>'Обновить страницу',
        'reports_archive'=>'Архив отчетов',
        'reports_archive_search_label'=>'Введите дату или часть даты|Поиск|Показать все',
        'reports_archive_table_header'=>'Время создания|Работник|Должность|Касса пересчета',
        'reports_buttons'=>'Архив отчетов|Закрыть смену|Предварительный просмотр отчётов',
        'reports_generate_buttons'=>'Назад к странице отчетов|Печать отчетов',
        'reports_header'=>'Статистика за период с|по',
        'reports_table_headers'=>'Валюта|Номинал|Количество|Сумма|Итого',
        'reports_were_created'=>'Отчеты за данный период уже в архиве',
        'reportset_view'=>'Подробности набора отчетов',
        'reportset_view_buttons'=>'Назад к архиву отчетов|Печать набора отчетов|Перевыслать XML отчеты',
        'restore_application_files'=>'Восстановить файлы приложения из архива',
        'restore_client'=>'Восстановить клиента',
        'scan_barcode_or_enter_deposit_card_number'=>'Сканируйте или введите вручную номер разделительной карты',
        'scen_add_title'=>'Добавить сценарий',
        'scen_edit_labels'=>'Название сценария|Сценарий по умолчанию|Однономинальные депозиты|Сверять только на сумму|Заявленное количество по умолчанию|С шагом подготовки|Обязательный ввод реквизитов|Проверять индексы|Вводить серийные номера сомнительных банкнот|Вводить Ф.И.О. кассира, сформировавшего депозит|Вводить дату упаковки|Вводить тип упаковки|Вводить идентификатор упаковки|Вводить целостность упаковки|Вводить тип оттиска|Вводить номер оттиска|Вводить целостность оттиска|Вводить тип банднроли|Вводить целостность банднроли|Логически удалён',
        'scen_edit_report_button'=>'Выбор отчетов',
        'scen_edit_title'=>'Редактирование сценария',
        'scen_edit_wizard_button'=>'Запустить пошаговый конфигуратор сценария',
        'scen_need_run_wizard'=>'Необходимо переопределить классы ценностей по этому сценарию',
        'scen_reports_header'=>'Таблица доступных отчетов по сценарию',
        'scen_reports_table_headers'=>'Описание|Комментарий|Используется',
        'scen_wizard_available'=>'Доступные',
        'scen_wizard_headers'=>'Выберите типы ценностей для сценария|Типы ценностей выбраны|Выберите номиналы для сценария|Номиналы выбраны|Выберите классы машинной обработки для сценария|Классы машинной обработки выбраны|Выберите классы ручного ввода|Классы ручного ввода выбраны|Назначте классы ценностей|Сценарий сконфигурирован',
        'scen_wizard_in_use'=>'Используемые',
        'scen_wizard_title'=>'Волшебник изумрудного города',
        'scenario_was_changed_to_current'=>'Сценарий сверяемого депозита был изменён на текущий',
        'scenario_was_changed_to_recon'=>'Рабочий сценарий был изменен на сценарий депозита в работе',
        'scenario_was_configured'=>'Сценарий скофигурирован',
        'scens_add_new_button'=>'Добавить новый сценарий',
        'scens_need_checked'=>'Необходимо проверить выделенные сценарии',
        'scens_table_headers'=>'Название сценария|По умолчанию|Удалён',
        'scens_table_title'=>'Список сценариев',
        'scens_title'=>'Сценарии',
        'select_all'=>'Выбрать все записи',
        'select_index_from_list'=>'Выберите имя индекса из списка',
        'select_one_valuable_type'=>'Нельзя продолжить не выбрав ни одного типа денежных знаков',
        'select_scenario'=>'Выберите сценарий из списка',
        'select_signers'=>'Выберите подписантов',
        'send_to_control'=>'Передать контролёру',
        'set_index'=>'Присвоить индекс',
        'signer_add_title'=>'Добавление подписанта',
        'signer_edit_title'=>'Редактирование подписанта',
        'signers_buttons'=>'Назад на рабочий экран|Добавить подписанта|Поиск|Показать всех',
        'signers_labels'=>'Подписанты|Список подписантов|Поиск по имени|по должности',
        'signers_table_header'=>'Ф.И.О (как в отчёте)|Должность|Телефон',
        'sorter_accounting_data'=>'Данные машинной обработки',
        'sorter_accounting_data_by_card'=>'Данные пересчета по карте №',
        'sorter_add_title'=>'Добавление новой машины',
        'sorter_buttons'=>'Добавить машину|Добавить тип машины',
        'sorter_edit_labels'=>'Название машины|Серийный номер|Тип машины|Вариант исполнения|Версия П.О.|Касса пересчёта|Машина логически удалена|Сетевой адрес|Маска сети|Порт|Логин машины|Пароль машины|Имя базы данных машины|Папка для сетевого подключения|Сетевое соединение установлено',
        'sorter_edit_title'=>'Редактирование свойств машины',
        'sorter_index_edit_title'=>'Изменение/добавление индекса машины',
        'sorter_list_add_sorter'=>'Добавить машину',
        'sorter_list_add_type'=>'Добавить тип машины',
        'sorter_list_title'=>'Список машин',
        'sorter_type_edit_labels'=>'Тип машины',
        'sorter_type_list_title'=>'Типы машин',
        'sorters_table_header'=>'Название|Серийный номер|Тип|Касса|Состояние|Удален',
        'sorters_table_states'=>'вкл|выкл',
        'start_backup'=>'Создать новый архив',
        'start_date_was_adjusted'=>'Стартовое время нового отчетного периода было откорретировано',
        'start_date_will_be_adjusted'=>'Стартовое время будет откорректировано. Будет создан пустой набор отчетов.',
        'success'=>'Выполнено успешно',
        'sum'=>'Сумма',
        'sum_and_total'=>'Сумма|Итого',
        'supervisor_is_not_allowed_to_start_recon'=>'Контролер не может создавать сверку',
        'supervisor_preview_reports'=>'Предварительный просмотр отчётов',
        'supervisor_reports'=>'Отчеты',
        'supervisor_workflow'=>'Рабочий экран контролёра',
        'suspect_buttons'=>'Назад к экрану сверки|Передать контролёру|Подтвердить',
        'suspect_header'=>'Серийные номера сомнительных банкнот',
        'suspect_table_headers'=>'Валюта|Номинал|Серия слева|Номер слева|Серия справа|Номер справа',
        'suspect_title'=>'Ввод серийных номеров',
        'system_edit_headers'=>'Название кассового центра|Город|Код (БИК) кассового центра|Код КП|Код OKATO|Название комплекса (АПК)|Язык системы по умолчанию|Разрешить сверку контролёру|Привязка пользователя к рабочему месту|Период автоархивирования (дней)|Неархивированные данные (дней)',
        'system_edit_title'=>'Параметры системы',
        'temporary_password'=>'Временный пароль',
        'total'=>'Всего',
        'total_table_header'=>'Номинал|Ожидалось|Результат|Расхождение|Сумма',
        'turn_javascript_on'=>'Пожалуйста, включите поддержку java-скриптов в вашем браузере!',
        'unreconciled_deposits'=>'Несверенные (последние 50)',
        'unreconciled_deposits_header'=>'Номер карты|Касса|Имя машины|Индекс|Начало обработки|Завершение обработки',
        'update_already_applied'=>'Такое обновление уже есть в системе',
        'update_file_invalid'=>'Файл обновлений неправильный',
        'update_finished'=>'Обновление успешно применено',
        'update_list'=>'История обновлений',
        'update_system'=>'Обновить систему',
        'update_table_header'=>'Дата|Название файла',
        'update_tool'=>'Обновление системы',
        'update_upload_wrong'=>'Загрузка обновления не удалась',
        'used_sorter_mode'=>'Использованый режим работы машины',
        'user'=>'Логин',
        'user_add_buttons'=>'Добавить|Отменить',
        'user_add_confirm_texts'=>'Подтвердите создание пользователя|ФИО|Подтвердить добавление|Отменить',
        'user_add_labels'=>'Создание работника|Фамилия|Имя|Отчество|Должность|Группа|Логин|Касса пересчета|Выберите из списка|поля обязательны для заполнения|Телефон|Родительный падеж|Творительный падеж',
        'user_add_title'=>'Создание пользователя',
        'user_add_warnings'=>'Пользователь с такими данными уже существует, добавить невозможно.|Добавлен пользователь|Поля помеченне звездочкой обязательны к заполнению',
        'user_delete_results'=>'Пользователь успешно удален|Пользователя нельзя удалить так как он не был заблокирован',
        'user_edit_buttons'=>'Сохранить и выйти|Отменить изменения и выйти|Отменить|Удалить пользователя',
        'user_edit_labels'=>'Редактирование пользователя|Телефон|Должность|Группа|Касса|Логин|Состояние|Создан|Создал|Пароль обновлялся|Попыток входа|Срок действия пароля|Блокирован|Фамилия|Имя|Отчество|Ф.И.О.|Слепая сверка|Сценарий|Родительный падеж|Творительный падеж',
        'user_edit_states'=>'Не заблокирован|Заблокирован',
        'user_edit_warnings'=>'У вас нет прав редактировать этого пользователя|Неправильный запрос на редактирование|Подтвердите удаление пользователя',
        'user_ip_edit_labels'=>'Логин|IP-адрес',
        'user_ip_edit_tip'=>'Для доступа с любого адреса поставьте %, для запрета X',
        'user_ips_header'=>'логин|IP-адрес',
        'user_ips_title'=>'IP-адреса пользователя',
        'users_admin_table_header'=>'Имя|Должность|Группа|Телефон|Блокирован|Удален',
        'users_buttons'=>'Назад на рабочий экран|Добавить работника|Найти|Показать все',
        'users_inspector_headers'=>'Логин|Фамилия, имя, отчество|Должность|Группа|Дата создания|Кем создан|Дата последней смены пароля|Срок действия пароля (дней)|Число попыток входа|Пользователь заблокирован',
        'users_labels'=>'Пользователи|Список пользователей|Поиск по фамилии|по должности|по кассе',
        'users_table_header'=>'Имя|Должность|Группа|Телефон|Касса|Слепая сверка|Сценарий',
        'valuable_add_title'=>'Добавление ценности',
        'valuable_edit_can_not_edit'=>'Невозможно редактировать ценность, потому что значение использовано в сверке.',
        'valuable_edit_labels'=>'Название категории|Тип машины|Номинал|Тип денежных знаков',
        'valuable_edit_title'=>'Редактирование ценности',
        'valuable_type_add_title'=>'Добавление нового типа денежных знаков',
        'valuable_type_edit_labels'=>'Название|Ярлык|Используется серийный номер',
        'valuable_type_edit_title'=>'Редактирование типа денежных знаков',
        'valuables_add_new_valuable'=>'Добавить новую ценность',
        'valuables_find_labels'=>'Поиск по машине|категории|номиналу|валюте|типу|Найти|Показать все',
        'valuables_table_headers'=>'Тип машины|Категория|Номинал|Валюта|Тип',
        'valuables_table_new_valuables_title'=>'Новые категории ценностей',
        'valuables_table_valuables_title'=>'Категории ценностей',
        'valuables_title'=>'Ценности',
        'wizard_denoms_table_headers'=>'Валюта|Номинал|Тип|Класс',
        'wizard_valuable_grades_header'=>'Категория|Тип машины|Валюта|Номинал|Тип|Класс',
        'work_scenario'=>'Рабочий сценарий',
        'wrong_currency_in_accounting_data'=>'В данных пересчета неподходящие валюты',
        'wrong_denom_in_accounting_data'=>'В данных пересчета неподходящие номиналы',
        'wrong_grade_in_accounting_data'=>'Неподходящий класс в данных пересчета',
        'wrong_index_in_accounting_data'=>'Нераспознанный индекс в данных пересчета',
        'wrong_login_pass'=>'Неверный логин или пароль. Повторите ввод.',
        'wrong_separator_number'=>'Неверный номер разделительной карты. Проверьте правильность ввода номера разделительной карты или введите его вручную.',
        'yes'=>'Да',
        'you_have_no_rights_to_work_with_recon'=>'У вас нет прав работать с записью о сверке депозита',
        'deposit_manager' => 'Корректировки депозитов',
        'card_is_free_to_use' => 'Карта свободна',
        'create_reconciliation' => 'Создать сверку с таким номером разделительной карты',
        'create_preparation' => 'Создать подготовку с таким номером разделительной карты',
        'prep_key_instruction' => 'Нажмите ENTER чтобы создать подготовку или ESC чтобы отменить создание',
        'prepared_reconciliations_header' => 'Номер карты|Касса|Оператор подготовки|Подготовка создана|Последнее изменение',
        'prepared_reconciliations' => 'Подготовки',
        'preparation_by_card_number' => 'Подготовка по карте №',
        'depositruns_exist_for_this_card' => 'Есть пересчеты для этой карты',
        'wrong_receipt_number' => 'Неправильный номер квитанции',
        'recon_in_progress' => 'Сверка в работе',
        'wrong_cashroom' => 'Депозит обрабатывался в кассе',
        'print_receipt' => 'Печать квитации',
        'receipt_labels' => 'КВИТАНЦИЯ О ПРИЁМЕ ДЕПОЗИТА|Кассовый центр|Клиент|Дата и время|Работник центра|Подпись',
        'prep_value_labels' => 'Заявленное|Номинал|Количество|Заявленная сумма',
        'cannot_change_cashroom' => 'Есть депозиты в работе. Невозможно изменить кассу',
        'create_log_copy'=>'Скачать копию журнала',
        'move_log_to_archive'=>'Сархивировать журнал',
        'log_archive' => 'Архив журнала',
        'log_will_be_moved' => 'Журнал будет сархивирован',
        'system_events_archive' => 'Архив журнала',
        'system_events' => 'Журнал событий',
        'system_events_backup_was_wrong' => 'Архив журнала событий не был создан',
        'taskrecalc_list' => 'Список файлов taskrecalc',
        'taskrecalc_list_table_header' => '№|Имя файла',
        'taskrecalc_new_files' => 'Новые файлы подготовок получены',
        'taskrecalc_go_to_button' => 'Перейти к файлам',
        'taskrecalc_table_header' => '№|PackId|Index|Year|Nominal|Sum|Count|Client|BIC|Packer|Date',
        'taskrecalc_view' => 'Просмотр XML данных taskrecalc',
        'create_prebook_recs' => 'Создать предподготовки',
        'delete_taskrecalc' => 'Удалить файл предподготовок',
        'add_new_index' => 'Добавить новый индекс',
        'add_new_client' => 'Добавить нового клиента',
        'edit_client_name' => 'Редактировать клиента',
        'new_client_bic_found' => 'Обнаружен новый Клиент',
        'wrong_client_name' => 'Код есть, но имя клиента не совпадает',
        'wrong_client_bic' => 'Код неизвестный, но имя клиента есть в списке',
        'index_not_found' => 'Поступил новый индекс',
        'name_exist_cannot_update' => 'Такое имя существует, невозможно обновить',
        'client_name_was_updated' => 'Имя клиента было изменено', 
        'client_was_added' => 'Клиент был добавлен',
        'prebook_header' => 'Номер упаковки|Имя файла',
        'prebook' => 'Предподготовки',
        'packid_for_prebook' => ' номер упаковки предподготовки',
        'create_preparation_for_prebook' => 'Создать подготовку для этого номера упаковки'

    ); 
?>