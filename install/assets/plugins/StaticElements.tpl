
//<?php
/**
 * StaticElements
 * 
 * StaticElements plugin
 *
 * @category    plugin
 * @version     4.3.7.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @properties1 &elementsPath=папка элементов;string;assets/elements/ &configFileName=Название конфиг файла;string;config.php
 * @internal    @properties &alert_time=Критичное время генерации;int;3 &alert_query=Критичное число запросов;int;100
 * @internal    @events OnWebPageInit,OnChunkFormDelete,OnChunkFormSave,OnPluginFormDelete,OnPluginFormSave,OnSnipFormDelete,OnSnipFormSave,OnTempFormDelete,OnTempFormSave
 * @internal    @modx_category system
 * @internal    @legacy_names StaticElements
 * @internal    @installset base
 *
 * @author Dzhuryn Volodymyr / updated: 2016-03-14
 */

require MODX_BASE_PATH.'assets/plugins/staticElements/plugin.staticElements.php';

&pluginStyleFormats=Custom Style Formats;textarea;Title,cssClass|Title2,cssClass &pluginCustomParams=Custom Parameters <b>(Be careful or leave empty!)</b>;textarea; &pluginEntityEncoding=Entity Encoding;list;named,numeric,raw;named &pluginEntities=Entities;text; &pluginPathOptions=Path Options;list;Site config,Absolute path,Root relative,URL,No convert;Site config &pluginResizing=Advanced Resizing;list;true,false;false &pluginDisabledButtons=Disabled Buttons;text; &pluginWebTheme=Web Theme;test;webuser &pluginWebPlugins=Web Plugins;text; &pluginWebButtons1=Web Buttons 1;text;bold italic underline strikethrough removeformat alignleft aligncenter alignright &pluginWebButtons2=Web Buttons 2;text;link unlink image undo redo &pluginWebButtons3=Web Buttons 3;text; &pluginWebButtons4=Web Buttons 4;text; &pluginWebAlign=Web Toolbar Alignment;list;ltr,rtl;ltr &width=Width;text;100% &height=Height;text;400px &inlineMode=<b>Inline-Mode</b>;list;enabled,disabled;disabled &inlineTheme=<b>Inline-Mode</b><br/>Theme;text;inline &editableClass=<b>Inline-Mode</b><br/>CSS-Class selector;text;editable &editableIds=<b>Inline-Mode</b><br/>Editable<br/>Modx-Phs->CSS-IDs<br/>(line-breaks allowed);textarea;longtitle->#modx_longtitle,content->#modx_content