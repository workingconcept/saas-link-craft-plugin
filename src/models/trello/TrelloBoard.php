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
 * Trello Board Model
 * https://developers.trello.com/reference/#board-object
 */

class TrelloBoard extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string The ID of the board
     */
    public $id;

    /**
     * @var string The name of the board
     */
    public $name;

    /**
     * @var string The description of the board.
     * @deprecated
     */
    public $desc;

    /**
     * @var string|null If the description includes custom emoji, this will contain the data necessary to display them.
     */
    public $descData;

    /**
     * @var bool Boolean whether the board has been closed or not.
     */
    public $closed;

    /**
     * @var string MongoID of the organization to which the board belongs.
     */
    public $idOrganization;

    /**
     * @var bool Boolean whether the board has been pinned or not.
     */
    public $pinned;

    /**
     * @var string Persistent URL for the board.
     */
    public $url;

    /**
     * @var string URL for the board using only its shortMongoID
     */
    public $shortUrl;

    /**
     * @var object Short for "preferences", these are the settings for the board
     */
    public $prefs;

    /**
     * @var object Object containing color keys and the label names given for one label of each color on the board. To get a full list of labels on the board see /boards/{id}/labels/.
     */
    public $labelNames;

    /**
     * @var boolean Whether the board has been starred by the current request's user.
     */
    public $starred;

    /**
     * @var object An object containing information on the limits that exist for the board. Read more about at Limits.
     */
    public $limits;

    /**
     * @var array Array of objects that represent the relationship of users to this board as memberships.
     */
    public $memberships;

    /**
     * @var string
     */
    public $shortLink;

    /**
     * @var array
     */
    public $powerUps;

    /**
     * @var string
     */
    public $dateLastActivity;

    /**
     * @var string
     */
    public $dateLastView;

    /**
     * @var array
     */
    public $idTags;

    /**
     * @var string|null
     */
    public $datePluginDisable;

    /**
     * @var
     */
    public $creationMethod;

    /**
     * @var
     */
    public $subscribed;

}
