<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird\variables;

use creativeorange\messagebird\Messagebird;

use Craft;

/**
 * Messagebird Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.messagebird }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 */
class MessagebirdVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.messagebird.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.messagebird.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
