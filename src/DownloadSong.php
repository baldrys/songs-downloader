<?php

namespace DownloadSong;

use Madcoda\Youtube\Youtube;
use YoutubeDl\YoutubeDl;

class DownloadSong {
    private $youtube;
    private $youtubeDl;

    /**
     * Возвращает apikey из конфига
     * 
     * @todo обработать ошибку если нету файла или неправильный путь
     *
     * @param  string $file
     *
     * @return string
     */
    private function getApiKeyFromConfig($file = __DIR__ .'/../app.ini') {
        $ini = parse_ini_file($file);
        return $ini['api_key'];  
    }
    
    function __construct($downloadPath = __DIR__ .'/../downloads')
    {
        $apiKey = $this->getApiKeyFromConfig();
        $this->youtube = new Youtube(array('key' => $apiKey));
        $this->youtubeDl = new YoutubeDl([
            'extract-audio' => true,
            'audio-format' => 'mp3',
            'audio-quality' => 0, // best
            'output' => '%(title)s.%(ext)s',
        ]);
        $this->setDownloadPath($downloadPath);
    }

    public function setDownloadPath($path) {
        $this->youtubeDl->setDownloadPath($path);
    }


    /**
     * Получение id видео по названию песни
     * 
     * @todo обработать ошибки, в частности когда песня не найдена
     *
     * @param  string $songTitle
     *
     * @return string
     */
    private function getYouTubeVideoIdIdBySongTitle(string $songTitle) {
        $video = $this->youtube->searchVideos($songTitle,  $maxResults = 1);
        return $video[0]->id->videoId;
    }

    /**
     * Скачивание песни по названию
     * 
     * @todo 
     * - Обработать возможные ошибки
     * - Обработать случай когда не было найдено песни
     * - В случае успешной скачки возвращать true 
     *
     * @param  string $songTitle
     *
     * @return boolean
     */
    public function downloadSong(string $songTitle) {
        $videId = $this->getYouTubeVideoIdIdBySongTitle($songTitle);
        echo "Идет скачивание $songTitle ... \n";
        $this->youtubeDl->download('https://www.youtube.com/watch?v='.$videId);
    }

    /**
     * Скачивание песен
     * 
     * @todo
     * Возможно передавать массив или же текстовый файл
     * В каждом случае вызывается своя приватная функция парсинга
     * 
     * @param  mixed $songTitles
     *
     * @return void
     */
    public function downloadSongs($songTitles) {
        $songs = [];
        if(realpath($songTitles)){
            $songs = self::getSongsArrayFromFile($songTitles);
        }

        foreach ($songs as $song) {
            $this->downloadSong($song);
        }
    }

    /**
     * Возвращает массив песен из текстового файла
     *
     * @param  string $path
     *
     * @return array
     */
    private static function getSongsArrayFromFile(string $path) {
        $songsArray = [];
        $file = file_get_contents($path);
        $separator = "\r\n";
        $line = strtok($file, $separator);

        while ($line !== false) {
            array_push($songsArray, $line);
            $line = strtok($separator);  
        }

        return $songsArray;
    }
}