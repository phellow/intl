<?php
namespace Phellow\Intl;

/**
 * @author    Christian Blos <christian.blos@gmx.de>
 * @copyright Copyright (c) 2014-2015, Christian Blos
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/phellow/intl
 */
class IntlService
{
    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var array
     */
    protected $availableLocales = [];

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var string
     */
    protected $translateReplaceRegex = '/\${key}/';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param string $defaultLocale
     * @param array  $availableLocales
     */
    public function __construct($defaultLocale, array $availableLocales = [])
    {
        $this->defaultLocale = $defaultLocale;
        $this->availableLocales = $availableLocales;
    }

    /**
     * @param string $locale
     *
     * @return void
     */
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param array $locales
     */
    public function setAvailableLocales(array $locales)
    {
        $this->availableLocales = $locales;
    }

    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    /**
     * @param string $locale
     * @param bool   $ignoreAvailable
     *
     * @return void
     */
    public function setLocale($locale, $ignoreAvailable = false)
    {
        if ($ignoreAvailable || in_array($locale, $this->availableLocales)) {
            $this->currentLocale = $locale;
        } else {
            $this->currentLocale = null;
        }
    }

    /**
     * Get current or default locale.
     *
     * @return string
     */
    public function getLocale()
    {
        if ($this->currentLocale !== null) {
            return $this->currentLocale;
        }
        return $this->defaultLocale;
    }

    /**
     * Set regular expression for translated text replacements.
     *
     * Default is "/\${key}/".
     * Use the markup "key" to replace it with the replacement variable.
     *
     * @param string $regex
     *
     * @return void
     */
    public function setTranslateReplaceRegex($regex)
    {
        $this->translateReplaceRegex = $regex;
    }

    /**
     * @return string
     */
    public function getTranslateReplaceRegex()
    {
        return $this->translateReplaceRegex;
    }

    /**
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Replace text variables.
     *
     * @param string     $text
     * @param array|null $replacements
     *
     * @return string
     */
    protected function replace($text, $replacements)
    {
        if (is_array($replacements)) {
            foreach ($replacements as $var => $replacement) {
                $regex = str_replace('key', preg_quote($var, '/'), $this->translateReplaceRegex);
                $text = preg_replace($regex, $replacement, $text);
            }
        }
        return $text;
    }

    /**
     * Translate a text.
     *
     * @param string     $text         The text to translate.
     * @param array|null $replacements Values that should be replaced.
     * @param string     $locale       The locale or null to use the current locale.
     *
     * @return string Translated text or given text if no translation found.
     */
    public function _($text, $replacements = null, $locale = null)
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        if ($this->translator !== null) {
            $text = $this->translator->translate($text, $locale);
        }

        return $this->replace($text, $replacements);
    }

    /**
     * Translate the given text based on the given number.
     *
     * @param string     $textSingular The text in its singular form.
     * @param string     $textPlural   The text in its plural form.
     * @param int        $number       The number.
     * @param array|null $replacements Values that should be replaced.
     * @param string     $locale       The locale or null to use the current locale.
     *
     * @return string Translated text or given text if no translation found.
     */
    public function _n($textSingular, $textPlural, $number, $replacements = null, $locale = null)
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        if ($this->translator !== null) {
            $text = $this->translator->translatePlurals($textSingular, $textPlural, $number, $locale);
        } else {
            $text = $number == 1 ? $textSingular : $textPlural;
        }

        return $this->replace($text, $replacements);
    }

    /**
     * Format DateTime to current locale format.
     *
     * @param \DateTime    $datetime DateTime to format.
     * @param string|array $format   Format string or array like [datetype, timetype].
     * @param int          $calendar IntlDateFormatter calendar to use.
     *
     * @return string
     */
    public function formatDateTime(\DateTime $datetime, $format, $calendar = \IntlDateFormatter::GREGORIAN)
    {
        $types = array(
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
        );
        if (is_array($format)) {
            if (isset($format[0])) {
                $types[0] = $format[0];
            }
            if (isset($format[1])) {
                $types[1] = $format[1];
            }
            $pattern = null;
        } else {
            $pattern = $format;
        }
        $formatter = new \IntlDateFormatter(
            $this->currentLocale,
            $types[0],
            $types[1],
            $datetime->getTimezone()->getName(),
            $calendar,
            $pattern
        );
        return $formatter->format($datetime);
    }
}
