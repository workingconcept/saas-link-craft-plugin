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

    private $defaultService;


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

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['service', 'relationshipType'], 'string'],
            [['service', 'relationshipType'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @return array
     */
    public function getAvailableServices(): array
    {
        $options = [];

        foreach (SaasLink::$plugin->getEnabledServices() as $service)
        {
            $options[] = [
                'label' => $service->serviceName,
                'value' => $service->serviceSlug
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getAvailableRelationshipTypes(): array
    {
        if ($serviceInstance = $this->getServiceInstance())
        {
            return $serviceInstance->getAvailableRelationshipTypes();
        }

        // pass the first set of options if this is a new field without a Service
        if (isset($this->defaultService))
        {
            return $this->defaultService->getAvailableRelationshipTypes();
        }

        return [];
    }

    /**
     * Get options from the relevant service based on field settings.
     * @return array
     */
    public function getOptions(): array
    {
        if (empty($this->options))
        {
            if ($serviceInstance = $this->getServiceInstance())
            {
                $this->options = $serviceInstance->getOptions($this->relationshipType);
            }
        }

        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        // Get all of the acceptable values
        $range = [];

        if ($options = $this->getOptions()) {
            foreach ($options as $option) {
                $range[] = $option['value'];
            }
        }

        return [
            ['in', 'range' => $range, 'allowArray' => false],
        ];
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

        $options = $this->getOptions();

        // add an empty first item
        array_unshift($options, [
                'label'   => '',
                'value'   => '',
                'link'    => null,
                'default' => null,
            ]
        );

        return Craft::$app->getView()->renderTemplate('saas-link/select',
            [
                'name'       => $this->handle,
                'value'      => $value,
                'optionLink' => $this->getLinkFromValue($value),
                'options'    => $options,
            ]
        );
    }

    /**
     * Get a convenient URL for the selected record to show next to the selection.
     *
     * @param mixed $value Stored field value.
     *
     * @return string|null
     */
    public function getLinkFromValue($value)
    {
        if ( ! empty($value))
        {
            $compareValue = $value->value ?? $value;

            foreach	($this->getOptions() as $option)
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


    // Private Methods
    // =========================================================================

    private function getServiceInstance()
    {
        foreach (SaasLink::$plugin->getEnabledServices() as $service)
        {
            if ( ! isset($this->defaultService))
            {
                // default to the first one in case we don't get a match (new field)
                $this->defaultService = $service;
            }

            if ($this->service === $service->serviceSlug)
            {
                return $service;
            }
        }
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
