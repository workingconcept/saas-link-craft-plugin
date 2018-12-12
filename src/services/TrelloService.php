<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\services;

use workingconcept\saaslink\models\trello\TrelloBoard;
use workingconcept\saaslink\models\trello\TrelloOrganization;
use workingconcept\saaslink\SaasLink;
use Craft;

class TrelloService extends SaasLinkService
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
    protected $apiBaseUrl = 'https://api.trello.com/1/';

    /**
     * @var string
     */
    public $serviceName = 'Trello';

    /**
     * @var string
     */
    public $serviceSlug = 'trello';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        return ! empty($this->settings->trelloApiKey) && 
            ! empty($this->settings->trelloApiToken) && 
            ! empty($this->settings->trelloOrganizationId);
    }

    /**
     * @inheritdoc
     */
    public function configureClient()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->apiBaseUrl,
            'query' => [
                'key'   => $this->settings->trelloApiKey,
                'token' => $this->settings->trelloApiToken
            ],
            'headers' => [],
            'verify'  => false,
            'debug'   => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAvailableRelationshipTypes(): array
    {
        return [
            [
                'label' => Craft::t('saas-link', 'Board'),
                'value' => 'board'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions($relationshipType): array
    {
        $options = [];

        if ($relationshipType === 'board')
        {
            $boards = SaasLink::$plugin->trello->getBoards();

            foreach ($boards as $board)
            {
                $options[] = [
                    'label'   => $board->name,
                    'value'   => $board->id,
                    'link'    => $board->url,
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
     * Get boards.
     *
     * @param string $filter `all` or a comma-separated list of: `open`, `closed`, `members`, `organization`, `public`
     * @param string $fields `all` or comma-separated list of board [fields](https://developers.trello.com/reference#board-object)
     *
     * @return TrelloBoard[]
     */

    public function getBoards($filter = 'all', $fields = 'all'): array
    {
        $boards = [];
        $cacheKey = 'trello_boards_' . $filter . $fields;

        if (self::CACHE_ENABLED && $cachedResponse = Craft::$app->cache->get($cacheKey))
        {
            $responseData = $cachedResponse;
        }
        else
        {
            $response = $this->client->get(sprintf(
                'organizations/%s/boards?filter=%s&fields=%s',
                $this->settings->trelloOrganizationId,
                $filter,
                $fields
            ));

            $responseData = json_decode($response->getBody());

            if (self::CACHE_ENABLED)
            {
                Craft::$app->cache->set($cacheKey, $responseData, self::CACHE_SECONDS);
            }
        }

        foreach ($responseData as $responseItem)
        {
            $boards[] = new TrelloBoard($responseItem);
        }

        return $boards;
    }


    /**
     * Get Organizations to which the relevant human (per API credentials) belongs.
     *
     * @return TrelloOrganization[]
     */
    public function getMemberOrganizations(): array
    {
        $organizations = [];

        // be extra sure we've got credentials since this is called during plugin setup when Settings aren't populated
        if ($this->isConfigured())
        {
            $response = $this->client->get('members/me/organizations');
            $responseData = json_decode($response->getBody());

            foreach ($responseData as $responseItem)
            {
                $organizations[] = new TrelloOrganization($responseItem);
            }
        }

        return $organizations;
    }


    // Private Methods
    // =========================================================================

}
