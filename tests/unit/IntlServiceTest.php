<?php
namespace Phellow\Intl;

/**
 * @covers \Phellow\Intl\IntlService
 */
class IntlServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testDefaultLocale()
    {
        $intl = new IntlService('de_DE');
        $this->assertEquals('de_DE', $intl->getDefaultLocale());

        $intl->setDefaultLocale('en');
        $this->assertEquals('en', $intl->getDefaultLocale());
    }

    /**
     *
     */
    public function testAvailableLocales()
    {
        $locales = ['de_DE', 'en_US'];

        $intl = new IntlService('de', $locales);
        $this->assertEquals($locales, $intl->getAvailableLocales());

        $locales = ['de', 'en'];
        $intl->setAvailableLocales($locales);
        $this->assertEquals($locales, $intl->getAvailableLocales());
    }

    /**
     *
     */
    public function testLocale()
    {
        $intl = new IntlService('de', ['de_DE', 'en_US']);

        $this->assertEquals('de', $intl->getLocale());

        $intl->setLocale('en');
        $this->assertEquals('de', $intl->getLocale());

        $intl->setLocale('de_DE');
        $this->assertEquals('de_DE', $intl->getLocale());

        $intl->setLocale('en', true);
        $this->assertEquals('en', $intl->getLocale());
    }

    /**
     *
     */
    public function testTranslateReplaceRegex()
    {
        $intl = new IntlService('de');

        $intl->setTranslateReplaceRegex('$key');
        $this->assertEquals('$key', $intl->getTranslateReplaceRegex());
    }

    /**
     *
     */
    public function testTranslate()
    {
        $intl = new IntlService('de_DE');

        $text = 'This is a test';
        $translator = $this->getMockForAbstractClass('Phellow\Intl\TranslatorInterface');
        $translator->expects($this->once())
            ->method('translate')
            ->with($this->equalTo($text), $this->equalTo('de_DE'))
            ->willReturn('translated');

        $intl->setTranslator($translator);
        $this->assertEquals($translator, $intl->getTranslator());

        $this->assertEquals('translated', $intl->_($text));
    }

    /**
     *
     */
    public function testTranslateWithReplacements()
    {
        $intl = new IntlService('de_DE');

        $text = 'My name is ${name}';
        $replacements = ['name' => 'Christian'];
        $translator = $this->getMockForAbstractClass('Phellow\Intl\TranslatorInterface');
        $translator->expects($this->once())
            ->method('translate')
            ->with($this->equalTo($text), $this->equalTo('en_US'))
            ->willReturn('some result');

        $intl->setTranslator($translator);
        $this->assertEquals('some result', $intl->_($text, $replacements, 'en_US'));
    }

    /**
     *
     */
    public function testTranslateWithoutAdapter()
    {
        $intl = new IntlService('de_DE');

        $text = 'This is a test';
        $this->assertEquals($text, $intl->_($text));

        $text = 'My name is ${name}';
        $replacements = ['name' => 'Christian'];

        $this->assertEquals('My name is Christian', $intl->_($text, $replacements, 'en_US'));
    }

    /**
     *
     */
    public function testTranslatePlurals()
    {
        $intl = new IntlService('de_DE');

        $translator = $this->getMockForAbstractClass('Phellow\Intl\TranslatorInterface');
        $translator->expects($this->once())
            ->method('translatePlurals')
            ->with($this->equalTo('one'), $this->equalTo('more'), 3, $this->equalTo('de_DE'))
            ->willReturn('translated');

        $intl->setTranslator($translator);

        $this->assertEquals('translated', $intl->_n('one', 'more', 3));
    }

    /**
     *
     */
    public function testTranslatePluralsWithoutAdapter()
    {
        $intl = new IntlService('de_DE');
        $this->assertEquals('one', $intl->_n('one', 'more', 1));
        $this->assertEquals('more', $intl->_n('one', 'more', 3));
    }

    /**
     *
     */
    public function testFormatDateTime()
    {
        $intl = new IntlService('de_DE');

        $dt = new \DateTime('1987-06-18', new \DateTimeZone('Europe/Berlin'));
        $this->assertEquals('18. Juni 1987', $intl->formatDateTime($dt, 'dd. MMMM yyyy'));

        $intl->setLocale('en_US', true);
        $format = [\IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT];
        $this->assertEquals('Jun 18, 1987 12:00 AM', $intl->formatDateTime($dt, $format));
    }
}
