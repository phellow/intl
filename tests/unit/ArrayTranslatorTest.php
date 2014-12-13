<?php
namespace Phellow\Intl;

/**
 * @covers \Phellow\Intl\ArrayTranslator
 */
class ArrayTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayTranslator
     */
    private $translator;

    /**
     *
     */
    protected function setUp()
    {
        $this->translator = new ArrayTranslator(__DIR__ . '/fixtures');
    }

    /**
     *
     */
    public function testTranslate()
    {
        $text = $this->translator->translate('someKey', 'de_DE');
        $this->assertEquals('some translation', $text);

        $text = $this->translator->translate('someKeyWithPlural', 'de_DE');
        $this->assertEquals('singular translation', $text);
    }

    /**
     *
     */
    public function testTranslatePlurals()
    {
        $text = $this->translator->translatePlurals('someKey', 'someKey', 1, 'de_DE');
        $this->assertEquals('some translation', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 1, 'de_DE');
        $this->assertEquals('singular translation', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 2, 'de_DE');
        $this->assertEquals('plural translation', $text);

        $text = $this->translator->translatePlurals('one', 'more', 1, 'de_DE');
        $this->assertEquals('one', $text);

        $text = $this->translator->translatePlurals('one', 'more', 3, 'de_DE');
        $this->assertEquals('more', $text);
    }

    /**
     *
     */
    public function testLocaleFileNotExists()
    {
        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 1, 'en');
        $this->assertEquals('someKeyWithPlural', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 2, 'en');
        $this->assertEquals('someKeyWithPlural2', $text);
    }

    /**
     *
     */
    public function testLocaleFileInvalid()
    {
        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 1, 'invalid');
        $this->assertEquals('someKeyWithPlural', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 2, 'invalid');
        $this->assertEquals('someKeyWithPlural2', $text);
    }

    /**
     *
     */
    public function testLocaleFileNoPluralForm()
    {
        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 1, 'noPluralForm');
        $this->assertEquals('singular translation', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 2, 'noPluralForm');
        $this->assertEquals('plural translation', $text);
    }

    /**
     *
     */
    public function testLocaleFileInvalidPluralForm()
    {
        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 1, 'invalidPluralForm');
        $this->assertEquals('singular translation', $text);

        $text = $this->translator->translatePlurals('someKeyWithPlural', 'someKeyWithPlural2', 2, 'invalidPluralForm');
        $this->assertEquals('singular translation', $text);
    }
}
