<?
// Подключаем пролог ядра Bitrix, который инициализирует основные компоненты и классы платформы.
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Устанавливаем заголовок страницы, который будет отображаться в <title> и в интерфейсе Bitrix.
$APPLICATION->SetTitle("AJAX");

// Инициализируем библиотеку AJAX из ядра Bitrix
CJSCore::Init(array('ajax'));

// Задаем уникальный идентификатор для AJAX-запроса, чтобы различать его от других запросов.
$sidAjax = 'testAjax';

// Проверяем, является ли текущий запрос AJAX-запросом, путем проверки параметра 'ajax_form' и его соответствия $sidAjax.
if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] == $sidAjax){
    // Сбрасываем буфер вывода Bitrix
    $GLOBALS['APPLICATION']->RestartBuffer();
    
    // Преобразуем PHP-массив в JSON-объект для отправки клиенту. Возвращаем результат 'HELLO' и пустую ошибку.
    echo CUtil::PhpToJSObject(array(
        'RESULT' => 'HELLO',
        'ERROR' => ''
    ));
    
    // Завершаем выполнение скрипта
    die();
}
?>

<!-- HTML-разметка для отображения результата AJAX-запроса и индикатора загрузки -->
<div class="group">
    <!-- Блок, в который будет выведен результат AJAX-запроса (например, 'HELLO'). -->
    <div id="block"></div>
    
    <!-- Блок с текстом 'wait ...', который отображается во время выполнения запроса. -->
    <div id="process">wait ... </div>
</div>

<script>
// Включаем режим отладки для Bitrix AJAX
window.BXDEBUG = true;

// Функция инициирует AJAX-запрос.
function DEMOLoad(){
    // Скрываем блок с результатом, чтобы он не отображался во время загрузки.
    BX.hide(BX("block"));
    
    // Показываем блок с индикатором загрузки ('wait ...').
    BX.show(BX("process"));
    
    // Выполняем AJAX-запрос методом BX.ajax.loadJSON, запрашивая текущую страницу с параметром ajax_form=testAjax.
    // Результат будет обработан функцией DEMOResponse.
    BX.ajax.loadJSON(
        '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>',
        DEMOResponse
    );
}

// Функция обрабатывает ответ от AJAX-запроса.
function DEMOResponse (data){
    // Выводим отладочную информацию в консоль
    BX.debug('AJAX-DEMOResponse ', data);
    
    // Устанавливаем содержимое блока #block равным значению data.RESULT ('HELLO')
    BX("block").innerHTML = data.RESULT;
    
    // Показываем блок с результатом после получения данных.
    BX.show(BX("block"));
    
    // Скрываем индикатор загрузки.
    BX.hide(BX("process"));
    
    // Запускаем пользовательское событие DEMOUpdate на элементе #block, чтобы другие скрипты могли на него отреагировать.
    BX.onCustomEvent(
        BX(BX("block")),
        'DEMOUpdate'
    );
}

// Выполняется, когда DOM полностью загружен
BX.ready(function(){
    /*
    Закомментированный код: добавляет обработчик события DEMOUpdate, который перезагружает страницу.
    BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
        window.location.href = window.location.href;
    });
    */
    
    // Скрываем блок с результатом при загрузке страницы.
    BX.hide(BX("block"));
    
    // Скрываем индикатор загрузки при загрузке страницы.
    BX.hide(BX("process"));
    
    // Привязываем обработчик события click к элементам с классом css_ajax через делегирование.
    // Делегирование позволяет обрабатывать клики на динамически добавленных элементах.
    BX.bindDelegate(
        document.body, // Элемент, к которому привязывается обработчик (весь документ).
        'click', // Событие, которое отслеживается.
        {className: 'css_ajax'}, // Селектор для элементов, на которые реагируем.
        function(e){ // Функция-обработчик события.
            // Кроссбраузерная обработка события (для старых браузеров).
            if(!e)
                e = window.event;
            
            // Запускаем функцию DEMOLoad для выполнения AJAX-запроса.
            DEMOLoad();
            
            // Предотвращаем стандартное поведение
            return BX.PreventDefault(e);
        }
    );
});
</script>

<!-- Элемент с классом css_ajax, по клику на который запускается AJAX-запрос. -->
<div class="css_ajax">click Me</div>

<?php
// Подключаем эпилог ядра Bitrix, который завершает работу страницы и выводит footer
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
