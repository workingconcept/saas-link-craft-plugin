<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\variables;

use workingconcept\saaslink\SaasLink;

class CapsuleVariable
{

    public function getOrganizations()
    {
        return SaasLink::$plugin->capsule->getOrganizations();
    }

    public function getPeople()
    {
        return SaasLink::$plugin->capsule->getPeople();
    }

    public function getOpportunities()
    {
        return SaasLink::$plugin->capsule->getOpportunities();
    }

}
