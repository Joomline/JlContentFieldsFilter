# JlContentFieldsFilter
Модуль фильтрации материалов Joomla по дополнительным полям. Поддерживает типы полей text, radio, list, checkboxes.

Official page: [RU](https://joomline.ru/rasshirenija/moduli/jlcontentfieldsfilter.html) - [EN](http://joomline.org/extensions/modules-for-joomla/jlcontentfieldsfilter.html)

## Joomla 4 и Joomla 5
### Модуль и плагин
Модуль и плагин переписаны с учётом структуры файлов и классов Joomla 4. Устаревшие методы Joomla API заменены на новые.
Расширения проверены на предмет ошибок на PHP 8.1.9 и Joomla 5.0.0-alpha2. Это означает, что пакет должен без проблем работать на всех версиях Joomla 4, а так же Joomla 5.
### Компонент
В компоненте проведена работа по обновлению устаревших методов Joomla API на новые. Компонент проверен на предмет ошибок на PHP 8.1.9 и Joomla 5.0.0-alpha2.
## Фильтрация
Фильтр по значением полей работает в материалах (com_content), контактах (com_contact) и тегах (com_tags, список сущностей по тегу).
### Фильтрация в компоненте тегов (com_tags)
На данный момент в списке сущностей по тегу фильтруются только материалы Joomla (articles). Фильтрации сущностей **разных компонентов** по значениям их полей нет.

## Установка
Пока дистрибутив не собран вы можете собрать его и установить модуль и плагин сами. Для этого скачайте ветку master (архивом), установите полученный архив, опубликуйте плагин, опубликуйте и настройте модуль.

Проверялось на Joomla 3.8.2.
Версия 3.0.0 проверялась на Joomla 4.3.3 и Joomla 5.0.0-alpha2.

## Статистика

![GitHub all releases](https://img.shields.io/github/downloads/joomline/JlContentFieldsFilter/total?style=for-the-badge&color=blue)  ![GitHub release (latest by SemVer)](https://img.shields.io/github/downloads/Joomline/JlContentFieldsFilter/latest/total?style=for-the-badge&color=blue)
