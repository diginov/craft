<?php

namespace modules\site;

use modules\site\base\ModuleTrait;
use modules\site\behaviors\UserBehavior;
use modules\site\bundles\site\SiteBundle;
use modules\site\web\SiteVariable;
use modules\site\web\TwigExtension;
use modules\site\widgets\BuildWidget;

use Craft;
use craft\elements\Entry;
use craft\elements\User;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\i18n\Formatter;
use craft\services\Dashboard;
use craft\services\Gql;
use craft\services\Plugins;
use craft\services\Sections;
use craft\web\Application;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;

use yii\base\Event;

class Site extends \yii\base\Module
{
    // Traits
    // =========================================================================

    use ModuleTrait;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        Craft::setAlias('@modules/site', __DIR__);

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\site\console\controllers';
        }

        Craft::$container->set(Formatter::class, function($container, $params, $config) {
            $config['dateTimeFormats']['short']['date'] = 'yyyy-MM-dd';
            $config['dateTimeFormats']['short']['datetime'] = 'yyyy-MM-dd HH:mm';
            return new Formatter($config);
        });

        $this->_setModuleComponents();

        Craft::$app->on(Application::EVENT_INIT, function() {
            $this->_registerTemplateRoots();
            $this->_registerUserBehaviors();
            $this->_registerAssetBundles();
            $this->_registerTwigExtensions();
            $this->_registerWidgets();
        });

        Event::on(Plugins::class, Plugins::EVENT_AFTER_LOAD_PLUGINS, function() {
            $this->_registerUrlRules();
            $this->_registerElementEvents();
            $this->_registerGqlEvents();
        });
    }

    // Private Methods
    // =========================================================================

    /**
     * Register url rules.
     */
    private function _registerUrlRules(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $rules = [
                'test' => 'site/test/index',
            ];

            $event->rules = array_merge($event->rules, $rules);
        });
    }

    /**
     * Register template roots.
     */
    private function _registerTemplateRoots(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $event->roots[ $this->id ] = $baseDir;
            }
        });
    }

    /**
     * Register user behaviors.
     */
    private function _registerUserBehaviors(): void
    {
        // Attach user behaviors
        Craft::$app->getUser()->attachBehaviors([
            UserBehavior::class,
        ]);

        Event::on(User::class, User::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->sender->attachBehaviors([
                UserBehavior::class,
            ]);
        });
    }

    /**
     * Register element events.
     */
    private function _registerElementEvents(): void
    {
        // Before saving a section
        Event::on(Sections::class, Sections::EVENT_BEFORE_SAVE_SECTION, [$this->getSection(), 'beforeSaveSectionHandler']);

        // Register entry sources
        Event::on(Entry::class, Entry::EVENT_REGISTER_SOURCES, [$this->getEntry(), 'registerSourcesHandler']);

        // Before saving an entry
        Event::on(Entry::class, Entry::EVENT_BEFORE_SAVE, [$this->getEntry(), 'beforeSaveHandler']);

        // Register user sources
        Event::on(User::class, User::EVENT_REGISTER_SOURCES, [$this->getUser(), 'registerSourcesHandler']);

        // Before saving a user
        Event::on(User::class, User::EVENT_BEFORE_SAVE, [$this->getUser(), 'beforeSaveHandler']);
    }

    /**
     * Register gql events.
     */
    private function _registerGqlEvents()
    {
        // Register schema components
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS, [$this->getGql(), 'registerGqlSchemaComponentsHandler']);

        // Register types
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_TYPES, [$this->getGql(), 'registerGqlTypesHandler']);

        // Register queries
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_QUERIES, [$this->getGql(), 'registerGqlQueriesHandler']);

        // Register mutations
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_MUTATIONS, [$this->getGql(), 'registerGqlMutationsHandler']);
    }

    /**
     * Register asset bundles.
     */
    private function _registerAssetBundles(): void
    {
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        Craft::$app->getView()->hook('cp.layouts.base', function(array &$context) {
            if (Craft::$app->getUser()->getIsAdmin()) {
                $context['bodyAttributes']['class'][] = 'user-is-admin';
            }

            if (Craft::$app->getUser()->getIsManager()) {
                $context['bodyAttributes']['class'][] = 'user-is-manager';
            }
        });

        Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE, function(TemplateEvent $event) {
            Craft::$app->getView()->registerAssetBundle(SiteBundle::class);
        });
    }

    /**
     * Register twig extensions.
     */
    private function _registerTwigExtensions(): void
    {
        Craft::$app->getView()->registerTwigExtension(new TwigExtension);

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;

            $variable->attachBehaviors([
                SiteVariable::class,
            ]);
        });
    }

    /**
     * Register widgets.
     */
    private function _registerWidgets(): void
    {
        Event::on(Dashboard::class, Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = BuildWidget::class;
        });
    }
}
