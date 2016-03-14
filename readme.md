Установка
====================

* Створити плагін "staticElements"
* в конфігурацію прописати &elementsPath=папка элементов;string;assets/elements/  &configFileName=Название конфиг файла;string;config.php
* добавити події OnWebPageInit,OnChunkFormDelete,OnChunkFormSave,OnPluginFormDelete,OnPluginFormSave,OnSnipFormDelete,OnSnipFormSave,OnTempFormDelete,OnTempFormSave
* скопіювати код із файлу plugin.staticElements.php

Робота з елементами
----------------
При першому запуск плагін створить папки для елементів (chunsk,templates,snippets,plugins)
Щоб додати новий елемент необхідно:
* Створити у відповідній папці файл
* В верху файлу додати  текст  
name:eFeedbackReport  
description:eFeedbackReport  шаблон отправки на почту  
======  
```//тут код шаблона чи чанка  
<?php  
echo phpinfo();  
?>```   

Робота з категоріями
----------------
Щоб елемент додати до категорії достатньо, створити папку з назвою категорії та перенести туди елемент.  
Наприклад, щоб чанк header був у категорії tpl достатньо щоб він знаходився у папці tpl, яка у свою чергу знаходиться у папці chunks.  
/assets/elements/chunks/tpl/header.tpl
