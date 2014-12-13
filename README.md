## Install via Composer

Add the following dependency to your project's _composer.json_ file:

```json
{
    "require": {
        "phellow/intl": "1.*"
    }
}
```

## Usage

Create an object of _Phellow\Intl\IntlService_. With this object, you can:

- store the current used locale of your application.
- translate texts (plurals are also supported).
- format a DateTime object based on the current locale.


```php
$intl = new IntlService('en_US');
$intl->setAvailableLocales(['en_US', 'de_DE']);

// get current locale
$locale = $intl->getLocale();

// translate texts
$translator = new ArrayTranslator('path/to/translation-files');
$intl->setTranslator($translator);

$text = $intl->_('translate this');
$text = $intl->_n('one', 'more', 2);
```

To see all the possibilities, you can check out the Unit Tests under _tests/_.

## License

The MIT license.