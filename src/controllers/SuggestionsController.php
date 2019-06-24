<?php

namespace creativeorange\messagebird\controllers;

use Craft;
use craft\web\Controller;
use creativeorange\messagebird\MessagebirdHelper;
use yii\web\Response;

class SuggestionsController extends Controller
{
    /**
     * Returns the available sender classes.
     */
    public function actionClasses(): Response
    {
        return $this->asJson([
            'classes' => MessagebirdHelper::classSuggestions(),
        ]);
    }

    /**
     * Returns the available events for a component class.
     */
    public function actionEvents(): Response
    {
        $senderClass = Craft::$app->getRequest()->getRequiredBodyParam('senderClass');

        return $this->asJson([
            'events' => MessagebirdHelper::eventSuggestions($senderClass),
        ]);
    }
}