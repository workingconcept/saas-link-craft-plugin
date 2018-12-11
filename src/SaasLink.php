<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * Craft Field Types for linking Entries to Harvest, Trello, and Capsule.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink;

use workingconcept\saaslink\services\HarvestService;
use workingconcept\saaslink\services\CapsuleService;
use workingconcept\saaslink\services\TrelloService;
use workingconcept\saaslink\variables\HarvestVariable;
use workingconcept\saaslink\models\Settings;
use workingconcept\saaslink\fields\SaasLinkField;

use Craft;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class SaaS Link
 *
 * @author    Working Concept
 * @package   SaaS Link
 * @since     1.0.0
 *
 * @property  HarvestService $harvestService
 * @property  TrelloService  $trelloService
 * @property  CapsuleService $capsuleService
 */
class SaasLink extends craft\base\Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var SaasLink
     */
    public static $plugin;

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    /**
     * @var string
     */
    public $t9nCategory = 'saas-link';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'harvest' => HarvestService::class,
            'trello'  => TrelloService::class,
            'capsule' => CapsuleService::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event)
            {
                $variable = $event->sender;
                $variable->set('harvest', HarvestVariable::class);
                $variable->set('trello', TrelloVariable::class);
                $variable->set('capsule', CapsuleVariable::class);
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event)
            {
                $event->types[] = SaasLinkField::class;
            }
        );

        Craft::info(
            Craft::t(
                'saas-link',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'saas-link/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

}
