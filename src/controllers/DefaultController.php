<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * Craft Field Types for linking Entries to Harvest, Trello, and Capsule.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\controllers;

use workingconcept\saaslink\SaasLink;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{

    public function actionFetchRelationshipTypes(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $serviceName = $request->getBodyParam('selectedService');

        foreach (SaasLink::$plugin->getSettings()->getEnabledServices() as $service)
        {
            if ($serviceName === $service->serviceSlug)
            {
                return $this->asJson($service->getAvailableRelationshipTypes());
            }
        }

        return $this->asJson([]);
    }

}
