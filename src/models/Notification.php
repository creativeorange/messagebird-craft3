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

use creativeorange\messagebird\Messagebird;

use Craft;
use craft\base\Model;

/**
 * Notification Model
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
class Notification extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $id;
    public $title;
    public $senderClass;
    public $eventName;
    public $recipient;
    public $message;
    public $enableConditional;
    public $conditionTemplate;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

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
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
