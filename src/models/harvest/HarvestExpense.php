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
 * Harvest Expense Model
 * https://help.getharvest.com/api-v2/expenses-api/expenses/expenses/
 *
 * Note that there are some write-only fields here, and undocumented ones that come with a standard model via GET.
 */

class HarvestExpense extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var integer Unique ID for the expense.
     */
    public $id;

    /**
     * @var object An object containing the expense’s client id, name, and currency.
     */
    private $_client;

    /**
     * @var object An object containing the expense’s project id, name, and code.
     */
    private $_project;

    /**
     * @var integer The ID of the project associated with this expense. (write only)
     */
    public $project_id;

    /**
     * @var object An object containing the expense’s expense category id, name, unit_price, and unit_name.
     */
    private $_expense_category;

    /**
     * @var integer The ID of the expense category this expense is being tracked against. (write only)
     */
    public $expense_category_id;

    /**
     * @var object An object containing the id and name of the user that recorded the expense.
     */
    private $_user;

    /**
     * @var object A user assignment object of the user that recorded the expense.
     */
    private $_user_assignment;

    /**
     * @var object An object containing the expense’s receipt URL and file name.
     */
    private $_receipt;

    /**
     * @var string A receipt file to attach to the expense. If including a receipt, you must submit a multipart/form-data request. (write only)
     */
    public $recipt;

    /**
     * @var object Once the expense has been invoiced, this field will include the associated invoice’s id and number.
     */
    private $_invoice;

    /**
     * @var string Textual notes used to describe the expense.
     */
    public $notes;

    /**
     * @var decimal The total amount of the expense.
     */
    public $total_cost;

    /**
     * @var integer The quantity of units to use in calculating the total_cost of the expense.
     */
    public $units;

    /**
     * @var boolean Whether the expense is billable or not.
     */
    public $billable;

    /**
     * @var boolean Whether the expense has been approved or closed for some other reason.
     */
    public $is_closed;

    /**
     * @var boolean Whether the expense has been been invoiced, approved, or the project or person related to the expense is archived.
     */
    public $is_locked;

    /**
     * @var boolean Whether or not the expense has been marked as invoiced.
     */
    public $is_billed;

    /**
     * @var string An explanation of why the expense has been locked.
     */
    public $locked_reason;

    /**
     * @var boolean Whether an attached expense receipt should be deleted. Pass true to delete the expense receipt. (write only)
     */
    public $delete_receipt;

    /**
     * @var date Date the expense occurred.
     */
    public $spent_date;

    /**
     * @var datetime Date and time the expense was created.
     */
    public $created_at;

    /**
     * @var datetime Date and time the expense was last updated.
     */
    public $updated_at;


    // Public Methods
    // =========================================================================

    
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

    public function getExpenseCategory()
    {
        return $this->_expense_category;
    }

    public function setExpense_Category($expenseCategory)
    {
        return $this->_expense_category = $expenseCategory;
    }

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

    public function getReceipt()
    {
        return $this->_receipt;
    }

    public function setReceipt($receipt)
    {
        return $this->_receipt = $receipt;
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
                'units', 
             ], 'number', 'integerOnly' => true],
            [['id'], 'required'],
            [[
                'total_cost', 
             ], 'number', 'integerOnly' => false],
            [[
                'notes', 
                'locked_reason', 
                'spent_date', 
                'created_at', 
                'updated_at', 
             ], 'string'],
            [[
                'billable', 
                'is_closed', 
                'is_locked', 
                'is_billed', 
             ], 'boolean'],
        ];
    }

}
