<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\models\capsule;

use craft\base\Model;

/**
 * Capsule Opportunity Model
 * https://developer.capsulecrm.com/v2/models/opportunity
 */

class CapsuleOpportunity extends Model
{
    // Constants
    // =========================================================================

    const DURATION_BASIS_FIXED   = 'FIXED';
    const DURATION_BASIS_HOUR    = 'HOUR';
    const DURATION_BASIS_DAY     = 'DAY';
    const DURATION_BASIS_WEEK    = 'WEEK';
    const DURATION_BASIS_MONTH   = 'MONTH';
    const DURATION_BASIS_QUARTER = 'QUARTER';
    const DURATION_BASIS_YEAR    = 'YEAR';

    // Properties
    // =========================================================================

    /**
     * @var int The unique id of this opportunity. (read only)
     */
    public $id;

    /**
     * @var string The ISO date/time this opportunity was created. (read only)
     */
    public $createdAt;

    /**
     * @var string The ISO date/time when this opportunity was last updated. (read only)
     */
    public $updatedAt;

    /**
     * @var object The main contact for this opportunity.
     */
    public $party;

    /**
     * @var string The name of this opportunity.
     */
    public $name;

    /**
     * @var string|null The description of this opportunity.
     */
    public $description;

    /**
     * @var object|null The user this opportunity is assigned to. This and/or team is required.
     */
    public $owner;

    /**
     * @var object|null The team this opportunity is assigned to. This and/or owner is required.
     */
    public $team;

    /**
     * @var object The milestone for this opportunity.
     */
    public $milestone;

    /**
     * @var object The value of this opportunity.
     */
    public $value;

    /**
     * @var string|null The expected close date of this opportunity.
     */
    public $expectedCloseOn;

    /**
     * @var int|null The probability of winning this opportunity.
     */
    public $probability;

    /**
     * @var string|null The time unit used by the duration field.
     *                  Accepted values are: FIXED, HOUR, DAY, WEEK, MONTH, QUARTER, YEAR
     */
    public $durationBasis;

    /**
     * @var int The duration of this opportunity. Must be null if durationBasis is set to FIXED.
     */
    public $duration;

    /**
     * @var string The date this opportunity was closed.
     */
    public $closedOn;

    /**
     * @var array
     */
    public $tags;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var
     */
    public $lastStageChangedAt;

    /**
     * @var
     */
    public $lostReason;

    /**
     * @var
     */
    public $lastOpenMilestone;

    /**
     * @var
     */
    public $lastContactedAt;

}
