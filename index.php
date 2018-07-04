<?
// файл общей конфигурации
require_once("conf/settings.php");
// библиотека для парсинга
require_once ("vendor/simple_html_dom/simple_html_dom.php");
// набор статических функций
require_once("core/functions.php");
//домен для парсинга
$urlPars = "bash.im";
//получение данных
$result = my_parser_url(
    "https://$urlPars/", // URL для получения данных
    "#body", // искомый глобальный блок
    function ( $body ) use ($urlPars) { // функция для обработки содержимого
        // переменная для сохранения результата
        $res = '';
        // проход по каждому элементу списка
        foreach ( $body->find('.quote') as $list ){
            // сохраняем данные прохода
            $res .= "<p><a href='//$urlPars".$list->find('a.id', 0)->href."'>".$list->find('.text', 0)->innertext."</a></p>";
        }
        return $res; // возвращаем результат
    }
);
// вывод результата полученный в результате парсинга
echo $result;


