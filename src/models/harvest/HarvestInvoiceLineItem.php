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
 * Harvest Invoice Line Item Model
 * https://help.getharvest.com/api-v2/invoices-api/invoices/invoices/#the-invoice-line-item-object
 */

class HarvestInvoiceLineItem extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int Unique ID for the line item.
     */
    public $id;

    /**
     * @var object An object containing the associated project’s id, name, and code.
     */
    private $_project;

    /**
     * @var string The name of an invoice item category.
     */
    public $kind;

    /**
     * @var string Text description of the line item.
     */
    public $description;

    /**
     * @var int The unit quantity of the item.
     */
    public $quantity;

    /**
     * @var float The individual price per unit.
     */
    public $unit_price;

    /**
     * @var float The line item subtotal (quantity * unit_price).
     */
    public $amount;

    /**
     * @var bool Whether the invoice’s tax percentage applies to this line item.
     */
    public $taxed;

    /**
     * @var bool Whether the invoice’s tax2 percentage applies to this line item.
     */
    public $taxed2;


    // Public Methods
    // =========================================================================
    
    public function getProject()
    {
        return $this->_project;
    }

    public function setProject($project)
    {
        return $this->_project = $project;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[
                'id', 
                'quantity', 
             ], 'number', 'integerOnly' => true],
            [['id'], 'required'],
            [[
                'unit_price', 
                'amount', 
             ], 'number', 'integerOnly' => false],
            [[
                'kind', 
                'description', 
             ], 'string'],
            [[
                'taxed', 
                'taxed2', 
             ], 'boolean'],
        ];
    }

}
