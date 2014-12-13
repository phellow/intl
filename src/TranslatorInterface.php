<?php
namespace Phellow\Intl;

/**
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/intl
 */
interface TranslatorInterface
{

    /**
     * Translate a text.
     *
     * @param string $text   Text to translate.
     * @param string $locale Locale to translate to.
     *
     * @return string Translated text or given text if no translation found.
     */
    public function translate($text, $locale);

    /**
     * Translate a text based on a number.
     *
     * @param string  $textSingular Text in its singular form.
     * @param string  $textPlural   Text in its plural form.
     * @param int     $number       Number to choose form.
     * @param string  $locale       Locale to translate to.
     *
     * @return string Translated text or given text if no translation found.
     */
    public function translatePlurals($textSingular, $textPlural, $number, $locale);
}