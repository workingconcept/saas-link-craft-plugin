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
 * Harvest Client Model
 * https://help.getharvest.com/api-v2/clients-api/clients/clients/
 */

class HarvestClient extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var integer Unique ID for the client.
     */
    public $id;

    /**
     * @var string A textual description of the client.
     */
    public $name;

    /**
     * @var boolean Whether the client is active or archived.
     */
    public $is_active;

    /**
     * @var string The physical address for the client.
     */
    public $address;

    /**
     * @var string The currency code associated with this client.
     */
    public $currency;

    /**
     * @var string Date and time the client was created.
     * TODO: convert to actual \DateTime
     */
    public $created_at;

    /**
     * @var string Date and time the client was last updated.
     * TODO: convert to actual \DateTime
     */
    public $updated_at;


    // Public Methods
    // =========================================================================

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
                'name', 
                'address', 
                'currency', 
                'created_at', 
                'updated_at', 
             ], 'string'],
            [[
                'is_active', 
             ], 'boolean'],
        ];
    }

}
