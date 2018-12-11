<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * Craft Field Types for linking Entries to Harvest, Trello, and Capsule.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\assetbundles\saaslink;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SaasLinkCpAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@workingconcept/saaslink/assetbundles/saaslink/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/saas-link-field.js',
        ];

        $this->css = [];

        parent::init();
    }
}
