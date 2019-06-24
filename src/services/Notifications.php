<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird\services;

use creativeorange\messagebird\Messagebird;

use Craft;
use craft\base\Component;
use creativeorange\messagebird\models\Notification as NotificationModel;
use creativeorange\messagebird\records\Notification as NotificationRecord;
use MessageBird\Objects\Message;
use MessageBird\Objects\Recipient;
use needletail\needletail\events\BucketEvent;
use needletail\needletail\records\BucketRecord;

/**
 * Notifications Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 */
class Notifications extends Component
{
    // Public Methods
    // =========================================================================

    public function getAll($criteria = null)
    {
        $query = $this->_getQuery();

        if ( $criteria && is_array($criteria))
            Craft::configure($query, $criteria);

        $results = [];

        foreach  ($query->all() as $key => $value) {
            $results[$key] = $this->_createModelFromRecord($value);
        }

        return $results;
    }
    
    public function getById($id)
    {
        $result = $this->_getQuery()
            ->where(['id' => $id])
            ->one();

        return $this->_createModelFromRecord($result);
    }


    public function deleteById($notificationId)
    {
        $model = $this->getById($notificationId);

        if ($model->id) {
            Craft::$app->getDb()->createCommand()
                ->delete(NotificationRecord::tableName(), ['id' => $model->id])
                ->execute();
        }

        return true;
    }

    public function save(NotificationModel $model, $runValidation = true)
    {
        $isNewModel = !$model->id;

        if (!$record = NotificationRecord::findOne($model->id)) {
            $record = new NotificationRecord();
        }

        $record->title = $model->title;
        $record->senderClass = $model->senderClass;
        $record->eventName = $model->eventName;
        $record->recipient = $model->recipient;
        $record->message = $model->message;
        $record->enableConditional = $model->enableConditional;
        $record->conditionTemplate = $model->conditionTemplate;

        if ($runValidation && !$record->validate()) {
            Craft::info('Notification not saved due to validation error.', __METHOD__);
            $model->addErrors($record->getErrors());
            return false;
        }

        $record->save(false);

        if (!$model->id) {
            $model->id = $record->id;
        }

        return true;
    }

    public function sendNotificationWithMessage(NotificationModel $notification, $message)
    {
        $MessageBird = new \MessageBird\Client(Messagebird::$plugin->getSettings()->getApiKey());

        $sms = new Message();
        $sms->originator = Messagebird::$plugin->getSettings()->getOriginator();
        $sms->recipients[] = $notification->recipient;
        $sms->body = $message;
        $sms->datacoding = 'unicode';

        try {
            $messageResult = $MessageBird->messages->create($sms);
        } catch (\MessageBird\Exceptions\AuthenticateException $e) {
        } catch (\MessageBird\Exceptions\BalanceException $e) {
        } catch (\Exception $e) {
        }
    }


    // Private Methods
    // =========================================================================

    private function _getQuery()
    {
        return NotificationRecord::find()
            ->select(['*']);
    }

    private function _createModelFromRecord(NotificationRecord $record = null)
    {
        if (!$record) {
            return null;
        }

        $attributes = $record->toArray();

        return new NotificationModel($attributes);
    }
}
