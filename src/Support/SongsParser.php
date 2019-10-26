<?php

namespace DownloadSong\Support;

class SongsParser {

    /**
     * Возвращает массив песен из текстового файла
     *
     * @param  string $path
     *
     * @return array
     */
    public static function getSongsArrayFromFile(string $path) {
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