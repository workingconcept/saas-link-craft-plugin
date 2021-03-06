<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\variables;

use workingconcept\saaslink\SaasLink;

class TrelloVariable
{

    public function getBoards()
    {
        return SaasLink::$plugin->trello->getBoards();
    }

}
