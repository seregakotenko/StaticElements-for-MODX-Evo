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
* Створити у відповідній папці файла
* В верху файлу додати  текст  
name:eFeedbackReport  
description:eFeedbackReport  шаблон отправки на почту  
======  
```//тут код шаблона чи чанка  
<?php  
echo phpinfo();  
?>```   