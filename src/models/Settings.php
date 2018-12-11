<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\models;

use workingconcept\saaslink\SaasLink;
use craft\base\Model;

class Settings extends Model
{
    // Constants
    // =========================================================================

    const SUPPORTED_SERVICES = [
        \workingconcept\saaslink\services\CapsuleService::class,
        \workingconcept\saaslink\services\HarvestService::class,
        \workingconcept\saaslink\services\TrelloService::class,
    ];


    // Properties
    // =========================================================================

    /**
     * @var array List of services that have been configured.
     */
    public $enabledServices = [];

    /**
     * @var string Harvest Personal Access Token.
     */
    public $harvestToken = '';

    /**
     * @var string Relevant Harvest account ID.
     */
    public $harvestAccountId = '';

    /**
     * @var string Harvest's customer base URL. (Will be grabbed+populated automatically.)
     */
    private $_harvestBaseUrl = '';

    /**
     * @var string
     */
    public $trelloApiKey = '';

    /**
     * @var string
     */
    public $trelloApiToken = '';

    /**
     * @var string
     */
    public $trelloOrganizationId = '';

    /**
     * @var string
     */
    public $capsuleToken = '';

    /**
     * @var string
     */
    private $_capsuleBaseUrl = '';

    /**
     * @var string
     */
    public $fetchRelationshipTypesUri = 'saas-link/default/fetch-relationship-types';


    // Public Methods
    // =========================================================================

    /**
     * Get the base URL for Capsule. Fetch it first if we don't already have it.
     * @return string
     */
    public function getCapsuleBaseUrl()
    {
        if (empty($this->_capsuleBaseUrl))
        {
            $this->updateBaseUrl('capsule');
        }

        return $this->_capsuleBaseUrl;
    }

    /**
     * Set the Capsule base URL.
     * @return string
     */
    public function setCapsuleBaseUrl($url)
    {
        return $this->_capsuleBaseUrl = $url;
    }

    /**
     * Get the base URL for Harvest. Fetch it first if we don't already have it.
     * @return string
     */
    public function getHarvestBaseUrl()
    {
        if (empty($this->_harvestBaseUrl))
        {
            $this->updateBaseUrl('harvest');
        }

        return $this->_harvestBaseUrl;
    }

    /**
     * Set the Harvest base URL.
     * @return string
     */
    public function setHarvestBaseUrl($url)
    {
        return $this->_harvestBaseUrl = $url;
    }

    /**
     * Get an array with the class names of services this plugin supports.
     *
     * @return array
     */
    public function getSupportedServices()
    {
        return self::SUPPORTED_SERVICES;
    }

    /**
     * Get an array of service instances that are configured and ready to poke at.
     *
     * @return array
     */
    public function getEnabledServices()
    {
        foreach (self::SUPPORTED_SERVICES as $service)
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
     * @inheritdoc
     */
     public function rules()
     {
         return [
             [['harvestToken', 'harvestAccountId', 'trelloApiKey', 'trelloApiToken', 'trelloOrganizationId', 'capsuleToken'], 'string'],
         ];
     }


    // Private Methods
    // =========================================================================

    /**
     * Fetch the base URL for the provided service so we don't have to store it as another plugin setting.
     *
     * @param string $service The slugified name of the relevant service.
     */
    private function updateBaseUrl($service)
    {
        if ($service === 'harvest')
        {
            $this->setHarvestBaseUrl(
                SaasLink::$plugin->harvest->getCompany()->base_uri
            );
        }
        elseif ($service === 'capsule')
        {
            $this->setCapsuleBaseUrl(
                SaasLink::$plugin->capsule->getSite()->url
            );
        }
    }

}
