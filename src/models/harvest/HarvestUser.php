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
 * Harvest User Model
 * https://help.getharvest.com/api-v2/users-api/users/users/
 */

class HarvestUser extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int Unique ID for the user.
     */
    public $id;

    /**
     * @var string The first name of the user.
     */
    public $first_name;

    /**
     * @var string The last name of the user.
     */
    public $last_name;

    /**
     * @var string The email address of the user.
     */
    public $email;

    /**
     * @var string The telephone number for the user.
     */
    public $telephone;

    /**
     * @var string The user’s timezone.
     */
    public $timezone;

    /**
     * @var bool Whether the user should be automatically added to future projects.
     */
    public $has_access_to_all_future_projects;

    /**
     * @var bool Whether the user is a contractor or an employee.
     */
    public $is_contractor;

    /**
     * @var bool Whether the user has admin permissions.
     */
    public $is_admin;
    
    /**
     * @var bool Whether the user has project manager permissions.
     */
    public $is_project_manager;
    
    /**
     * @var bool Whether the user can see billable rates on projects. Only applicable to project managers.
     */
    public $can_see_rates;
    
    /**
     * @var bool Whether the user can create projects. Only applicable to project managers.
     */
    public $can_create_projects;
    
    /**
     * @var bool Whether the user can create invoices. Only applicable to project managers.
     */
    public $can_create_invoices;
    
    /**
     * @var bool Whether the user is active or archived.
     */
    public $is_active;
    
    /**
     * @var int The number of hours per week this person is available to work in seconds,
     *              in half hour increments. For example, if a person’s capacity is 35 hours, 
     *              the API will return 126000 seconds.
     */
    public $weekly_capacity;
    
    /**
     * @var float The billable rate to use for this user when they are added to a project.
     */
    public $default_hourly_rate;
    
    /**
     * @var float The cost rate to use for this user when calculating a project’s costs vs billable amount.
     */
    public $cost_rate;
    
    /**
     * @var string[] The role names assigned to this person.
     */
    public $roles;
    
    /**
     * @var string The URL to the user’s avatar image.
     */
    public $avatar_url;
    
    /**
     * @var string Date and time the user was created.
     */
    public $created_at;
    
    /**
     * @var string Date and time the user was last updated.
     */
    public $updated_at;


    // Public Methods
    // =========================================================================


    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [[
                'id', 
                'weekly_capacity'
             ], 'number', 'integerOnly' => true],
            [[
                'default_hourly_rate', 
                'cost_rate'
             ], 'number', 'integerOnly' => false],
            [['id'], 'required'],
            [[
                'first_name', 
                'last_name', 
                'email', 
                'telephone', 
                'timezone', 
                'avatar_url', 
                'created_at', 
                'updated_at'
             ], 'string'],
            [[
                'has_access_to_all_future_projects', 
                'is_contractor', 
                'is_admin', 
                'is_project_manager', 
                'can_see_rates', 
                'can_create_projects', 
                'can_create_invoices', 
                'is_active'
             ], 'boolean'],
            ['roles', 'each', 'rule' => ['string']],
        ];
    }

}
