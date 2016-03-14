<?php
$elementsPath;
$configFileName;

$elementsPath = $_SERVER['DOCUMENT_ROOT'].'/'.$elementsPath;

$expansions = array(
    'chunks'=>'tpl',
    'templates'=>'tpl',
    'snippets'=>'php',
    'plugins'=>'php',
);

function translit($str)
{
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}


function str_replace_once($search, $replace, $text)
{
    $pos = strpos($text, $search);
    return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
}



function filePars($file)
{

    $name = $file;
    $name = basename($name, ".php");
    $name = basename($name, ".tpl");


    $fileContent = file_get_contents($file, FILE_USE_INCLUDE_PATH);
    $pos = strpos($fileContent, '======');
    if ($pos === false) { // нема
        $startText  = 'name:'.$name.PHP_EOL;
        $startText .= 'description:'.$name.PHP_EOL;
        $startText .= '======'.PHP_EOL;
        $fileContent = $startText.$fileContent;
        file_put_contents($file,$fileContent, FILE_USE_INCLUDE_PATH);

    }

    $fileArray = explode('======', $fileContent);

    $scriptHeadParamsStr = $fileArray[0];
    $scriptHeadParams = explode("\n", $scriptHeadParamsStr);

    $scriptHeadParamsFormat = array();
    foreach ($scriptHeadParams as $scriptHeadParam) {
        if (empty($scriptHeadParam)) {
            continue;
        }
        $ar = explode(':', $scriptHeadParam);
        $ar[0] = trim($ar[0]);
        $ar[1] = trim($ar[1]);

        $scriptHeadParamsFormat[$ar[0]] = $ar[1];
    }
    $scriptBody = $fileArray[1];

    if (empty($scriptHeadParamsFormat['name'])) {
        $scriptHeadParamsFormat['name'] = $name;

        $startText  = 'name:'.$name.PHP_EOL;
        $startText .= 'description:'.$name.PHP_EOL;
        $startText .= '======'.PHP_EOL;
        $fileContent = $startText.$scriptBody;
        file_put_contents($file,$fileContent, FILE_USE_INCLUDE_PATH);



    }
    return array(
        'head' => $scriptHeadParamsFormat,
        'body' => $scriptBody,
    );

}


function GetListFiles($folder, &$all_files)
{
    $fp = opendir($folder);
    while ($cv_file = readdir($fp)) {
        if (is_file($folder . "/" . $cv_file)) {
            $all_files[] = $folder . "/" . $cv_file;
        } elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder . "/" . $cv_file)) {
            GetListFiles($folder . "/" . $cv_file, $all_files);
        }
    }
    closedir($fp);
}

function categoryCheck($category)
{


    global $modx;
    $C = $modx->getFullTableName('categories');
    $cat = $modx->db->escape($category);
    $sql = 'SELECT count(id) FROM ' . $C . ' where `category`="' . $cat . '"';

    $result = $modx->db->query($sql);
    $count = $modx->db->getValue($result);


    if ($count == 0) {
        $fields = array('category' => $category);
        $modx->db->insert($fields, $C);
    }

    $sql = 'SELECT * FROM ' . $C . ' where `category`="' . $cat . '"';
    $result = $modx->db->query($sql);
    $result = $modx->db->getRow($result);


    return $result;

}

function fileNameParse($elementPath, $fileName)
{

    $clear = str_replace($elementPath . '/', '', $fileName);
    $level = mb_substr_count($clear, '/');
    if ($level <= 1) {
        $arr = explode('/', $clear);

        if (count($arr) == 1) {
            return array('category' => NULL, 'fileName' => $arr[0]);
        } else {
            return array('category' => $arr[0], 'fileName' => $arr[1]);
        }
    } else {
        return NULL;
    }
}

function fileWrite($file, $data)
{
    if ($handle = @fopen($file, 'w+')) {
        flock($handle, LOCK_EX);
        fwrite($handle, $data);
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}


function searchInArray($arr, $val)
{

    foreach ($arr as $key => $ar) {
        if ($ar['fileName'] == $val) {

            $data = $ar;
            $data['elementId'] = $key;
            return $data;
        }
    }
    return false;
}

function fileRead($fileFull)
{
    $str = '';
    $fd = fopen($fileFull, 'r+') or die("Ошибка открытия файла");

    if (flock($fd, LOCK_EX)) // установка исключительной блокировки на запись
    {
        while (!feof($fd)) {
            $str .= fgetss($fd);
        }
        flock($fd, LOCK_UN); // снятие блокировки
    }
    fclose($fd);
    return $str;
}


function getCategoryName($categoryId)
{
    global $modx;

    $C = $modx->getFullTableName('categories');

    $sql = 'SELECT * FROM ' . $C . ' where `id`="' . $categoryId . '"';
    $result = $modx->db->query($sql);
    $result = $modx->db->getRow($result);

    return $result;

}

function getFieldNames($element)
{
    //chunks,templates,snippets,plugins
    global $modx;

    switch ($element) {
        case 'chunks':
            $fieldNames = array(
                'tableName' => $modx->getFullTableName('site_htmlsnippets'),
                'name' => 'name',
                'description' => 'description',
                'code' => 'snippet',
                'category' => 'category',
                'id' => 'id'
            );
            break;
        case 'templates':
            $fieldNames = array(
                'tableName' => $modx->getFullTableName('site_templates'),
                'name' => 'templatename',
                'description' => 'description',
                'code' => 'content',
                'category' => 'category',
                'id' => 'id'
            );
            break;
        case 'snippets':
            $fieldNames = array(
                'tableName' => $modx->getFullTableName('site_snippets'),
                'name' => 'name',
                'description' => 'description',
                'code' => 'snippet',
                'category' => 'category',
                'id' => 'id'
            );
            break;
        case 'plugins':
            $fieldNames = array(
                'tableName' => $modx->getFullTableName('site_plugins'),
                'name' => 'name',
                'description' => 'description',
                'code' => 'plugincode',
                'category' => 'category',
                'id' => 'id'

            );
            break;
    }
    return $fieldNames;
}

$statusCheck = false;
$eventName = $modx->event->name;
$eventParams = $modx->Event->params;


if (!file_exists($elementsPath . $configFileName)) { //перший старт скрипта
    $statusCheck = true;
    $config = array(
        'chunks' => array(),
        'templates' => array(),
        'snippets' => array(),
        'plugins' => array(),
    );
    if (!file_exists($elementsPath)) {
        if (!mkdir($elementsPath, 0755)) {
            $modx->logEvent(2002, 3, 'Не створена папка для елементів', 'Не вдалось створити папку');
            return false;
        }
    }


    foreach ($config as $key => $el) {
        $element = $key;


        if (!file_exists($elementsPath . $element)) {  // створюєм папку для елементу
            if (!mkdir($elementsPath . $element, 0755)) {
                $modx->logEvent(2002, 3, 'Не створена папка для елементу ' . $element, 'Не вдалось створити папку');
                return false;
            }
        }

        $responseField = getFieldNames($element);
        $tableName = $responseField['tableName'];

        $sql = "select * from $tableName";
        $q = $modx->db->query($sql);
        $res = $modx->db->makeArray($q);


        $expansion = $expansions[$element];
        foreach ($res as $re) {
            $elemId = $re[$responseField['id']];
            $elemName = $re[$responseField['name']];
            $elemDescription = $re[$responseField['description']];
            $elemCode = $re[$responseField['code']];
            $elemCategory = $re[$responseField['category']];

            $fileName = translit($elemName) . '.' . $expansion;

            $fileText = 'name:' . $elemName . PHP_EOL;
            $fileText .= 'description:' . $elemDescription . PHP_EOL;
            $fileText .= '======' . PHP_EOL;
            if (in_array($element, array('snippets', 'plugins'))) {
                $fileText .= '<?php' . PHP_EOL;
            }
            $fileText .= $elemCode;

            $categoryCheckResp = getCategoryName($elemCategory);

            $categoryName = $categoryCheckResp['category'];
            $filePathFull = $elementsPath . $element . '/';
            if ($elemCategory > 0) { // якщо елемент має категорію

                $filePathFull .= $categoryName . '/';
            }

            if (!file_exists($filePathFull)) {  // створюєм папку для категорії
                if (!mkdir($filePathFull, 0755)) {
                    $modx->logEvent(2002, 3, 'Не створена папка для категоії ' . $categoryName . ' елемент ' . $element, 'Не вдалось створити папку');
                    return false;
                }
            }


            fileWrite($filePathFull . $fileName, $fileText); //збрегшаєм файл
            $config[$element][$elemId] = array(
                'elementName' => $elemName,
                'fileName' => $fileName,
                'categoryId' => $elemCategory,
                'category' => $categoryName,
                'date' => time()
            );

        }


    }
    fileWrite($elementsPath . $configFileName, json_encode($config));
}

$config = fileRead($elementsPath . $configFileName);
$config = json_decode($config, true);
if ($eventName == 'OnWebPageInit') {

    foreach ($config as $key => $el) {

        $element = $key; // нахва едеменьу
        $elementPath = $elementsPath . $element;

        if (!file_exists($elementPath)) {
            mkdir($elementPath, 0700, true);
        }

        $files = array();
        $parseFiles = array();
        GetListFiles($elementPath, $files);
        foreach ($files as $file) {
            $resp = fileNameParse($elementPath, $file);
            if (isset($resp)) {
                $parseFiles[] = $resp;
            }
        }


        $fieldNames = getFieldNames($element);


        foreach ($parseFiles as $file) {

            $categoryId = NULL;

            if (isset($file['category'])) {
                $categoryId = categoryCheck($file['category']);
                $categoryId = $categoryId['id'];
            }

            $response = searchInArray($config[$element], $file['fileName']);
            //echo $file['fileName'] .' - '.!empty($response).'<br>';
            if (!empty($response)) { //файл уже є


                $fileFull = $elementPath;
                if (!empty($file['category'])) {
                    $fileFull .= '/' . $file['category'];
                }
                $fileFull .= '/' . $file['fileName'];
                $fileFullParams = filePars($fileFull);


                if (filemtime($fileFull) > $response['date'] && isset($fileFullParams)) {
                   // echo 'файл оновлено';
                    $statusCheck= true;

                    $fieldName = $fieldNames['name'];
                    $elementTable = $fieldNames['tableName'];
                    $code = $modx->db->escape($fileFullParams['body']);
                    $code =  str_replace_once('<?php', '', $code);
                    $fields = array(
                        $fieldNames['name'] => $modx->db->escape($fileFullParams['head']['name']),
                        $fieldNames['description'] => $modx->db->escape($fileFullParams['head']['description']),
                        $fieldNames['code'] => $code,
                        $fieldNames['category'] => 0,
                    );

                    if (!empty($categoryId)) {
                        $fields['category'] = $categoryId;
                    }
                    $modx->db->update($fields, $elementTable, 'id = "' . $response['elementId'] . '"');
                    $config[$element][$response['elementId']]['date'] = time(); // обновляем дату
                }


            } else { //новий файл

                $statusCheck= true;
                //echo 'новий файл';
                $fileFull = $elementPath;
                if (!empty($file['category'])) {
                    $fileFull .= '/' . $file['category'];
                }
                $fileFull .= '/' . $file['fileName'];

                $fileFullParams = filePars($fileFull);
                if (isset($fileFullParams)) {

                    $code = $modx->db->escape($fileFullParams['body']);
                    $code =  str_replace_once('<?php', '', $code);

                    $fields = array(
                        $fieldNames['name'] => $fileFullParams['head']['name'],
                        $fieldNames['description'] => $fileFullParams['head']['description'],
                        $fieldNames['code'] => $code,
                        $fieldNames['category'] => 0
                    );
                    if (!empty($categoryId)) {
                        $fields['category'] = $categoryId;
                    }
                    $name = $modx->db->escape($fileFullParams['head']['name']);
                    $fieldName = $fieldNames['name'];
                    $elementTable = $fieldNames['tableName'];

                    $elementId = $modx->db->getValue($modx->db->query("select `id` from $elementTable where $fieldName ='" . $name . "'"));

                    //echo $elementId . ' ' . $file['fileName'] . '<br>';
                    if (empty($elementId)) {
                        $modx->db->insert($fields, $elementTable);
                        $elementId = $modx->db->getValue($modx->db->query("select `id` from $elementTable where $fieldName ='" . $name . "'"));
                    } else {
                        $modx->db->update($fields, $elementTable, 'id = "' . $elementId . '"');
                    }
                    $config[$element][$elementId] = array('elementName' => $fileFullParams['head']['name'], 'fileName' => $file['fileName'], 'categoryId' => $categoryId, 'category' => $file['category'], 'date' => time());
                }
            }
        }
    }

}
elseif(in_array($eventName,array('OnSnipFormSave','OnChunkFormSave','OnTempFormSave','OnPluginFormSave'))){

    $debugFile = $_SERVER['DOCUMENT_ROOT'].'/debug.txt';

    //chunks,templates,snippets,plugins
    switch($eventName){
        case 'OnSnipFormSave':
            $element = 'snippets';
            break;
        case 'OnChunkFormSave':
            $element = 'chunks';
            break;
        case 'OnTempFormSave':
            $element = 'templates';
            break;
        case 'OnPluginFormSave':
            $element = 'plugins';
            break;
        default:
            $element = 'plugins';
    }

    $fieldNames = getFieldNames($element);

    $elemId = $eventParams['id'];
    $elemName = $_POST[$fieldNames['name']];
    $elemDescription = $_POST[$fieldNames['description']];
    $elemCode = $_POST['post'];
    $elemCategory = $_POST['categoryid'];

    $expansion = $expansions[$element];
    $fileName = translit($elemName) . '.' . $expansion;



    $fileText = 'name:' . $elemName . PHP_EOL;
    $fileText .= 'description:' . $elemDescription . PHP_EOL;
    $fileText .= '======' . PHP_EOL;
    if (in_array($element, array('snippets', 'plugins'))) {
        $fileText .= '<?php' . PHP_EOL;
    }
    $fileText .= $elemCode;

    $categoryCheckResp = getCategoryName($elemCategory);

    $categoryName = $categoryCheckResp['category'];
    $filePathFull = $elementsPath . $element . '/';
    if ($elemCategory > 0) { // якщо елемент має категорію

        $filePathFull .= $categoryName . '/';
    }

    if (!file_exists($filePathFull)) {  // створюєм папку для категорії
        if (!mkdir($filePathFull, 0755)) {
            $modx->logEvent(2002, 3, 'Не створена папка для категоії ' . $categoryName . ' елемент ' . $element, 'Не вдалось створити папку');
            return false;
        }
    }

    if(isset($config[$element][$elemId]['categoryId'])){
        // отримуэм стару категорію
        $oldPathFull = $elementsPath . $element . '/';
        $oldFileName = $config[$element][$elemId]['fileName'];
        if($config[$element][$elemId]['categoryId']>0){
            $oldPathFull .= $config[$element][$elemId]['category'] . '/';
        }
    }

    if(isset($config[$element][$elemId]['categoryId']) && ($elemCategory!=$config[$element][$elemId]['categoryId'] || $fileName!=$config[$element][$elemId]['fileName'])){
        //видаляєм файл при зміні категорії чи назви
        unlink($oldPathFull . $oldFileName);

    }

    fileWrite($filePathFull . $fileName, $fileText); //збрегшаєм файл
    $config[$element][$elemId] = array(
        'elementName' => $elemName,
        'fileName' => $fileName,
        'categoryId' => $elemCategory,
        'category' => $categoryName,
        'date' => time()
    );

    //file_put_contents($debugFile,$elemId);
	$statusCheck = false;
}
elseif(in_array($eventName,array('OnSnipFormDelete','OnChunkFormDelete','OnTempFormDelete','OnPluginFormDelete'))){
  $debugFile = $_SERVER['DOCUMENT_ROOT'].'/debug.txt';

    $elemId = $eventParams['id'];

    switch($eventName){
        case 'OnSnipFormDelete':
            $element = 'snippets';
            break;
        case 'OnChunkFormDelete':
            $element = 'chunks';
            break;
        case 'OnTempFormDelete':
            $element = 'templates';
            break;
        case 'OnPluginFormDelete':
            $element = 'plugins';
            break;
        default:
            $element = 'plugins';
    }

    $elementConfig = $config[$element][$elemId];

    $deleteElemPath = $elementsPath.$element.'/';

    if(!empty($elementConfig['category'])){
        $deleteElemPath = $deleteElemPath.$elementConfig['category'].'/';
    }
    $deleteElemFull = $deleteElemPath. $elementConfig['fileName'];
    unlink($deleteElemFull);
    file_put_contents($debugFile,$deleteElemFull);

	$statusCheck = false;
}


fileWrite($elementsPath . $configFileName, json_encode($config));

$modx->clearCache('full');
if($statusCheck){

    $redUrl = $_SERVER['REQUEST_URI'];
    header("Location: $redUrl");
}
