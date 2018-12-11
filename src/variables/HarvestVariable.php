<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\variables;

use workingconcept\saaslink\SaasLink;

class HarvestVariable
{

    public function getProject($projectId)
    {
        return SaasLink::$plugin->harvest->getProject($projectId);
    }

    public function getClientProjects($clientId, $active = true)
    {
        return SaasLink::$plugin->harvest->getClientProjects($clientId, $active);
    }

}
