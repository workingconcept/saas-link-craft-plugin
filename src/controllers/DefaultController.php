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

        foreach (SaasLink::$plugin->getEnabledServices() as $service)
        {
            if ($serviceName === $service->serviceSlug)
            {
                return $this->asJson($service->getAvailableRelationshipTypes());
            }
        }

        return $this->asJson([]);
    }

    public function actionFetchTrelloOrganizationOptions(): Response
    {
        $this->requirePostRequest();

        $options = [];
        $request = Craft::$app->getRequest();
        $trelloKey = $request->getBodyParam('trelloKey');
        $trelloToken = $request->getBodyParam('trelloToken');

        SaasLink::$plugin->getSettings()->trelloApiKey = $trelloKey;
        SaasLink::$plugin->getSettings()->trelloApiToken = $trelloToken;
        SaasLink::$plugin->getSettings()->trelloOrganizationId = '¯\_(ツ)_/¯';

        $organizations = SaasLink::$plugin->trello->getMemberOrganizations();

        foreach ($organizations as $organization)
        {
            $options[] = [
                'label' => $organization->displayName,
                'value' => $organization->id,
            ];
        }

        return $this->asJson($options);
    }

}
