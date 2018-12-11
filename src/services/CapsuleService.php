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

    const CACHE_ENABLED = false;
    const CACHE_SECONDS = 15;


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
    public function isConfigured()
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
    }

    /**
     * @inheritdoc
     */
    public function getAvailableRelationshipTypes()
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
    public function getOptions($relationshipType)
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
                    'link'    => SaasLink::$plugin->settings->capsuleBaseUrl . '/opportunity/' . $opportunity->id,
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
                    'link'    => SaasLink::$plugin->settings->capsuleBaseUrl . '/party/' . $organization->id,
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
                    'link'    => SaasLink::$plugin->settings->capsuleBaseUrl . '/party/' . $person->id,
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
    public function getParties()
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
     * @return mixed
     */
    public function getPeople()
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
     * @return mixed
     */
    public function getOrganizations()
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
     * @return mixed
     */
    public function getOpportunities()
    {
        $opportunities = [];
        $result  = $this->collectPaginatedResults('opportunities');

        foreach ($result as $opportunityData)
        {
            $opportunities[] = new CapsuleOpportunity($opportunityData);
        }

        return $opportunities;
    }

    /**
     * Get Capsule site details.
     *
     * @return mixed
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
