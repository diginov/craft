<?php

namespace modules\site\services;

use modules\site\helpers\StringHelper;

use Craft;
use craft\elements\Entry;
use craft\helpers\Template;

use Spatie\SchemaOrg\Schema;
use Twig\Markup;
use vaersaagod\seomate\SEOMate;

/**
 * @property-read Markup $breadcrumb
 * @property-read Markup $webpage
 * @property-read Markup $website
 */
class MetaService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Returns breadcrumb microdata.
     *
     * @return Markup
     */
    public function getBreadcrumb(): Markup
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $element = Craft::$app->getUrlManager()->getMatchedElement();

        $items = [];
        $items[] = Schema::listItem()
            ->name($site->name)
            ->url($site->getBaseUrl())
            ->position(1);

        $position = 2;

        if (!$element) {
            $request = Craft::$app->getRequest();
            $segments = $request->getSegments();

            $items[] = Schema::listItem()
                ->name(StringHelper::titleize((string) end($segments)))
                ->url($request->getAbsoluteUrl())
                ->position($position);
        } else if (!$element->getIsHomepage()) {
            foreach($element->getAncestors()->all() as $ancestor) {
                $items[] = Schema::listItem()
                    ->name($ancestor->title)
                    ->url($ancestor->getUrl())
                    ->position($position);

                $position++;
            }

            $items[] = Schema::listItem()
                ->name($element->title)
                ->url($element->getUrl())
                ->position($position);
        }

        $schema = Schema::breadcrumbList()->itemListElement($items);

        return Template::raw($schema->toScript());
    }

    /**
     * Returns webpage microdata.
     *
     * @return Markup
     */
    public function getWebpage(): Markup
    {
        $context = Craft::$app->getView()->getTwig()->getGlobals();
        $element = Craft::$app->getUrlManager()->getMatchedElement();
        $meta = SEOMate::getInstance()->meta->getContextMeta($context);

        $schema = Schema::webPage()
            ->name($meta['title'])
            ->headline($meta['title'])
            ->description($meta['description'])
            ->inLanguage(StringHelper::first(Craft::$app->language, 2))
            ->url(Craft::$app->getRequest()->getAbsoluteUrl());

        if (!empty($meta['image'])) {
            $schema->image(Schema::imageObject()->url($meta['image']));
        }

        if ($element) {
            $schema->dateModified($element->dateUpdated);
            $schema->datePublished($element->dateCreated);
        }

        return Template::raw($schema->toScript());
    }

    /**
     * Returns website microdata.
     *
     * @return Markup
     */
    public function getWebsite(): Markup
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $meta = Entry::find()->section('globalMeta')->one();
        $image = $meta->metaImage->one();

        $schema = Schema::webSite()
            ->name($site->name)
            ->description($meta->metaDescription)
            ->url($site->getBaseUrl());

        if (!empty($image)) {
            $schema->image(Schema::imageObject()->url($image->getUrl(['width' => 1200, 'height' => 630, 'format' => 'jpg'])));
        }

        return Template::raw($schema->toScript());
    }
}
