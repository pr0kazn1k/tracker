Придумываем новые название папкам
- track
- track-common
- track-show
    
1.
    - в файле `track/settings_path.php` меняем константу `_DIR_TRACK_` (line:3)
    - в файле `track-show/default_settings.php` меняем константу `_DIR_TRACK_` (line:3)
    - Переименовываем папке `track`

2.
    - в файле `_DIR_TRACK_/settings_path.php` меняем константу `_DIR_TRACK_COMMON_` (line:4)
    - в файле `track-show/default_settings.php` меняем константу `_DIR_TRACK_COMMON_` (line:4)
    - Переименовываем папке `track-common`
      
3.
    - в файле `_DIR_TRACK_/settings_path.php` меняем константу `_DIR_TRACK_SHOW_` (line:5)
    - в файле `track-show/default_settings.php` меняем константу `_DIR_TRACK_SHOW_` (line:5)
    - Переименовываем папке `track-show`
    
Итоговая ссылка получается `http://tracker.org/_DIR_TRACK_/{Название ссылки в трекере}` - формируется автоматически

Так же можно добавить два парамета вручную или через интерфейс `http://tracker.org/DIR_TRACK/{Название ссылки в трекере}/{Источник}/{Кампания}`

Например: `http://tracker.org/page/offer1/vk/post`

### Дополнительные правила

| Правило  | Значение   | Действие                          |
| -------- | ---------- | --------------------------------- |
| Реферер  | `{empty}`  | Выполняется если пустой referrer  |
| IP       | `{repeat}` | Выполняется если переход с таким IP уже был  |

p.s. Первый переход по сформированной ссылке может быть долгим и выполниться с ошибкой. Нужно подождать, если через 10 мин. не заработало, значит где-то ошибка
 