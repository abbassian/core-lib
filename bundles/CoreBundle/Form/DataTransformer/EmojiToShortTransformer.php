<?php

namespace Autoborna\CoreBundle\Form\DataTransformer;

use Autoborna\CoreBundle\Helper\EmojiHelper;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class EmojiToShortTransformer.
 */
class EmojiToShortTransformer implements DataTransformerInterface
{
    /**
     * Convert short to unicode.
     *
     * @param array|string $content
     *
     * @return string|array
     */
    public function transform($content)
    {
        if (is_array($content)) {
            foreach ($content as &$convert) {
                $convert = $this->transform($convert);
            }
        } else {
            $content = EmojiHelper::toEmoji($content, 'short');
        }

        return $content;
    }

    /**
     * Convert emoji to short bytes.
     *
     * @param array|string $content
     *
     * @return array|string
     */
    public function reverseTransform($content)
    {
        if (is_array($content)) {
            foreach ($content as &$convert) {
                $convert = $this->reverseTransform($convert);
            }
        } else {
            $content = EmojiHelper::toShort($content);
        }

        return $content;
    }
}
