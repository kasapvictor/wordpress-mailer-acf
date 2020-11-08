# Кастомный mailer для WordPress

portfolio - одностраничная тема для WordPress

Мэйлер отправляет письма на почту с фложениями.<br>
Проверка на бота reCaptcha V2

Испольщуя плагин ACF можно добавить в админку страницу с опциями, в которой вывести настройки для почты.<br>
Пример получения данных из этих полей в файле `mail.php`

## Структура файлов в WordPress

<pre>
`[wp-content]
 |--[themes]
    |--[portfolio]
        |--[functions]
            |--enqueue.php
        |--[mailer]
        |--[partials]
           |--partial-form.php
         index.php
         functions.php`
</pre>
index.php - <br>`<?php get_template_part( 'partials/partial-form'); ?>` <br><br>
functions.php - <br>`require get_template_directory() . '/functions/enqueue.php';` <br><br>