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
 * Capsule Party Model
 * https://developer.capsulecrm.com/v2/models/party
 */

class CapsuleParty extends Model
{
    // Constants
    // =========================================================================

    const TYPE_PERSON = 'person';
    const TYPE_ORGANISATION = 'organisation';

    // Properties
    // =========================================================================

    /**
     * @var int The unique id of this party. (read only)
     */
    public $id;

    /**
     * @var string Represents if this party is a person or an organisation.
     *             Accepted values are: person, organisation
     */
    public $type;

    /**
     * @var string|null First name of the person. This field is present only when type is person.
     */
    public $firstName;

    /**
     * @var string|null Last name of the person. This field is present only when type is person.
     */
    public $lastName;

    /**
     * @var string|null Title of the person. This field is present only when type is person.
     *             Accepted values are: Mr, Master, Mrs, Miss, Ms, Dr, Prof
     */
    public $title;

    /**
     * @var string|null Job title of the person. This field is present only when type is person.
     */
    public $jobTitle;

    /**
     * @var object|null The organization this party is associated with. This field is present only when type is person.
     */
    public $organisation;

    /**
     * @var string|null The name of the organization. This field is present only when type is organisation.
     */
    public $name;

    /**
     * @var string|null A short description about the party.
     */
    public $about;

    /**
     * @var string The ISO date/time when this party was created. (read only)
     */
    public $createdAt;

    /**
     * @var string The ISO date/time when this party was last updated. (read only)
     */
    public $updatedAt;

    /**
     * @var string The ISO date/time when this party was last contacted. This field is automatically set by Capsule and cannot be edited (read only)
     */
    public $lastContactedAt;

    /**
     * @var array An array of all the addresses associated with this party.
     */
    public $addresses;

    /**
     * @var array An array of all the phone numbers associated with this party.
     */
    public $phoneNumbers;

    /**
     * @var array An array of the websites and social network accounts associated with this party.
     */
    public $websites;

    /**
     * @var array An array of all the email addresses associated with this party.
     */
    public $emailAddresses;

    /**
     * @var string A URL that represents the location of the profile picture for this party. This field is automatically derived by Capsule. (read only)
     */
    public $pictureURL;

    /**
     * @var array An array of tags that are added to this party. This field is include in responses only if the embed parameter contains tags.
     */
    public $tags;

    /**
     * @var array An array of custom fields that are defined for this party. This field is include in responses only if the embed parameter contains fields.
     */
    public $fields;

    /**
     * @var object The user this party is assigned to.
     */
    public $owner;

    /**
     * @var object The team this party is assigned to.
     */
    public $team;

}
