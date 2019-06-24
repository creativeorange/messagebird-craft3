<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird;

use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use creativeorange\messagebird\models\Notification;
use creativeorange\messagebird\services\Notifications as NotificationsService;
use creativeorange\messagebird\variables\MessagebirdVariable;
use creativeorange\messagebird\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use Twig\Error\Error;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 *
 * @property  NotificationsService $notifications
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Messagebird extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Messagebird::$plugin
     *
     * @var Messagebird
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Messagebird::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['messagebird/notifications'] = 'messagebird/notifications/index';
                $event->rules['messagebird/notifications/new'] = 'messagebird/notifications/edit';
                $event->rules['messagebird/notifications/<notificationId:\d+>'] = 'messagebird/notifications/edit';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('messagebird', MessagebirdVariable::class);
            }
        );

        $notifications = [];
        try {
            $notifications = $this->notifications->getAll();
        } catch (\Throwable $exception) {}

        foreach ($notifications as $notification) {
            /** @var Notification $notification */
            Event::on($notification->senderClass, $notification->eventName, function(Event $e) use ($notification) {
                Craft::$app->getView()->twig->disableStrictVariables();
                $mustSend = false;
                if ( ! $notification->enableConditional ) {
                    $mustSend = true;
                } else {
                    $condition = Craft::$app->getView()->renderString($notification->conditionTemplate, ArrayHelper::toArray($e));
                    $trimmed = trim(preg_replace('/\s\s+/','', $condition));

                    $mustSend = boolval($trimmed);
                }

                if ( ! $mustSend )
                    return;

                $message = '';
                $message = trim(Craft::$app->getView()->renderString($notification->message, ArrayHelper::toArray($e) ));
                
                $this->notifications->sendNotificationWithMessage($notification, $message);
            });
        }

    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'messagebird/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * Returns the CP nav item definition for this plugin’s CP section, if it has one.
     *
     * @return array|null
     * @see PluginTrait::$hasCpSection
     * @see Cp::nav()
     */
    public function getCpNavItem()
    {
        $ret = parent::getCpNavItem();
        $ret['label'] = 'Messagebird';

        return $ret;
    }
}
