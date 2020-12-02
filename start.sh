#!/usr/bin/env bash

green=$(tput setf 2)
toend=$(tput hpa $(tput cols))$(tput cub 6)

echo -n 'Как к вам обращаться?: '
read name
echo "Привет тебе $name! Мы начинаем старт докера для проекта tutmesto.ru"
echo -n "$name, ты хочешь использовать дамп для БД? (y/n): "
read use_dump
echo 'Сейчас мы запустим сборку докера!'
docker-compose up -d || exit
echo -en '\n'
echo -n "Докер успешно собрался! ${green}${toend}[OK]"
echo -en '\n'
echo 'Теперь нам необходимо собрать композер.'
./composer-install.sh
echo -en '\n'
echo -n "Композер успешно собрался ${green}${toend}[OK]"
echo -en '\n'
echo 'Сейчас надо будет заснуть на 40 секунд, чтобы успела развернуться postgres-ка'
sleep 5
echo 'Осталось еще 35 секунд...'
sleep 5
echo 'Осталось еще 30 секунд...'
sleep 5
echo 'Осталось еще 25 секунд...'
sleep 5
echo 'Осталось еще 20 секунд...'
sleep 5
echo 'Осталось еще 15 секунд...'
sleep 5
echo 'Осталось еще 10 секунд...'
sleep 5
echo 'Осталось еще 5 секунд...'
sleep 5
echo 'Сон завершился. По идее postgres-ка уже поднялась и сейчас мы будем закачивать дамп!'

case "$use_dump" in
    y|Y) ./dump.sh
         echo -en '\n'
         echo -n "Дамп успешно закачался! ${green}${toend}[OK]"
         echo -en '\n'
    ;;
    *) echo "$name, хорошо, обойдемся без дампа! =)"
    ;;
esac
echo 'Теперь нам надо провести миграции!'
./migrations-migrate.sh
echo -en '\n'
echo -n "Миграции успешно проведены! ${green}${toend}[OK]"
echo -en '\n'
echo 'Теперь почистим кэш!'
./php-fpm-command.sh rm -rf var/cache/*
./php-fpm-command.sh chmod 777 var/ -R
./cache-clear.sh
echo -en '\n'
echo -n "Кэш успешно очищен! ${green}${toend}[OK]"
echo -en '\n'
echo 'Теперь скопируем настройки для локалки!'
./env.sh
echo -en '\n'
echo -n "Настройки для локалки скопированы! ${green}${toend}[OK]"
echo -en '\n'
echo "Теперь, $name, ты можешь пользоваться локалкой! Открой в браузере localhost:7777 и наслаждайся!"
echo -en '\n'
echo "------------------------------------------------------------------------------"
echo -en '\n'
echo "ОСНОВНЫЕ КОМАНДЫ КОТОРЫЕ МОЖНО ИСПОЛЬЗОВАТЬ:"
echo "./cache-clear.sh                            |Очистка кэша symfony 4"
echo "./composer.sh [command(ex. install)]        |Обращение к композеру"
echo "./composer-install.sh                       |Запуск composer install"
echo "./connect-to-php-fpm.sh                     |Подключение к консоли php"
echo "./console.sh [command(ex. cache:clear)]     |Запуск команды php bin/console"
echo "./destroy.sh                                |Жесткое сворачивание локалки. Убивает все кроме образов."
echo "./dump.sh                                   |Закачать дамп, который находится в корне (dump.sql)"
echo "./env.sh                                    |Скопировать настройки для локалки"
echo "./migrations-migrate.sh                     |Провести миграции"
echo "./php-fpm-command.sh [command(ex. php -m)]  |Выполнить команду в php-fpm контейнере"
echo "./start.sh                                  |Запуск локалки (этот скрипт)"
echo "./stop.sh                                   |Gracefull shutdown локалки"
echo -en '\n'
echo "ДЛЯ УДОБНОГО ПОЛЬЗОВАНИЯ В ДАМПЕ БЫЛИ СОЗДАНЫ СЛЕДУЮЩИЕ ПОЛЬЗОВАТЕЛИ:"
echo "client@c.cc    | QWEasd123"
echo "admin@a.aa     | QWEasd123"
echo "moderator@m.mm | QWEasd123"
echo -en '\n'
echo "------------------------------------------------------------------------------"
echo -en '\n'
echo -en '\n'
echo 'OtezVikentiy brain corporation!'
echo -en '\n'
echo -en '\n'