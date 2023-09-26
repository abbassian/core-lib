<?php

namespace Autoborna\EmailBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Autoborna\CoreBundle\Form\Type\SlotButtonType;
use Autoborna\CoreBundle\Form\Type\SlotCodeModeType;
use Autoborna\CoreBundle\Form\Type\SlotDynamicContentType;
use Autoborna\CoreBundle\Form\Type\SlotImageCaptionType;
use Autoborna\CoreBundle\Form\Type\SlotImageCardType;
use Autoborna\CoreBundle\Form\Type\SlotSeparatorType;
use Autoborna\CoreBundle\Form\Type\SlotSocialFollowType;
use Autoborna\CoreBundle\Form\Type\SlotTextType;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\EmojiHelper;
use Autoborna\EmailBundle\EmailEvents;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Event\EmailBuilderEvent;
use Autoborna\EmailBundle\Event\EmailSendEvent;
use Autoborna\EmailBundle\Model\EmailModel;
use Autoborna\PageBundle\Entity\Redirect;
use Autoborna\PageBundle\Entity\Trackable;
use Autoborna\PageBundle\Model\RedirectModel;
use Autoborna\PageBundle\Model\TrackableModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BuilderSubscriber implements EventSubscriberInterface
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * @var TrackableModel
     */
    private $pageTrackableModel;

    /**
     * @var RedirectModel
     */
    private $pageRedirectModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        CoreParametersHelper $coreParametersHelper,
        EmailModel $emailModel,
        TrackableModel $trackableModel,
        RedirectModel $redirectModel,
        TranslatorInterface $translator,
        EntityManager $entityManager
    ) {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->emailModel           = $emailModel;
        $this->pageTrackableModel   = $trackableModel;
        $this->pageRedirectModel    = $redirectModel;
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_BUILD => ['onEmailBuild', 0],
            EmailEvents::EMAIL_ON_SEND  => [
                ['fixEmailAccessibility', 0],
                ['onEmailGenerate', 0],
                // Ensure this is done last in order to catch all tokenized URLs
                ['convertUrlsToTokens', -9999],
            ],
            EmailEvents::EMAIL_ON_DISPLAY => [
                ['fixEmailAccessibility', 0],
                ['onEmailGenerate', 0],
                // Ensure this is done last in order to catch all tokenized URLs
                ['convertUrlsToTokens', -9999],
            ],
        ];
    }

    public function onEmailBuild(EmailBuilderEvent $event)
    {
        if ($event->abTestWinnerCriteriaRequested()) {
            //add AB Test Winner Criteria
            $openRate = [
                'group'    => 'autoborna.email.stats',
                'label'    => 'autoborna.email.abtest.criteria.open',
                'event'    => EmailEvents::ON_DETERMINE_OPEN_RATE_WINNER,
            ];
            $event->addAbTestWinnerCriteria('email.openrate', $openRate);

            $clickThrough = [
                'group'    => 'autoborna.email.stats',
                'label'    => 'autoborna.email.abtest.criteria.clickthrough',
                'event'    => EmailEvents::ON_DETERMINE_CLICKTHROUGH_RATE_WINNER,
            ];
            $event->addAbTestWinnerCriteria('email.clickthrough', $clickThrough);
        }

        $tokens = [
            '{unsubscribe_text}' => $this->translator->trans('autoborna.email.token.unsubscribe_text'),
            '{webview_text}'     => $this->translator->trans('autoborna.email.token.webview_text'),
            '{signature}'        => $this->translator->trans('autoborna.email.token.signature'),
            '{subject}'          => $this->translator->trans('autoborna.email.subject'),
        ];

        if ($event->tokensRequested(array_keys($tokens))) {
            $event->addTokens(
                $event->filterTokens($tokens)
            );
        }

        // these should not allow visual tokens
        $tokens = [
            '{unsubscribe_url}' => $this->translator->trans('autoborna.email.token.unsubscribe_url'),
            '{webview_url}'     => $this->translator->trans('autoborna.email.token.webview_url'),
        ];
        if ($event->tokensRequested(array_keys($tokens))) {
            $event->addTokens(
                $event->filterTokens($tokens)
            );
        }

        if ($event->slotTypesRequested()) {
            $event->addSlotType(
                'text',
                $this->translator->trans('autoborna.core.slot.label.text'),
                'font',
                'AutobornaCoreBundle:Slots:text.html.php',
                SlotTextType::class,
                1000
            );
            $event->addSlotType(
                'image',
                $this->translator->trans('autoborna.core.slot.label.image'),
                'image',
                'AutobornaCoreBundle:Slots:image.html.php',
                SlotImageCardType::class,
                900
            );
            $event->addSlotType(
                'imagecard',
                $this->translator->trans('autoborna.core.slot.label.imagecard'),
                'id-card-o',
                'AutobornaCoreBundle:Slots:imagecard.html.php',
                SlotImageCardType::class,
                870
            );
            $event->addSlotType(
                'imagecaption',
                $this->translator->trans('autoborna.core.slot.label.imagecaption'),
                'image',
                'AutobornaCoreBundle:Slots:imagecaption.html.php',
                SlotImageCaptionType::class,
                850
            );
            $event->addSlotType(
                'button',
                $this->translator->trans('autoborna.core.slot.label.button'),
                'external-link',
                'AutobornaCoreBundle:Slots:button.html.php',
                SlotButtonType::class,
                800
            );
            $event->addSlotType(
                'socialfollow',
                $this->translator->trans('autoborna.core.slot.label.socialfollow'),
                'twitter',
                'AutobornaCoreBundle:Slots:socialfollow.html.php',
                SlotSocialFollowType::class,
                600
            );
            $event->addSlotType(
                'codemode',
                $this->translator->trans('autoborna.core.slot.label.codemode'),
                'code',
                'AutobornaCoreBundle:Slots:codemode.html.php',
                SlotCodeModeType::class,
                500
            );
            $event->addSlotType(
                'separator',
                $this->translator->trans('autoborna.core.slot.label.separator'),
                'minus',
                'AutobornaCoreBundle:Slots:separator.html.php',
                SlotSeparatorType::class,
                400
            );

            $event->addSlotType(
                'dynamicContent',
                $this->translator->trans('autoborna.core.slot.label.dynamiccontent'),
                'tag',
                'AutobornaCoreBundle:Slots:dynamiccontent.html.php',
                SlotDynamicContentType::class,
                300
            );
        }

        if ($event->sectionsRequested()) {
            $event->addSection(
                'one-column',
                $this->translator->trans('autoborna.core.slot.label.onecolumn'),
                'file-text-o',
                'AutobornaCoreBundle:Sections:one-column.html.php',
                null,
                1000
            );
            $event->addSection(
                'two-column',
                $this->translator->trans('autoborna.core.slot.label.twocolumns'),
                'columns',
                'AutobornaCoreBundle:Sections:two-column.html.php',
                null,
                900
            );
            $event->addSection(
                'three-column',
                $this->translator->trans('autoborna.core.slot.label.threecolumns'),
                'th',
                'AutobornaCoreBundle:Sections:three-column.html.php',
                null,
                800
            );
        }
    }

    public function fixEmailAccessibility(EmailSendEvent $event): void
    {
        if ($event->isDynamicContentParsing() || !$event->getEmail() instanceof Email) {
            // prevent a loop
            return;
        }

        $content = $event->getContent();
        $subject = $event->getEmail()->getSubject();

        // Add the empty <head/> tag if it's missing.
        if (empty(preg_match('#<\s*?head\b[^>]*>(.*?)</head\b[^>]*>#s', $content, $matches))) {
            $content = str_replace('<body', '<head></head><body', $content);
        }

        // Add the <title/> tag with email subject value into the <head/> tag if it's missing.
        $content = preg_replace_callback(
            "/<title>(.*?)<\/title>/is",
            fn ($matches) => empty(trim($matches[1])) ? "<title>{$subject}</title>" : $matches[0],
            $content,
            -1,
            $fixed
        );

        if (!$fixed) {
            $content = str_replace('</head>', "<title>{$subject}</title></head>", $content);
        }

        // Add the lang attribute to the <html/> tag if it's missing.
        $locale = empty($event->getEmail()->getLanguage()) ? $this->coreParametersHelper->get('locale') : $event->getEmail()->getLanguage();
        preg_match_all("~<html.*lang\s*=\s*[\"']([^\"']+)[\"'][^>]*>~i", $content, $matches);
        if (empty($matches[1])) {
            $content = str_replace('<html', '<html lang="'.$locale.'"', $content);
        }

        $event->setContent($content);
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        $idHash = $event->getIdHash();
        $lead   = $event->getLead();
        $email  = $event->getEmail();

        if (null == $idHash) {
            // Generate a bogus idHash to prevent errors for routes that may include it
            $idHash = uniqid();
        }

        $unsubscribeText = $this->coreParametersHelper->get('unsubscribe_text');
        if (!$unsubscribeText) {
            $unsubscribeText = $this->translator->trans('autoborna.email.unsubscribe.text', ['%link%' => '|URL|']);
        }
        $unsubscribeText = str_replace('|URL|', $this->emailModel->buildUrl('autoborna_email_unsubscribe', ['idHash' => $idHash]), $unsubscribeText);
        $event->addToken('{unsubscribe_text}', EmojiHelper::toHtml($unsubscribeText));

        $event->addToken('{unsubscribe_url}', $this->emailModel->buildUrl('autoborna_email_unsubscribe', ['idHash' => $idHash]));

        $webviewText = $this->coreParametersHelper->get('webview_text');
        if (!$webviewText) {
            $webviewText = $this->translator->trans('autoborna.email.webview.text', ['%link%' => '|URL|']);
        }
        $webviewText = str_replace('|URL|', $this->emailModel->buildUrl('autoborna_email_webview', ['idHash' => $idHash]), $webviewText);
        $event->addToken('{webview_text}', EmojiHelper::toHtml($webviewText));

        // Show public email preview if the lead is not known to prevent 404
        if (empty($lead['id']) && $email) {
            $event->addToken('{webview_url}', $this->emailModel->buildUrl('autoborna_email_preview', ['objectId' => $email->getId()]));
        } else {
            $event->addToken('{webview_url}', $this->emailModel->buildUrl('autoborna_email_webview', ['idHash' => $idHash]));
        }

        $signatureText = $this->coreParametersHelper->get('default_signature_text');
        $fromName      = $this->coreParametersHelper->get('mailer_from_name');
        $signatureText = str_replace('|FROM_NAME|', $fromName, nl2br($signatureText));
        $event->addToken('{signature}', EmojiHelper::toHtml($signatureText));

        $event->addToken('{subject}', EmojiHelper::toHtml($event->getSubject()));
    }

    /**
     * @return array
     */
    public function convertUrlsToTokens(EmailSendEvent $event)
    {
        if ($event->isInternalSend() || $this->coreParametersHelper->get('disable_trackable_urls')) {
            // Don't convert urls
            return;
        }

        $email   = $event->getEmail();
        $emailId = ($email) ? $email->getId() : null;
        if (!$email instanceof Email) {
            $email = $this->emailModel->getEntity($emailId);
        }

        $utmTags      = $email->getUtmTags();
        $clickthrough = $event->generateClickthrough();
        $trackables   = $this->parseContentForUrls($event, $emailId);

        /**
         * @var string
         * @var Trackable $trackable
         */
        foreach ($trackables as $token => $trackable) {
            $url = ($trackable instanceof Trackable)
                ?
                $this->pageTrackableModel->generateTrackableUrl($trackable, $clickthrough, false, $utmTags)
                :
                $this->pageRedirectModel->generateRedirectUrl($trackable, $clickthrough, false, $utmTags);

            $event->addToken($token, $url);
        }
    }

    /**
     * Parses content for URLs and tokens.
     *
     * @param $emailId
     *
     * @return mixed
     */
    private function parseContentForUrls(EmailSendEvent $event, $emailId)
    {
        static $convertedContent = [];

        // Prevent parsing the exact same content over and over
        if (!isset($convertedContent[$event->getContentHash()])) {
            $html = $event->getContent();
            $text = $event->getPlainText();

            $contentTokens = $event->getTokens();

            [$content, $trackables] = $this->pageTrackableModel->parseContentForTrackables(
                [$html, $text],
                $contentTokens,
                ($emailId) ? 'email' : null,
                $emailId
            );

            [$html, $text] = $content;
            unset($content);

            if ($html) {
                $event->setContent($html);
            }
            if ($text) {
                $event->setPlainText($text);
            }

            $convertedContent[$event->getContentHash()] = $trackables;

            // Don't need to preserve Trackable or Redirect entities in memory
            $this->entityManager->clear(Redirect::class);
            $this->entityManager->clear(Trackable::class);

            unset($html, $text, $trackables);
        }

        return $convertedContent[$event->getContentHash()];
    }
}
