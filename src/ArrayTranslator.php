<?php
namespace Phellow\Intl;

/**
 * Translator that stores translations in an array.
 *
 * There must be a directory where all locale files are stored.
 * A locale file must be a php file that returns an array like:
 * return [
 *     'pluralForm' => *callable plural form or null*
 *     'texts' => [
 *         'someKey' => 'some translation',
 *         'someKeyWithPlural' => [
 *             'singular translation',
 *             'plural translation',
 *         ]
 *     ]
 * ]
 *
 * Plural forms:
 * Each locale has its own plural form. They are usually defined as strings like the following:
 * nplurals=2; plural=(n != 1)
 * nplurals=2; plural=(n > 1);
 * nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;
 * nplurals=1; plural=0;
 *
 * For this class, a plural form is a callable function that just reflects that string definition.
 * For example:
 * function (&$nplurals, &$plural, $n) {
 *     $nplurals = 2; $plural = ($n != 1);
 * }
 *
 * Note, that the first two function parameters must be references!
 *
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/intl
 */
class ArrayTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $localeDir;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $localeDir Directory where locale php files are stored.
     */
    public function __construct($localeDir)
    {
        $this->localeDir = $localeDir;
    }

    /**
     * Set translations and plural form for the given locale.
     *
     * @param string $locale
     */
    protected function initLocale($locale)
    {
        $file = $this->localeDir . '/' . $locale . '.php';
        if (file_exists($file)) {
            $data = include $file;

            if (!is_array($data)) {
                $data = [];
            }
        } else {
            $data = [];
        }

        if (!isset($data['pluralForm']) || !is_callable($data['pluralForm'])) {
            $data['pluralForm'] = null;
        }
        if (!isset($data['texts'])) {
            $data['texts'] = [];
        }

        $this->data[$locale] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($text, $locale)
    {
        if (!isset($this->data[$locale])) {
            $this->initLocale($locale);
        }

        $translations = $this->data[$locale]['texts'];

        if (isset($translations[$text])) {
            $value = $translations[$text];
            if (is_array($value)) {
                $text = reset($value);
            } else {
                $text = $value;
            }
        }
        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function translatePlurals($textSingular, $textPlural, $number, $locale)
    {
        if (!isset($this->data[$locale])) {
            $this->initLocale($locale);
        }

        if ($this->data[$locale]['pluralForm'] !== null) {
            $idx = $this->getPluralIndex($this->data[$locale]['pluralForm'], $number);
        } else {
            $idx = ($number == 1 ? 0 : 1);
        }

        $translations = $this->data[$locale]['texts'];

        if (isset($translations[$textSingular]) && is_string($translations[$textSingular])) {
            $text = $translations[$textSingular];
        } elseif (isset($translations[$textSingular][$idx])) {
            $text = $translations[$textSingular][$idx];
        } else {
            $text = $idx > 0 ? $textPlural : $textSingular;
        }

        return $text;
    }

    /**
     * Get plural form index for the given number.
     *
     * @param callable $pluralForm
     * @param int      $number
     *
     * @return int
     */
    protected function getPluralIndex(callable $pluralForm, $number)
    {
        $nplurals = 0;
        $plural = 0;

        call_user_func_array($pluralForm, [&$nplurals, &$plural, $number]);

        if (is_bool($plural)) {
            $plural = $plural ? 1 : 0;
        }

        if ($plural < 0) {
            $plural = 0;
        }

        if ($plural > $nplurals - 1) {
            $plural = $nplurals - 1;
        }

        return $plural;
    }
}
