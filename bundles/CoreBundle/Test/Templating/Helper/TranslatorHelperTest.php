<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Test\Templating\Helper;

use Autoborna\CoreBundle\Templating\Helper\TranslatorHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class TranslatorHelperTest extends TestCase
{
    public function testGetJsLangBasedOnLocale(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->method('setLocale')->willReturnCallback(
            fn ($locale) => $translator->method('getLocale')->willReturn($locale)
        );
        $translator->method('getCatalogue')->willReturnCallback(
            fn () => new TranslatorCatalogue($translator)
        );

        $translatorHelper = new TranslatorHelper($translator);
        $jsLang           = json_decode($translatorHelper->getJsLang(), true);
        $this->assertArrayHasKey('autoborna.custom.string', $jsLang);
        $this->assertEquals('en_US string', $jsLang['autoborna.custom.string']);

        $translator->setLocale('fr_FR');
        $translatorHelper = new TranslatorHelper($translator);
        $jsLang           = json_decode($translatorHelper->getJsLang(), true);
        $this->assertArrayHasKey('autoborna.custom.string', $jsLang);
        $this->assertEquals('fr_FR string', $jsLang['autoborna.custom.string']);
    }
}

class TranslatorCatalogue
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string[]
     */
    public function all()
    {
        switch ($this->translator->getLocale()) {
            case 'fr_FR':
                return ['autoborna.custom.string' => 'fr_FR string'];
            case 'en_US':
            default:
                return ['autoborna.custom.string' => 'en_US string'];
        }
    }
}
