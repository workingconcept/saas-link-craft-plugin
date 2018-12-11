<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\services;

use workingconcept\saaslink\models\trello\TrelloBoard;
use workingconcept\saaslink\SaasLink;
use Craft;

class TrelloService extends SaasLinkService
{
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
    public function isConfigured()
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
    public function getAvailableRelationshipTypes()
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
    public function getOptions($relationshipType)
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


    // Private Methods
    // =========================================================================

    /**
     * Get boards.
     *
     * @param string $filter `all` or a comma-separated list of: `open`, `closed`, `members`, `organization`, `public`
     * @param string $fields `all` or comma-separated list of board [fields](https://developers.trello.com/reference#board-object)
     *
     * @return TrelloBoard[]
     */

    public function getBoards($filter = 'all', $fields = 'all')
    {
        // TODO: handle pagination
        $boards = [];

        if ($cachedResponse = Craft::$app->cache->get('trello_boards'))
        {
            $responseData = $cachedResponse;
        }
        else
        {
            $response     = $this->client->get('organizations/' . $this->settings->trelloOrganizationId . '/boards');
            $responseData = json_decode($response->getBody(true));

            Craft::$app->cache->set('trello_boards', $responseData, 3600);
        }

        foreach ($responseData as $responseItem)
        {
            $boards[] = new TrelloBoard($responseItem);
        }

        return $boards;
    }

}
