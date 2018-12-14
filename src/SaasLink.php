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
use workingconcept\saaslink\variables\TrelloVariable;
use workingconcept\saaslink\variables\CapsuleVariable;
use workingconcept\saaslink\models\Settings;
use workingconcept\saaslink\fields\SaasLinkField;

use Craft;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class SaasLink
 *
 * @author    Working Concept
 * @package   SaaS Link
 * @since     1.0.0
 *
 * @property  HarvestService $harvest
 * @property  TrelloService  $trello
 * @property  CapsuleService $capsule
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
     * @var string
     */
    public $schemaVersion = '1.0.1';

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

    /**
     * Get an array of configured and enabled services.
     *
     * @return array
     */
    public function getEnabledServices(): array
    {
        $enabled = [];

        foreach ($this->getSettings()->enabledServices as $service)
        {
            $instance = new $service;

            if ($instance->isConfigured())
            {
                $enabled[] = $instance;
            }
        }

        return $enabled;
    }

    /**
     * Fetch and save base account URLs if they're not already set.
     *
     * @return bool
     */
    public function beforeSaveSettings(): bool
    {
        $enabled = [];

        foreach (Settings::SUPPORTED_SERVICES as $service)
        {
            $instance = new $service;

            if ($instance->isConfigured())
            {
                $enabled[] = $service;
            }
        }

        $this->getSettings()->enabledServices = $enabled;

        if ($this->capsule->isConfigured() && empty($this->getSettings()->capsuleBaseUrl))
        {
            // have the service update the URL so we can save it
            $this->capsule->setCapsuleBaseUrlSetting();
        }

        if ($this->harvest->isConfigured() && empty($this->getSettings()->harvestBaseUrl))
        {
            // have the service update the URL so we can save it
            $this->harvest->setHarvestBaseUrlSetting();
        }

        return true;
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
        $organizationOptions = [];

        foreach($this->trello->getMemberOrganizations() as $organization)
        {
            $organizationOptions[] = [
                'label' => $organization->displayName,
                'value' => $organization->id,
            ];
        }

        return Craft::$app->view->renderTemplate(
            'saas-link/settings',
            [
                'organizationOptions' => $organizationOptions,
                'settings' => $this->getSettings()
            ]
        );
    }

}
