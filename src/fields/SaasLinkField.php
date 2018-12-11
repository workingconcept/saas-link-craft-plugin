<?php
/**
 * SaaS Link plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\saaslink\fields;

use workingconcept\saaslink\SaasLink;
use Craft;
use craft\fields\data\SingleOptionFieldData;
use craft\fields\BaseOptionsField;
use craft\base\ElementInterface;
use yii\db\Schema;

class SaasLinkField extends BaseOptionsField
{
    // Public Properties
    // =========================================================================

    public $service;
    public $relationshipType;


    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('saas-link', 'SaaS Link');
    }


    // Public Methods
    // =========================================================================

    public function rules()
    {
        return [
            [['service', 'relationshipType'], 'string'],
            [['service', 'relationshipType'], 'required'],
        ];
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // start with an empty item
        $this->options = array_merge([
                [
                    'label'   => '',
                    'value'   => '',
                    'link'    => null,
                    'default' => null
                ]
            ],
            $this->getOptions()
        );
    }

    public function getAvailableServices()
    {
        $options = [];

        foreach (SaasLink::$plugin->getSettings()->getEnabledServices() as $service) 
        {
            $options[] = [
                'label' => $service->serviceName,
                'value' => $service->serviceSlug
            ];
        }

        return $options;
    }

    public function getAvailableRelationshipTypes()
    {
        if ($serviceInstance = $this->getServiceInstance())
        {
            return $serviceInstance->getAvailableRelationshipTypes();
        }

        return [];
    }


    public function getOptions()
    {
        if ($serviceInstance = $this->getServiceInstance())
        {
            return $serviceInstance->getOptions($this->relationshipType);
        }

        return [];
    }


    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // If this is a new entry, look for a default option
        if ($this->isFresh($element))
        {
            $value = $this->defaultValue();
        }

        return Craft::$app->getView()->renderTemplate('saas-link/select',
            [
                'name'       => $this->handle,
                'value'      => $value,
                'optionLink' => $this->getLinkFromValue($value),
                'options'    => $this->options,
            ]);
    }


    /**
     * Get a convenient URL for the selected record to show next to the selection.
     *
     * @param string $value Stored field value.
     * @return void
     */
    public function getLinkFromValue($value)
    {
        if ( ! empty($value))
        {
            $compareValue = isset($value->value) ? $value->value : $value;

            foreach	($this->options as $option)
            {
                if ($option['value'] === $compareValue)
                {
                    return $option['link'];
                }
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (!$value)
        {
            $value = $this->defaultValue();
        }

        // Normalize to an array
        $selectedValues = (array)$value;
        $value = reset($selectedValues) ?: null;
        $label = $this->optionLabel($value);
        $value = new SingleOptionFieldData($label, $value, true);

        return $value;
    }

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('saas-link/field-settings', [
            'settings' => SaasLink::$plugin->getSettings(),
            'field' => $this
        ]);
    }


    private function getServiceInstance()
    {
        foreach (SaasLink::$plugin->getSettings()->getEnabledServices() as $service)
        {
            if ($this->service === $service->serviceSlug)
            {
                return $service;
            }
        }

        return;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function optionsSettingLabel(): string
    {
        return Craft::t('saas-link', 'SaaS Link');
    }

}
