<?php
/**
 * Messagebird plugin for Craft CMS 3.x
 *
 * Sends SMS notifications on specific events
 *
 * @link      https://www.creativeorange.nl
 * @copyright Copyright (c) 2019 Creativeorange
 */

namespace creativeorange\messagebird\migrations;

use creativeorange\messagebird\Messagebird;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Messagebird Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Creativeorange
 * @package   Messagebird
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // messagebird_notifications table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%messagebird_notifications}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%messagebird_notifications}}',
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->string(255)->notNull()->defaultValue(''),
                    'senderClass' => $this->string(255)->notNull()->defaultValue(''),
                    'eventName' => $this->string(255)->notNull()->defaultValue(''),
                    'recipient' => $this->string(255)->notNull()->defaultValue(''),
                    'message' => $this->text(),
                    'enableConditional' => $this->boolean()->defaultValue(0),
                    'conditionTemplate' => $this->text(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // messagebird_notifications table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%messagebird_notifications}}',
                ['senderClass', 'eventName'],
                true
            ),
            '{{%messagebird_notifications}}',
            ['senderClass', 'eventName'],
            false
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // messagebird_notifications table
//        $this->addForeignKey(
//            $this->db->getForeignKeyName('{{%messagebird_notifications}}', 'siteId'),
//            '{{%messagebird_notifications}}',
//            'siteId',
//            '{{%sites}}',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // messagebird_notifications table
        $this->dropTableIfExists('{{%messagebird_notifications}}');
    }
}
