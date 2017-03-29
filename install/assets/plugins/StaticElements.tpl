
//<?php
/**
 * StaticElements
 * 
 * StaticElements plugin
 *
 * @category    plugin
 * @version     4.3.7.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @properties &elementsPath=папка элементов;string;assets/elements/
                            &configFileName=Название конфиг файла;string;config.php
                            &onlyAdmin=Только для админа;string;1
                            &showDebug=Дебаг;string;1

 * @internal    @events OnPageNotFound,OnWebPageInit,OnChunkFormDelete,OnChunkFormSave,OnPluginFormDelete,OnPluginFormSave,OnSnipFormDelete,OnSnipFormSave,OnTempFormDelete,OnTempFormSave
 * @internal    @modx_category system
 * @internal    @legacy_names StaticElements
 * @internal    @installset base
 *
 * @author Dzhuryn Volodymyr / updated: 2016-03-14
 */

require MODX_BASE_PATH.'assets/plugins/staticElements/plugin.staticElements.php';
