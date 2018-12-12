<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\services;

use workingconcept\saaslink\SaasLink;
use workingconcept\saaslink\models\harvest\HarvestClient;
use workingconcept\saaslink\models\harvest\HarvestExpense;
use workingconcept\saaslink\models\harvest\HarvestInvoice;
use workingconcept\saaslink\models\harvest\HarvestProject;
use workingconcept\saaslink\models\harvest\HarvestTimeEntry;
use workingconcept\saaslink\models\harvest\HarvestUser;
use workingconcept\saaslink\models\harvest\HarvestCompany;
use Craft;

class HarvestService extends SaasLinkService
{
    // Constants
    // =========================================================================

    const CACHE_ENABLED = true;
    const CACHE_SECONDS = 60;
    const TIME_ROUNDING_METHOD = 'nextHalfHour'; // nextHalfHour, nearestHalfHour, nextWholeNumber, nearestWholeNumber


    // Properties
    // =========================================================================

    /**
     * @var string
     */
    protected $apiBaseUrl  = 'https://api.harvestapp.com/v2/';

    /**
     * @var string
     */
    public $serviceName = 'Harvest';

    /**
     * @var string
     */
    public $serviceSlug = 'harvest';

    /**
     * @var HarvestProject
     */
    private $project;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function isConfigured(): bool
    {
        return ! empty($this->settings->harvestToken) && ! empty($this->settings->harvestAccountId);
    }

    /**
     * @inheritdoc
     */
    public function configureClient()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->apiBaseUrl,
            'headers' => [
                'Authorization'      => 'Bearer ' . $this->settings->harvestToken,
                'Harvest-Account-Id' => $this->settings->harvestAccountId,
                'User-Agent'         => 'Craft CMS',
            ],
            'verify' => false,
            'debug' => false
        ]);

        if (empty($this->settings->harvestBaseUrl))
        {
            $this->setHarvestBaseUrlSetting();
        }
    }

    /**
     * Save a reference to the customer-facing base Harvest URL so we don't have to keep looking it up.
     */
    public function setHarvestBaseUrlSetting()
    {
        $this->settings->harvestBaseUrl = $this->getCompany()->base_uri;

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
                'label' => Craft::t('saas-link', 'Client'),
                'value' => 'client'
            ],
            [
                'label' => Craft::t('saas-link', 'Project'),
                'value' => 'project'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getOptions($relationshipType): array
    {
        $options = [];

        if ($relationshipType === 'client')
        {
            foreach ($this->getClients() as $client)
            {
                $options[] = [
                    'label'   => $client->name,
                    'value'   => (string)$client->id,
                    'link'    => $this->settings->harvestBaseUrl . '/reports/clients/' . $client->id,
                    'default' => null
                ];
            }

            // alphabetize
            usort($options, function($a, $b) {
                return strtolower($a['label']) <=> strtolower($b['label']);
            });
        }
        elseif ($relationshipType === 'project')
        {
            $projects = $this->getProjects();

            foreach ($projects as $project)
            {
                $options[] = [
                    'label'   => $project->name . ' (' . $project->client->name . ')',
                    'value'   => (string)$project->id,
                    'link'    => $this->settings->harvestBaseUrl . '/projects/' . $project->id,
                    'default' => null
                ];
            }
        }

        return $options;
    }

    /**
     * Get company information
     * https://help.getharvest.com/api-v2/company-api/company/company/
     *
     * @return HarvestCompany
     */
    public function getCompany(): HarvestCompany
    {
        if ($cachedResponse = Craft::$app->cache->get('harvest_company'))
        {
            return new HarvestCompany($cachedResponse);
        }

        $response = $this->client->get('company');
        $responseData = json_decode($response->getBody(true));

        Craft::$app->cache->set('harvest_company', $responseData, 3600);

        return new HarvestCompany($responseData);
    }

    /**
     * Get client list.
     * https://help.getharvest.com/api-v2/clients-api/clients/clients/
     *
     * @return array HarvestClient models
     */
    public function getClients(): array
    {
        $clients = [];
        $result  = $this->collectPaginatedResults('clients');

        foreach ($result as $clientData)
        {
            $clients[] = new HarvestClient($clientData);
        }

        return $clients;
    }

    /**
     * Get project list.
     * https://help.getharvest.com/api-v2/projects-api/projects/projects/
     *
     * @return array HarvestProject models
     */
    public function getProjects(): array
    {
        $projects = [];
        $result   = $this->collectPaginatedResults('projects');

        foreach ($result as $projectData)
        {
            $projects[] = new HarvestProject($projectData);
        }

        return $projects;
    }

    /**
     * Get user list.
     * https://help.getharvest.com/api-v2/users-api/users/users/
     *
     * @return array HarvestUser models
     */
    public function getUsers(): array
    {
        $users  = [];
        $result = $this->collectPaginatedResults('users');

        foreach ($result as $userData)
        {
            $users[] = new HarvestUser($userData);
        }

        return $users;
    }

    /**
     * Get project details.
     * https://help.getharvest.com/api-v2/projects-api/projects/projects/#retrieve-a-project
     *
     * @param  int $projectId  ID of relevant Harvest project
     *
     * @return HarvestProject
     */
    public function getProject($projectId): HarvestProject
    {
        if ( ! $this->project || $this->project->id !== $projectId)
        {
            $response     = $this->client->get('projects/' . $projectId);
            $responseData = json_decode($response->getBody(true));
            $project      = new HarvestProject($responseData->project);

            $this->project = $project;
        }

        return $this->project;
    }

    /**
     * Get projects for a specific client.
     * https://help.getharvest.com/api-v2/projects-api/projects/projects/#list-all-projects
     *
     * @param  int     $clientId  ID of relevant Harvest client
     * @param  boolean $active    whether to retrieve projects that are active
     *
     * @return array HarvestProject models
     */
    public function getClientProjects($clientId, $active): array
    {
        $projects = [];
        $result   = $this->collectPaginatedResults('projects?client_id='
            . $clientId
            . '&is_active=' . ($active ? 'true' : 'false')
        );

        foreach ($result as $projectData)
        {
            $projects[] = new HarvestProject($projectData);
        }

        return $projects;
    }

    /**
     * Get all time entries logged to a project.
     *
     * https://help.getharvest.com/api-v2/timesheets-api/timesheets/time-entries/
     *
     * @param  int   $projectId  ID of relevant Harvest project
     *
     * @return array HarvestTimeEntry models
     */
    public function getProjectTimeEntries($projectId): array
    {
        $entries = [];
        $result  = $this->collectPaginatedResults('time_entries?project_id=' . $projectId);

        foreach ($result as $entryData)
        {
            $entries[] = new HarvestTimeEntry($entryData);
        }

        return $entries;
    }

    /**
     * Get all time entries logged by the given user.
     *
     * https://help.getharvest.com/api-v2/timesheets-api/timesheets/time-entries/
     *
     * @param  int   $userId  ID of relevant Harvest user
     *
     * @return array HarvestTimeEntry models
     */
    public function getUserTimeEntries($userId): array
    {
        $entries = [];
        $result  = $this->collectPaginatedResults('time_entries?user_id=' . $userId);

        foreach ($result as $entryData)
        {
            $entries[] = new HarvestTimeEntry($entryData);
        }

        return $entries;
    }

    /**
     * Get all expenses related to a project.
     *
     * https://help.getharvest.com/api-v2/expenses-api/expenses/expenses/
     *
     * @param  int   $projectId  ID of relevant Harvest project
     *
     * @return array HarvestExpense models
     */
    public function getProjectExpenses($projectId): array
    {
        $expenses = [];
        $result   = $this->collectPaginatedResults('expenses?project_id=' . $projectId);

        foreach ($result as $expenseData)
        {
            $expenses[] = new HarvestExpense($expenseData);
        }

        return $expenses;
    }

    /**
     * Get all invoices related to a project.
     * https://help.getharvest.com/api-v2/invoices-api/invoices/invoices/
     *
     * @param  int   $projectId  ID of relevant Harvest project
     *
     * @return array             API response with Invoice objects
     */
    public function getProjectInvoices($projectId): array
    {
        $invoices = [];
        $result   = $this->collectPaginatedResults('invoices?project_id=' . $projectId);

        foreach ($result as $invoiceData)
        {
            $invoices[] = new HarvestInvoice($invoiceData);
        }

        return $invoices;
    }

    /**
     * Get the total number of hours logged on a given project.
     *
     * @param int     $projectId            ID of relevant Harvest project
     * @param boolean $billableOnly         only count billable hours (default true)
     * @param boolean $individuallyRounded  round time to nearest half hour before adding to the total (default true)
     *
     * @return float
     */
    public function getTotalProjectHours($projectId, $billableOnly = false, $individuallyRounded = true): float
    {
        $timeEntries       = $this->getProjectTimeEntries($projectId);
        $totalRoundedHours = 0;
        $totalHours        = 0;
        $hoursByPerson     = [];

        foreach ($timeEntries as $timeEntry)
        {
            if ($timeEntry->billable || $billableOnly === false)
            {
                $roundedHours       = $this->roundTime($timeEntry->hours, self::TIME_ROUNDING_METHOD);
                $totalRoundedHours += $roundedHours;
                $totalHours        += $timeEntry->hours;

                if ( ! isset($hoursByPerson[$timeEntry->user->name]))
                {
                    $hoursByPerson[$timeEntry->user->name] = $timeEntry->hours;
                }
                else
                {
                    $hoursByPerson[$timeEntry->user->name] += $timeEntry->hours;
                }

                //echo "$timeEntry->hours // {$timeEntry->user->name} on $timeEntry->spent_date\n";
            }
        }

        //print_r($hoursByPerson);

        // echo "-------------------------------------------";
        // echo "total: " . $totalHours;
        // echo "total: " . $totalRoundedHours . " (rounded)";

        return $individuallyRounded ? $totalRoundedHours : $totalHours;
    }

    /**
     * Get a user's logged hours within a range of dates.
     *
     * @param int      $userId     Harvest user ID
     * @param DateTime $startDate  beginning of date range
     * @param DateTime $endDate    end of date range
     * @param bool     $billable   whether to return billable or non-billable hours
     *
     * @return float
     */
    public function getTotalUserHoursInRange($userId, $startDate, $endDate, $billable, $roundTime = false): float
    {
        $from  = $startDate->format('Y-m-d');
        $to    = $endDate->format('Y-m-d');
        $total = 0;

        $timeEntries = $this->collectPaginatedResults('time_entries?user_id=' . $userId . '&from=' . $from . '&to=' . $to);

        foreach ($timeEntries as $timeEntry)
        {
            if (($timeEntry->billable && $billable === true) || ($timeEntry->billable === false && $billable === false))
            {
                $roundedHours = $this->roundTime($timeEntry->hours, self::TIME_ROUNDING_METHOD);

                if ($roundTime)
                {
                    $total += $roundedHours * $timeEntry->billable_rate;
                }
                else
                {
                    $total += $timeEntry->hours * $timeEntry->billable_rate;
                }
            }
        }

        return $total;
    }

    /**
     * Calculate and return the grand total for all invoices on a given project.
     *
     * @param int $projectId  ID of relevant Harvest project
     *
     * @return float
     */
    public function getTotalProjectInvoiced($projectId): float
    {
        $invoices  = $this->getProjectInvoices($projectId);
        $total     = 0;
        $totalPaid = 0;

        foreach ($invoices as $invoice)
        {
            $total     += $invoice->amount;
            $paid       = $invoice->amount - $invoice->due_amount;
            $totalPaid += $paid;
        }

        return $total;
    }

    /**
     * Get uninvoiced billables for a given project.
     *
     * @param int     $projectId        ID of relevant Harvest project
     * @param boolean $includeExpenses  include billable project expenses?
     * @param boolean $includeTime      include logged billable time?
     * @param boolean $roundTime        round logged time before adding to the total?
     *
     * @return float
     */
    public function getTotalProjectUninvoiced($projectId, $includeExpenses = true, $includeTime = true, $roundTime = true): float
    {
        $total = 0;

        if ($includeExpenses)
        {
            $expenseEntries = $this->getProjectExpenses($projectId);

            foreach ($expenseEntries as $expense)
            {
                if ($expense->billable && ! $expense->is_billed)
                {
                    $total += $expense->total_cost;
                }
            }
        }

        if ($includeTime)
        {
            $timeEntries = $this->getProjectTimeEntries($projectId);

            foreach ($timeEntries as $timeEntry)
            {
                if ($timeEntry->billable && !$timeEntry->is_billed)
                {
                    $roundedHours = $this->roundTime($timeEntry->hours, self::TIME_ROUNDING_METHOD);

                    if ($roundTime)
                    {
                        $total += $roundedHours * $timeEntry->billable_rate;
                    }
                    else
                    {
                        $total += $timeEntry->hours * $timeEntry->billable_rate;
                    }
                }
            }
        }

        return $total;
    }

    /**
     * Total and return all costs for a given project.
     *
     * @param int     $projectId       Relevant Harvest project ID.
     * @param boolean $includeExpenses Calculate expenses toward total cost.
     * @param boolean $includeTime     Calculate logged time toward total cost.
     * @param boolean $roundTime       Round time when calculating its cost.
     *
     * @return float
     */
    public function getTotalProjectCosts($projectId, $includeExpenses = true, $includeTime = true, $roundTime = false): float
    {
        $total                = 0;
        $billableExpensesOnly = true;
        $billableTimeOnly     = true;

        if ($includeExpenses)
        {
            $expenseEntries = $this->getProjectExpenses($projectId);

            foreach ($expenseEntries as $expense)
            {
                if ($expense->billable || $billableExpensesOnly === false)
                {
                    $total += $expense->total_cost;
                }
            }
        }

        if ($includeTime)
        {
            $timeEntries = $this->getProjectTimeEntries($projectId);

            foreach ($timeEntries as $timeEntry)
            {
                if ($timeEntry->billable || $billableExpensesOnly === false)
                {
                    $roundedHours = $this->roundTime($timeEntry->hours, self::TIME_ROUNDING_METHOD);

                    if ($roundTime)
                    {
                        $timeCost = $roundedHours * $timeEntry->cost_rate;
                    }
                    else
                    {
                        $timeCost = $timeEntry->hours * $timeEntry->cost_rate;
                    }

                    $total += $timeCost;
                }
            }
        }

        return $total;
    }


    // Private Methods
    // =========================================================================

    /**
     * Make an API call and continue through any pagination.
     * Endpoint will be used as a cache key when caching is enabled.
     *
     * @param string $endpoint  API endpoint to be queried, which can include URL parameters but *not* `page=`
     * @param string $property  optional property that contains result object array (defaults to cleaned endpoint name)
     *
     * @return array
     */
    private function collectPaginatedResults($endpoint, $property = ''): array
    {
        $cacheKey = 'harvest_' . $endpoint;

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

            if ($responseData->links->next)
            {
                $page++;
                continue;
            }

            $fetchedAllRecords = true;
        }

        if (self::CACHE_ENABLED)
        {
            Craft::$app->cache->set($cacheKey, $records, self::CACHE_SECONDS);
        }

        return $records;
    }


    /**
     * Round a decimal representing logged time in one of several interesting ways.
     *
     * @param float  $hours
     * @param string $method 'nextHalfHour', 'nearestHalfHour', 'nextWholeNumber', or 'nearestWholeNumber'
     *                       missing or invalid option will skip rounding
     *
     * @return float
     */
    private function roundTime($hours, $method = ''): float
    {
        if ($method === 'nextHalfHour')
        {
            // round up to nearest half hour
            return ceil($hours * 2) / 2;
        }
        elseif ($method === 'nearestHalfHour')
        {
            // round up or down to closest half hour
            return round($hours * 2) / 2;
        }
        elseif ($method === 'nextWholeNumber')
        {
            // round up to whole number
            return round($hours);
        }
        elseif ($method === 'nearestWholeNumber')
        {
            // round up or down to closest whole number
            return round($hours, 0, PHP_ROUND_HALF_EVEN);
        }

        return $hours;
    }
}
