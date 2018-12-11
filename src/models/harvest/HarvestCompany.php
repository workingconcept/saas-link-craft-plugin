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
 * Harvest Company Model
 * https://help.getharvest.com/api-v2/company-api/company/company/
 */

class HarvestCompany extends Model
{
    // Constants
    // =========================================================================

    const WEEK_START_SATURDAY       = 'Saturday';
    const WEEK_START_SUNDAY         = 'Sunday';
    const WEEK_START_MONDAY         = 'Monday';
    const TIME_FORMAT_DECIMAL       = 'decimal';
    const TIME_FORMAT_HOURS_MINUTES = 'hours_minutes';
    const PLAN_TYPE_TRIAL           = 'trial';
    const PLAN_TYPE_FREE            = 'free';
    const PLAN_TYPE_SIMPLE_V4       = 'simple-v4';
    const CLOCK_12H                 = '12h';
    const CLOCK_24H                 = '24h';


    // Properties
    // =========================================================================

    /**
     * @var string The Harvest URL for the company.
     */
    public $base_uri;

    /**
     * @var string The Harvest domain for the company.
     */
    public $full_domain;

    /**
     * @var string The name of the company.
     */
    public $name;

    /**
     * @var bool Whether the company is active or archived.
     */
    public $is_active;

    /**
     * @var string The week day used as the start of the week. Returns one of: `Saturday`, `Sunday`, or `Monday`.
     */
    public $week_start_day;

    /**
     * @var bool Whether time is tracked via duration or start and end times.
     */
    public $wants_timestamp_timers;

    /**
     * @var string The format used to display time in Harvest. Returns either `decimal` or `hours_minutes`.
     */
    public $time_format;

    /**
     * @var string The type of plan the company is on. Examples: `trial`, `free`, or `simple-v4`
     */
    public $plan_type;

    /**
     * @var string Used to represent whether the company is using a 12-hour or 24-hour clock. Returns either `12h` or `24h`.
     */
    public $clock;

    /**
     * @var string Symbol used when formatting decimals.
     */
    public $decimal_symbol;

    /**
     * @var string Separator used when formatting numbers.
     */
    public $thousands_separator;

    /**
     * @var string The color scheme being used in the Harvest web client.
     */
    public $color_scheme;

    /**
     * @var bool Whether the expense module is enabled.
     */
    public $expense_feature;

    /**
     * @var bool Whether the invoice module is enabled.
     */
    public $invoice_feature;

    /**
     * @var bool Whether the estimate module is enabled.
     */
    public $estimate_feature;

    /**
     * @var bool Whether the approval module is enabled.
     */
    public $approval_feature;

    /**
     * @var bool
     */
    public $approval_required;
}
