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
 * Harvest Time Entry Model
 * https://help.getharvest.com/api-v2/timesheets-api/timesheets/time-entries/
 */

class HarvestTimeEntry extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int Unique ID for the time entry.
     */
    public $id;

    /**
     * @var string Date of the time entry.
     */
    public $spent_date;

    /**
     * @var object An object containing the id and name of the associated user.
     */
    private $_user;

    /**
     * @var object A user assignment object of the associated user.
     */
    private $_user_assignment;

    /**
     * @var object An object containing the id and name of the associated client.
     */
    private $_client;

    /**
     * @var object An object containing the id and name of the associated project.
     */
    private $_project;

    /**
     * @var object An object containing the id and name of the associated task.
     */
    private $_task;

    /**
     * @var object A task assignment object of the associated task.
     */
    private $_task_assignment;

    /**
     * @var object An object containing the id, group_id, permalink, service, and service_icon_url of the associated external reference.
     */
    private $_external_reference;

    /**
     * @var object Once the time entry has been invoiced, this field will include the associated invoice’s id and number.
     */
    private $_invoice;

    /**
     * @var float Number of (decimal time) hours tracked in this time entry.
     */
    public $hours;

    /**
     * @var string Notes attached to the time entry.
     */
    public $notes;

    /**
     * @var bool Whether or not the time entry has been locked.
     */
    public $is_locked;

    /**
     * @var string Why the time entry has been locked.
     */
    public $locked_reason;

    /**
     * @var bool Whether or not the time entry has been approved via Timesheet Approval.
     */
    public $is_closed;

    /**
     * @var bool Whether or not the time entry has been marked as invoiced.
     */
    public $is_billed;

    /**
     * @var string Date and time the timer was started (if tracking by duration). Use the ISO 8601 Format.
     */
    public $timer_started_at;

    /**
     * @var string Time the time entry was started (if tracking by start/end times).
     */
    public $started_time;

    /**
     * @var string Time the time entry was ended (if tracking by start/end times).
     */
    public $ended_time;

    /**
     * @var bool Whether or not the time entry is currently running.
     */
    public $is_running;

    /**
     * @var bool Whether or not the time entry is billable.
     */
    public $billable;

    /**
     * @var bool Whether or not the time entry counts towards the project budget.
     */
    public $budgeted;

    /**
     * @var float The billable rate for the time entry.
     */
    public $billable_rate;

    /**
     * @var float The cost rate for the time entry.
     */
    public $cost_rate;

    /**
     * @var string Date and time the time entry was created. Use the ISO 8601 Format.
     */
    public $created_at;

    /**
     * @var string Date and time the time entry was last updated. Use the ISO 8601 Format.
     */
    public $updated_at;


    // Public Methods
    // =========================================================================

    public function getUser()
    {
        return $this->_user;
    }
    
    public function setUser($user)
    {
        return $this->_user = $user;
    }
    
    public function getUserAssignment()
    {
        return $this->_user_assignment;
    }
    
    public function setUser_Assignment($userAssignment)
    {
        return $this->_user_assignment = $userAssignment;
    }
    
    public function getClient()
    {
        return $this->_client;
    }
    
    public function setClient($client)
    {
        return $this->_client = $client;
    }
    
    public function getProject()
    {
        return $this->_project;
    }
    
    public function setProject($project)
    {
        return $this->_project = $project;
    }
    
    public function getTask()
    {
        return $this->_task;
    }
    
    public function setTask($task)
    {
        return $this->_task = $task;
    }
    
    public function getTaskAssignment()
    {
        return $this->_task_assignment;
    }
    
    public function setTask_Assignment($taskAssignment)
    {
        return $this->_task_assignment = $taskAssignment;
    }
    
    public function getExternalReference()
    {
        return $this->_external_reference;
    }
    
    public function setExternal_Reference($externalReference)
    {
        return $this->_external_reference = $externalReference;
    }
    
    public function getInvoice()
    {
        return $this->_invoice;
    }
    
    public function setInvoice($invoice)
    {
        return $this->_invoice = $invoice;
    }
    
    public function rules()
    {
        return [
            [[
                'id', 
             ], 'number', 'integerOnly' => true],
            [['id'], 'required'],
            [[
                'hours', 
                'billable_rate', 
                'cost_rate', 
             ], 'number', 'integerOnly' => false],
            [[
                'spent_date', 
                'notes', 
                'locked_reason', 
                'timer_started_at', 
                'started_time', 
                'ended_time', 
                'created_at', 
                'updated_at', 
             ], 'string'],
            [[
                'is_locked', 
                'locked_reason', 
                'is_closed', 
                'is_billed', 
                'is_running', 
                'billable', 
                'budgeted', 
             ], 'boolean'],
        ];
    }
}
