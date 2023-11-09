<?php

namespace modules\site\helpers;

class ElementHelper extends \craft\helpers\ElementHelper
{
    // Public Methods
    // =========================================================================

    /**
     * @inerhitdoc
     */
    public static function generateSlug(string $str, ?bool $ascii = null, ?string $language = null): string
    {
        $str = StringHelper::replaceAll($str, ["‘", "’", "′", "ˊ", "`", "‵", "ˋ", "ˈ", "ꞌ"], "'");
        $str = StringHelper::replaceAll($str, [" d'", " l'", " m'", " s'", " t'"], ' ', false);

        return parent::generateSlug($str, true, $language);
    }
}
