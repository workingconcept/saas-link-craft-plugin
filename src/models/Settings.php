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
     * @var array Services that are configured and ready to use.
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
    public $harvestBaseUrl = '';

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
     * @var string Capsule's customer base URL. (Will be grabbed+populated automatically.)
     */
    public $capsuleBaseUrl = '';

    /**
     * @var string
     */
    public $fetchRelationshipTypesUri = 'saas-link/default/fetch-relationship-types';

    /**
     * @var string
     */
    public $fetchTrelloOrganizationOptionsUri = 'saas-link/default/fetch-trello-organization-options';


    // Public Methods
    // =========================================================================

    /**
     * Get an array with the class names of services this plugin supports.
     *
     * @return array
     */
    public function getSupportedServices(): array
    {
        return self::SUPPORTED_SERVICES;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[
                'harvestToken',
                'harvestAccountId',
                'trelloApiKey',
                'trelloApiToken',
                'trelloOrganizationId',
                'capsuleToken',
                'harvestBaseUrl',
                'capsuleBaseUrl',
            ], 'string'],
        ];
    }

}
