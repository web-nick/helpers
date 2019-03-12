<?php

namespace wn\libraries\helpers;

/**
 * Helper для работы с web-страницами
 *
 * @package application\helpers
 */
class WebHelper
{
    protected $curl;

    /**
     * @param string $url
     * @param array $options
     * @throws \Exception
     */
    public function __construct(string $url, array $options = [])
    {
        $this->checkURL($url);

        $this->curl = curl_init();

        $this->setOptions($options + [
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_CONNECTTIMEOUT => $timeout = 10,
                CURLOPT_TIMEOUT => $timeout,
            ]);
    }

    /**
     * Установить опции для curl с помощью метода curl_setopt_array()
     *
     * @param array $options
     * @throws \Exception
     */
    public function setOptions(array $options): void
    {
        if (!curl_setopt_array($this->curl, $options))
            throw new \Exception('Incorrect CURL option');
    }

    /**
     * Вернуть описание последней CURL ошибки
     *
     * @return string
     */
    public function getError(): string
    {
        return curl_error($this->curl);
    }


    /**
     * Проверить что страница отвечает статусом 200
     *
     * @return bool
     */
    public function isPage200(): bool
    {
        curl_setopt($this->curl, CURLOPT_NOBODY, true);

        curl_setopt($this->curl, CURLOPT_HEADER, true);

        $result = curl_exec($this->curl);

        return ($result and stripos($result, '200 OK')) ? true : false;
    }

    /**
     * Получить страницу
     *
     * @return null|string
     */
    public function getPage(): ?string
    {
        $result = curl_exec($this->curl);

        return $result ? $result : null;
    }

    /**
     * Скопировать содержимое с url-а в файл
     *
     * @param string $to путь к файлу
     *
     * @return bool
     */
    public function copyTo(string $to): bool
    {
        $file = fopen($to, 'wb');

        curl_setopt($this->curl, CURLOPT_FILE, $file);

        $result = curl_exec($this->curl);

        fclose($file);

        if (!$result) {
            unlink($to);
            return false;
        } else
            return true;
    }

    /**
     * Установить новый URL для работы
     *
     * @param string $url
     * @return $this
     * @throws \Exception
     */
    public function setURL(string $url): WebHelper
    {
        $this->checkURL($url);

        $this->setOptions([CURLOPT_URL => $url]);

        return $this;
    }

    /**
     * Проверяет URL на валидность
     *
     * @param string $url
     * @return bool
     */
    public static function isValidURL(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    /**
     * Останавливает выполнение, если URL не валидный
     *
     * @param string $url
     * @throws \Exception
     */
    protected function checkURL(string $url): void
    {
        if (!self::isValidURL($url))
            throw new \Exception("Incorrect URL: '$url'");
    }
}