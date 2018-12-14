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
 * Harvest Invoice Model
 * https://help.getharvest.com/api-v2/invoices-api/invoices/invoices/
 */

class HarvestInvoice extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int Unique ID for the expense.
     */
    public $id;

    /**
     * @var object An object containing invoice’s client id and name.
     */
    private $_client;

    /**
     * @var array Array of invoice line items.
     */
    public $line_items;

    /**
     * @var object An object containing the associated estimate’s id.
     */
    private $_estimate;

    /**
     * @var object An object containing the associated retainer’s id.
     */
    private $_retainer;

    /**
     * @var object An object containing the id and name of the person that created the invoice.
     */
    private $_creator;

    /**
     * @var string Used to build a URL to the public web invoice for your client: 
     *             `https://{ACCOUNT_SUBDOMAIN}.harvestapp.com/client/invoices/abc123456`
     */
    public $client_key;

    /**
     * @var string If no value is set, the number will be automatically generated.
     */
    public $number;

    /**
     * @var string The purchase order number.
     */
    public $purchase_order;

    /**
     * @var float The total amount for the invoice, including any discounts and taxes.
     */
    public $amount;

    /**
     * @var float The total amount due at this time for this invoice.
     */
    public $due_amount;

    /**
     * @var float This percentage is applied to the subtotal, including line items and discounts.
     */
    public $tax;

    /**
     * @var float The first amount of tax included, calculated from tax. If no tax is defined, this value will be null.
     */
    public $tax_amount;

    /**
     * @var float This percentage is applied to the subtotal, including line items and discounts.
     */
    public $tax2;

    /**
     * @var float The amount calculated from tax2.
     */
    public $tax2_amount;

    /**
     * @var float This percentage is subtracted from the subtotal.
     */
    public $discount;

    /**
     * @var float The amount calcuated from discount.
     */
    public $discount_amount;

    /**
     * @var string The invoice subject.
     */
    public $subject;

    /**
     * @var string Any additional notes included on the invoice.
     */
    public $notes;

    /**
     * @var string The currency code associated with this invoice.
     */
    public $currency;

    /**
     * @var string The current state of the invoice: draft, open, paid, or closed.
     */
    public $state;

    /**
     * @var string Start of the period during which time entries were added to this invoice.
     */
    public $period_start;

    /**
     * @var string End of the period during which time entries were added to this invoice.
     */
    public $period_end;

    /**
     * @var string Date the invoice was issued.
     */
    public $issue_date;

    /**
     * @var string Date the invoice is due.
     */
    public $due_date;

    /**
     * @var string The timeframe in which the invoice should be paid. Options: upon receipt, net 15, net 30, net 45, net 60, or custom.
     */
    public $payment_term;

    /**
     * @var string Date and time the invoice was sent.
     */
    public $sent_at;

    /**
     * @var string Date and time the invoice was paid.
     */
    public $paid_at;

    /**
     * @var date Date the invoice was paid.
     */
    public $paid_date;

    /**
     * @var string Date and time the invoice was closed.
     */
    public $closed_at;

    /**
     * @var string Date and time the invoice was created.
     */
    public $created_at;

    /**
     * @var string Date and time the invoice was last updated.
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

    public function getEstimate()
    {
        return $this->_estimate;
    }

    public function setEstimate($estimate)
    {
        return $this->_estimate = $estimate;
    }

    public function getRetainer()
    {
        return $this->_retainer;
    }

    public function setRetainer($retainer)
    {
        return $this->_retainer = $retainer;
    }

    public function getCreator()
    {
        return $this->_creator;
    }

    public function setCreator($creator)
    {
        return $this->_creator = $creator;
    }

    public function rules()
    {
        return [
            [[
                'id', 
             ], 'number', 'integerOnly' => true],
            [['id'], 'required'],
            [[
                'amount', 
                'due_amount', 
                'tax', 
                'tax_amount', 
                'tax2', 
                'tax2_amount', 
                'discount', 
                'discount_amount', 
             ], 'number', 'integerOnly' => false],
            [[
                'client_key', 
                'number', 
                'purchase_order', 
                'subject', 
                'notes', 
                'currency', 
                'state', 
                'period_start', 
                'period_end', 
                'issue_date', 
                'due_date', 
                'payment_term', 
                'sent_at', 
                'paid_at', 
                'paid_date', 
                'closed_at', 
                'created_at', 
                'updated_at', 
             ], 'string'],
        ];
    }

}
