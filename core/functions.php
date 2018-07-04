<?php
//отправка get запроса
function my_file_get_contents(string $url = '', array $headers = null, string $cookie = null, string $proxy = null, string $proxyauth = null): string {
    //возвращаемые данные
    $data = null;
    try{
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url, //url отправки данных
            CURLOPT_ENCODING => "UTF-8", //кодировка данных
            CURLOPT_HEADER => false, //не возвращать заголовки
            CURLOPT_RETURNTRANSFER => true, //необрабатывать ответ
            CURLOPT_AUTOREFERER => true, //автоматическая установка поля Referer
            CURLOPT_FOLLOWLOCATION => true, //автопереходы при редиректах
            CURLOPT_TIMEOUT => 15, //время ожидание в секундах
            CURLOPT_CONNECTTIMEOUT => 5, //время конекта в секундах
            CURLOPT_SSL_VERIFYPEER => false, //для обращения к https
            CURLOPT_SSL_VERIFYHOST => false, //для обращения к https
        ]);
        //если используется дополнительные заголовки
        if( $headers and is_array($headers) and count($headers) ) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } else { // вбиваем юзер агент для стандарта
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0');
        }
        //если используется файл кук
        if( $cookie ){
            if( !file_exists($cookie) ){
                $fp = fopen($cookie, "w+");
                fwrite($fp, "");
                fclose($fp);
            }
            curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
        }
        //проверка параметров для прокси соединения
        if( $proxy ){
            //прокси сервер
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
            //используемый драйвер
            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
            //необходимо ли авторизироваться
            if($proxyauth){
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);
            }
        }
        //получам данные
        $data = curl_exec($curl);
        //проверка на ошибку
        $data = $data ? $data : curl_error($curl);
        //закрываем соединение
        curl_close($curl);
    } catch (Exception $e) {
        $data = null;
    }
    return $data;
}

// основные параметры парсинга
function my_parser_url(string $url = null, string $find = null, callable $callable = null ) {
    if( !$url ) return false;
    $dataText = my_file_get_contents(
        $url, //url для чтения
        [ //передаваемые заголовки
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3',
            'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip,deflate',
            'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ],
        dirname(__FILE__) . '/../temp/cookie.txt' //файл для сохранения кук
    );
    //если в поиск передается "NULL", то данные передаются в функцию без преобразования
    //на случай если нам необходимо отпарсить json
    if( $find === null ){
        //если запускаемая функция не передана, возвращаем полученные данные в исходном состоянии
        return $callable ? $callable($dataText) : $dataText;
    }
    //преобразуем данные в объект
    $dataObject = str_get_html( $dataText );
    //получаем необходимый элемент в DOM
    $dataObject = $find ? $dataObject->find($find, 0) : $dataObject;
    //возвращаем результат функции с переданными данными или полученный объект
    return $callable ? $callable($dataObject) : $dataObject;
}