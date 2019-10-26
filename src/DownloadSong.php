<?php

namespace DownloadSong;

use YoutubeDl\YoutubeDl;
use Madcoda\Youtube\Youtube;
use DownloadSong\Support\SongsParser;
use DownloadSong\Support\YouTubeApiConfig;


class DownloadSong {

    private $youtubeApi;
    private $youtubeDl;

    function __construct($downloadPath = __DIR__ .'/../downloads')
    {
        $apiKey = YouTubeApiConfig::getApiKeyFromConfig();
        $this->youtubeApi = new Youtube(array('key' => $apiKey));
        $this->youtubeDl = new YoutubeDl([
            'extract-audio' => true,
            'audio-format' => 'mp3',
            'audio-quality' => 0, // best
            'output' => '%(title)s.%(ext)s',
        ]);
        $this->setDownloadPath($downloadPath);
    }


    /**
     * Установка пути для скачивания
     *
     * @param  string $path
     *
     * @return YoutubeDl\YoutubeDl;
     */
    public function setDownloadPath($path) {
        $this->youtubeDl->setDownloadPath($path);
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
        $video = $this->youtubeApi->searchVideos($songTitle,  $maxResults = 1);
        $videId = $video[0]->id->videoId;
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
            $songs = SongsParser::getSongsArrayFromFile($songTitles);
        }

        foreach ($songs as $song) {
            $this->downloadSong($song);
        }
    }
}