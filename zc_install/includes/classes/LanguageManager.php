<?php
/**
 * @copyright Copyright 2003-2020 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id:$
 */

class LanguageManager
{
    public function __construct($langPath = 'includes/languages/')
    {
        $this->langPath = $langPath;
    }

    public function getLanguagesInstalled()
    {
        $this->languagesInstalled = require(DIR_FS_INSTALL . $this->langPath . 'languages_installed.php');
        return $this->languagesInstalled;
    }

    public function loadLanguageDefines($lng, $currentPage, $fallback = 'en_us')
    {
        $defineListFallback = [];
        if ($lng != $fallback) {
            $defineListFallback = $this->loadDefineFile($fallback, 'main');
        }
        $defineListMain = $this->loadDefineFile($lng, 'main');
        $defineList = array_merge($defineListFallback, $defineListMain);
        $this->makeConstants($defineList);
    }

    public function loadDefineFile($lng, $file)
    {
        $defineList = [];
        $fp = DIR_FS_INSTALL . $this->langPath . $lng . '/' . $file . '.php';
        if (file_exists($fp)) {
            $defineList = require($fp);
        }
        return $defineList;
    }

    public function makeConstants($defines)
    {
        foreach ($defines as $defineKey => $defineValue) {
            preg_match_all('/%{2}([^%]+)%{2}/', $defineValue, $matches, PREG_PATTERN_ORDER);
            if (count($matches[1])) {
                foreach ($matches[1] as $index => $match) {
                    if (isset($defines[$match])) {
                        $defineValue = str_replace($matches[0][$index], $defines[$match], $defineValue);
                    }
                }
            }
            define($defineKey, $defineValue);
        }
    }
}