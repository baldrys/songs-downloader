<?php

namespace DownloadSong\Support;


class YouTubeApiConfig {

    /**
     * Возвращает apikey из конфига
     * 
     * @todo обработать ошибку если нету файла или неправильный путь
     *
     * @param  string $file
     *
     * @return string
     */
    public static function getApiKeyFromConfig($file = __DIR__ .'/../../app.ini') {
        $ini = parse_ini_file($file);
        return $ini['api_key'];  
    }
    
}