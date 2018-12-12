<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\services;

use workingconcept\saaslink\models\capsule\CapsuleOpportunity;
use workingconcept\saaslink\SaasLink;
use workingconcept\saaslink\models\capsule\CapsuleParty;
use GuzzleHttp\Psr7;
use Craft;

class CapsuleService extends SaasLinkService
{
    // Constants
    // =========================================================================

    const CACHE_ENABLED = true;
    const CACHE_SECONDS = 60;


    // Properties
    // =========================================================================

    /**
     * @var string
     */
    protected $apiBaseUrl = 'https://api.capsulecrm.com/api/v2/';

    /**
     * @var string
     */
    public $serviceName = 'Capsule';

    /**
     * @var string
     */
    public $serviceSlug = 'capsule';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        return ! empty($this->settings->capsuleToken);
    }

    /**
     * @inheritdoc
     */
    public function configureClient()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->settings->capsuleToken,
                'Accept'        => 'application/json',
                'User-Agent'    => 'Craft CMS',
            ],
            'verify' => false,
            'debug' => false
        ]);

        if (empty($this->settings->capsuleBaseUrl))
        {
            $this->setCapsuleBaseUrlSetting();
        }
    }

    /**
     * Save a reference to the customer-facing base Capsule URL so we don't have to keep looking it up.
     */
    public function setCapsuleBaseUrlSetting()
    {
        $this->settings->capsuleBaseUrl = $this->getSite()->url;

        // let the base plugin class worry about *saving* the settings model
        // Craft::$app->plugins->savePluginSettings(SaasLink::$plugin, $this->settings->toArray());
    }

    /**
     * @inheritdoc
     */
    public function getAvailableRelationshipTypes(): array
    {
        return [
            [
                'label' => Craft::t('saas-link', 'Opportunity'),
                'value' => 'opportunity'
            ],
            [
                'label' => Craft::t('saas-link', 'Organization'),
                'value' => 'organization'
            ],
            [
                'label' => Craft::t('saas-link', 'Person'),
                'value' => 'person'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions($relationshipType): array
    {
        $options = [];

        if ($relationshipType === 'opportunity')
        {
            $opportunities = $this->getOpportunities();

            // first sort by updatedAt
            usort($opportunities, function($a, $b) {
                return $b->updatedAt <=> $a->updatedAt;
            });

            foreach ($opportunities as $opportunity)
            {
                $label = $opportunity->name;

                if (isset($opportunity->party->name))
                {
                    $label = $opportunity->name . ' (' . $opportunity->party->name . ')';
                }

                $options[] = [
                    'label'   => $label,
                    'value'   => (string)$opportunity->id,
                    'link'    => $this->settings->capsuleBaseUrl . '/opportunity/' . $opportunity->id,
                    'default' => null
                ];
            }
        }
        elseif ($relationshipType === 'organization')
        {
            foreach ($this->getOrganizations() as $organization)
            {
                $options[] = [
                    'label'   => $organization->name,
                    'value'   => (string)$organization->id,
                    'link'    => $this->settings->capsuleBaseUrl . '/party/' . $organization->id,
                    'default' => null
                ];
            }

            // alphabetize
            usort($options, function($a, $b) {
                return strtolower($a['label']) <=> strtolower($b['label']);
            });
        }
        elseif ($relationshipType === 'person')
        {
            foreach (SaasLink::$plugin->capsule->getPeople() as $person)
            {
                $options[] = [
                    'label'   => $person->firstName . ' ' . $person->lastName,
                    'value'   => (string)$person->id,
                    'link'    => $this->settings->capsuleBaseUrl . '/party/' . $person->id,
                    'default' => null
                ];
            }

            // alphabetize
            usort($options, function($a, $b) {
                return strtolower($a['label']) <=> strtolower($b['label']);
            });
        }

        return $options;
    }

    /**
     * Get available Capsule parties.
     *
     * @return CapsuleParty[]
     */
    public function getParties(): array
    {
        $parties = [];
        $result  = $this->collectPaginatedResults('parties');

        foreach ($result as $partyData)
        {
            $parties[] = new CapsuleParty($partyData);
        }

        return $parties;
    }

    /**
     * Get available Capsule humans.
     *
     * @return CapsuleParty[]
     */
    public function getPeople(): array
    {
        $people = [];

        foreach ($this->getParties() as $party)
        {
            if ($party->type === CapsuleParty::TYPE_PERSON)
            {
                $people[] = $party;
            }
        }

        return $people;
    }

    /**
     * Get available Capsule organizations.
     *
     * @return CapsuleParty[]
     */
    public function getOrganizations(): array
    {
        $organizations = [];

        foreach ($this->getParties() as $party)
        {
            if ($party->type === CapsuleParty::TYPE_ORGANISATION)
            {
                $organizations[] = $party;
            }
        }

        return $organizations;
    }

    /**
     * Get available Capsule opportunities.
     *
     * @return CapsuleOpportunity[]
     */
    public function getOpportunities(): array
    {
        $opportunities = [];
        $result = $this->collectPaginatedResults('opportunities');

        foreach ($result as $opportunityData)
        {
            $opportunities[] = new CapsuleOpportunity($opportunityData);
        }

        return $opportunities;
    }

    /**
     * Get Capsule site details.
     *
     * @return object
     */
    public function getSite()
    {
        if ($cachedResponse = Craft::$app->cache->get('capsule_site'))
        {
            return $cachedResponse;
        }

        $response = $this->client->get('site');
        $responseData = json_decode($response->getBody(true))->site;

        Craft::$app->cache->set('capsule_site', $responseData, 3600);

        return $responseData;
    }


    // Private Methods
    // =========================================================================

    private function collectPaginatedResults($endpoint, $property = '')
    {
        $cacheKey = 'capsule_' . $endpoint;

        if (strpos($endpoint, '?') !== false)
        {
            $endpointPieces = explode('?', $endpoint);
            $endpointWithoutParameters = $endpointPieces[0];
            $endpoint .= '&page=';
        }
        else
        {
            $endpointWithoutParameters = $endpoint;
            $endpoint .= '?page=';
        }

        if ($property === '')
        {
            $property = $endpointWithoutParameters;
        }

        if (self::CACHE_ENABLED)
        {
            if ($cachedRecords = Craft::$app->cache->get($cacheKey))
            {
                return $cachedRecords;
            }
        }

        $fetchedAllRecords = false;
        $records           = [];
        $page              = 1;

        while ( ! $fetchedAllRecords)
        {
            $response = $this->client->get($endpoint . $page);
            $responseData = json_decode($response->getBody(true));

            $records = array_merge($records, $responseData->{$property});

            if ($response->hasHeader('Link'))
            {
                $parsed = Psr7\parse_header($response->getHeader('Link'));

                if ( ! empty($parsed[0]['rel']) && $parsed[0]['rel'] === 'next')
                {
                    $page++;
                    continue;
                }
            }

            $fetchedAllRecords = true;
        }

        if (self::CACHE_ENABLED)
        {
            Craft::$app->cache->set($cacheKey, $records, self::CACHE_SECONDS);
        }

        return $records;
    }

}
