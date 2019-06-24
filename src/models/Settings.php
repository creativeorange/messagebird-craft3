<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird\models;

use craft\behaviors\EnvAttributeParserBehavior;
use creativeorange\messagebird\Messagebird;

use Craft;
use craft\base\Model;

/**
 * Messagebird Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apiKey;
    public $originator;
    public $debugMode;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['apiKey', 'string'],
            ['originator', 'string']
        ];
    }

    public function behaviors()
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => ['apiKey', 'originator'],
            ],
        ];
    }

    public function getApiKey ()
    {
        return Craft::parseEnv($this->apiKey);
    }
    public function getOriginator ()
    {
        return Craft::parseEnv($this->originator);
    }
}
