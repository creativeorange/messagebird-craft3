<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird\controllers;

use creativeorange\messagebird\Messagebird;

use Craft;
use craft\web\Controller;
use creativeorange\messagebird\models\Notification as NotificationModel;
use needletail\needletail\models\BucketModel;
use needletail\needletail\Needletail;

/**
 * Notifications Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 */
class NotificationsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
//    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/messagebird/notifications
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $variables['notifications'] = Messagebird::$plugin->notifications->getAll();

        return $this->renderTemplate('messagebird/notifications/index', $variables);
    }

    /**
     * Show the create or edit form for a notification
     *
     * @param null $notificationId
     * @return \yii\web\Response
     */
    public function actionEdit($notificationId = null)
    {
        $variables = [];

        if ($notificationId) {
            $variables['notification'] = Messagebird::$plugin->notifications->getById($notificationId);
        } else {
            $variables['notification'] = new NotificationModel();
        }

        return $this->renderTemplate('messagebird/notifications/_edit', $variables);
    }

    public function actionSave()
    {
        $model = $this->_getModelFromPostRequest();

        if (!Messagebird::$plugin->notifications->save($model)) {
            Craft::$app->getSession()->setError(Craft::t('needletail', 'Unable to save notication.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'notication' => $model,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('messagebird', 'Notification saved.'));

        return $this->redirectToPostedUrl($model);
    }

    /**
     * Delete a notification
     *
     * @return \yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDelete()
    {
        $request = Craft::$app->getRequest();

        $this->requirePostRequest();

        $notificationId = $request->getRequiredBodyParam('id');

        Messagebird::$plugin->notifications->deleteById($notificationId);

        return $this->asJson(['success' => true]);
    }

    // Private Methods
    // =========================================================================
    private function _getModelFromPostRequest()
    {
        $request = Craft::$app->getRequest();

        $this->requireCpRequest();
        $this->requirePostRequest();

        if ($request->getBodyParam('notificationId')) {
            $notification = Messagebird::$plugin->notifications->getById($request->getBodyParam('notificationId'));
        } else {
            $notification = new NotificationModel();
        }

        $params = [
            'title', 'senderClass', 'eventName', 'recipient', 'message', 'enableConditional', 'conditionTemplate'
        ];

        foreach ( $params as $param )
        {
            $fromRequest = $request->getBodyParam($param);
            $notification->{$param} = $fromRequest;
        }

        return $notification;
    }
}
