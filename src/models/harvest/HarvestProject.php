<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\models\harvest;

use craft\base\Model;

/**
 * Harvest Project Model
 * https://help.getharvest.com/api-v2/projects-api/projects/projects/
 */

class HarvestProject extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var integer Unique ID for the project.
     */
    public $id;

    /**
     * @var HarvestClient An object containing the project’s client id, name, and currency.
     */
    private $_client;

    /**
     * @var string Unique name for the project.
     */
    public $name;

    /**
     * @var string The code associated with the project.
     */
    public $code;

    /**
     * @var boolean Whether the project is active or archived.
     */
    public $is_active;

    /**
     * @var boolean Whether the project is billable or not.
     */
    public $is_billable;

    /**
     * @var boolean Whether the project is a fixed-fee project or not.
     */
    public $is_fixed_fee;

    /**
     * @var string The method by which the project is invoiced.
     */
    public $bill_by;

    /**
     * @var decimal Rate for projects billed by Project Hourly Rate.
     */
    public $hourly_rate;

    /**
     * @var decimal The budget in hours for the project when budgeting by time.
     */
    public $budget;

    /**
     * @var string The method by which the project is budgeted.
     */
    public $budget_by;

    /**
     * @var boolean Option to have the budget reset every month.
     */
    public $budget_is_monthly;

    /**
     * @var boolean Whether project managers should be notified when the project goes over budget.
     */
    public $notify_when_over_budget;

    /**
     * @var decimal Percentage value used to trigger over budget email alerts.
     */
    public $over_budget_notification_percentage;

    /**
     * @var date Date of last over budget notification. If none have been sent, this will be null.
     */
    public $over_budget_notification_date;

    /**
     * @var boolean Option to show project budget to all employees. Does not apply to Total Project Fee projects.
     */
    public $show_budget_to_all;

    /**
     * @var decimal The monetary budget for the project when budgeting by money.
     */
    public $cost_budget;

    /**
     * @var boolean Option for budget of Total Project Fees projects to include tracked expenses.
     */
    public $cost_budget_include_expenses;

    /**
     * @var decimal The amount you plan to invoice for the project. Only used by fixed-fee projects.
     */
    public $fee;

    /**
     * @var string Project notes.
     */
    public $notes;

    /**
     * @var date Date the project was started.
     */
    public $starts_on;

    /**
     * @var date Date the project will end.
     */
    public $ends_on;

    /**
     * @var datetime Date and time the project was created.
     */
    public $created_at;

    /**
     * @var datetime Date and time the project was last updated.
     */
    public $updated_at;


    // Public Methods
    // =========================================================================

    /**
     * Gets the project's client.
     *
     * @return HarvestClient
     */
    public function getClient(): HarvestClient
    {
        return $this->_client;
    }

    /**
     * Sets the project's client.
     *
     * @param stdClass|HarvestClient $client Project client.
     *
     * @return HarvestClient
     */
    public function setClient($client)
    {
        if (is_object($client))
        {
            $client = new HarvestClient($client);
        }

        return $this->_client = $client;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 
             ], 'number', 'integerOnly' => true],
            [['id'], 'required'],
            [[
                'hourly_rate', 
                'budget',
                'over_budget_notification_percentage',
                'cost_budget',
                'fee',
             ], 'number', 'integerOnly' => false],
            [[
                'name', 
                'code', 
                'bill_by', 
                'budget_by', 
                'over_budget_notification_date', 
                'notes', 
                'starts_on', 
                'ends_on', 
                'created_at', 
                'updated_at', 
             ], 'string'],
            [[
                'is_active', 
                'is_billable', 
                'is_fixed_fee', 
                'budget_is_monthly', 
                'notify_when_over_budget', 
                'show_budget_to_all', 
                'cost_budget_include_expenses', 
             ], 'boolean'],
        ];
    }

}
