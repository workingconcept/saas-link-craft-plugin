<?php

namespace workingconcept\saaslink\migrations;

use Craft;
use craft\db\Migration;
use workingconcept\saaslink\SaasLink;

/**
 * m181212_040717_settings_update migration.
 */
class m181212_040717_settings_update extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // re-save plugin settings
        Craft::$app->plugins->savePluginSettings(SaasLink::$plugin);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181212_040717_settings_update cannot be reverted.\n";
        return false;
    }
}
