## Как это работает?

- За запросы на получаение данных отвечает класс `\App\Services\Request\RequestService`
- Функционал загрузки HTML реализуется в `\App\Services\Parser\ParserService`
- Основной ункционал парсинга работает между классами `\App\Services\Parser\ParserService`, `\App\Services\Parser\ParserCollection` и `\App\Services\Parser\ParserItemService`
- Раз в час (условно) работает комманда Loader, которая дергает методы из Сервисов `\App\Services\Loaders\...`
- Комманду можно запустить вручную для всех сервисов не используя параметры, или с параметром `--service=<service>` для запуска отдельного сервиса

## Расширение функционала

- Чтобы расширить функционал и настроить работу на другой сайт, еобходимо создать новый loaderService в `\App\Services\Loaders\`...
- Важно, чтоб loader имплементировался от `\App\Interfaces\PageParser`, где будет метод, отвечающий за получение и парсинг (run) и метод сохранения (saveData)
- После создания сервиса необходимо его декларировать в `\App\Services\LoadService` в константах `ALLOW_LOAD_SERVICES`, и чтобы он выводился в списке сервисов на фронте `ALLOW_SERVICES_NAMES`
- В принципе этого достаточно, чтобы при следующей отработке комманды она активировала новый сервис

`php artisan storage:links` для генерации симлинка `public/storage/`
