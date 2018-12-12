<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\services;

class SaasLinkService extends \craft\base\Component
{
    // Properties
    // =========================================================================

    /**
     * @var \workingconcept\saaslink\models\Settings
     */
    public $settings;

    /**
     * The base URL used to interact with the SaaS API.
     *
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * The human-friendly name of the service.
     *
     * @var string
     */
    public $serviceName;

    /**
     * A slugified name of the service.
     *
     * @var string
     */
    public $serviceSlug;

    /**
     * Guzzle client to be configured and used for API interaction.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // populate the settings
        $this->settings = \workingconcept\saaslink\SaasLink::$plugin->getSettings();

        if ($this->isConfigured())
        {
            $this->configureClient();
        }
    }


    /**
     * Determine whether we're ready to interact with the service.
     *
     * @return boolean
     */
    public function isConfigured(): bool
    {
        return false;
    }


    /**
     * Configure Guzzle ->client to interact with the API.
     *
     * @return void
     */
    public function configureClient()
    {
        // ready $this->client !
    }


    /**
     * Get an array of label+value options that represent service Objeccts
     * that may act as link targets.
     *
     * @return array
     */
    public function getAvailableRelationshipTypes(): array
    {
        return [];
    }


    /**
     * Get an array of label+value options that represent instances of whatever
     * relationshipType object was chosen. (Things to link to.)
     *
     * @param string $relationshipType
     *
     * @return array
     */
    public function getOptions($relationshipType): array
    {
        return [];
    }

}
