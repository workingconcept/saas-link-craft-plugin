<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\models\trello;

use craft\base\Model;

/**
 * Trello Organization Model
 * https://developers.trello.com/reference/#organization-object
 */

class TrelloOrganization extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string The ID of the organization
     */
    public $id;

    /**
     * @var string The programmatic name for the team. For example: `trelloinc`
     */
    public $name;

    /**
     * @var string The name for the team. For example: `Trello Inc`
     */
    public $displayName;

    /**
     * @var string The description for the team
     */
    public $desc;

    /**
     * @var object If there are custom emoji in the `desc` this will contain information about them.
     */
    public $descData;

    /**
     * @var string[] An array of board IDs that are in the team
     */
    public $idBoards;

    /**
     * @var
     */
    public $idEnterprise;

    /**
     * @var
     */
    public $invited;

    /**
     * @var
     */
    public $invitations;

    /**
     * @var
     */
    public $limits;

    /**
     * @var object[]
     */
    public $memberships;

    /**
     * @var object The preferences (settings) for the team
     */
    public $prefs;

    /**
     * @var
     */
    public $powerUps;

    /**
     * @var
     */
    public $products;

    /**
     * @var int
     */
    public $billableMemberCount;

    /**
     * @var int
     */
    public $activeBillableMemberCount;

    /**
     * @var int
     */
    public $billableCollaboratorCount;

    /**
     * @var string The URL to the team page on Trello
     */
    public $url;

    /**
     * @var string
     */
    public $website;

    /**
     * @var
     */
    public $logoHash;

    /**
     * @var
     */
    public $logoUrl;

    /**
     * @var
     */
    public $premiumFeatures;

    /**
     * @var
     */
    public $enterpriseJoinRequest;

    /**
     * @var
     */
    public $availableLicenseCount;

    /**
     * @var
     */
    public $maximumLicenseCount;
}
