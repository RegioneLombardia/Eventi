<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220527_115000_add_column_internal_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_internal_list', 'sent_at', $this->dateTime()->comment('Sent at')->after('mailup_sending_id'));
        $this->addColumn('event_internal_list', 'mailup_stats_number_comunication_views', $this->integer()->comment('Mailup n views')->after('mailup_sending_id'));
        $this->addColumn('event_internal_list', 'mailup_stats_number_comunication_clicks', $this->integer()->comment('Mailup n clicks')->after('mailup_sending_id'));
        $this->addColumn('event_internal_list', 'mailup_stats_number_comunication_bounces', $this->integer()->comment('Mailup n bounce')->after('mailup_sending_id'));
        $this->addColumn('event_internal_list', 'mailup_error_message', $this->integer()->comment('Mailup n bounce')->after('mailup_sending_id'));

        $this->addColumn('event_communication', 'mailup_stats_number_comunication_views', $this->integer()->comment('Mailup n views')->after('mailup_sending_id'));
        $this->addColumn('event_communication', 'mailup_stats_number_comunication_clicks', $this->integer()->comment('Mailup n clicks')->after('mailup_sending_id'));
        $this->addColumn('event_communication', 'mailup_stats_number_comunication_bounces', $this->integer()->comment('Mailup n bounce')->after('mailup_sending_id'));
        $this->addColumn('event_communication', 'mailup_error_message', $this->integer()->comment('Mailup n bounce')->after('mailup_sending_id'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_internal_list', 'sent_at');
        $this->dropColumn('event_internal_list', 'mailup_stats_number_comunication_views');
        $this->dropColumn('event_internal_list', 'mailup_stats_number_comunication_clicks');
        $this->dropColumn('event_internal_list', 'mailup_stats_number_comunication_bounces');
        $this->dropColumn('event_internal_list', 'mailup_error_message');


        $this->dropColumn('event_communication', 'mailup_stats_number_comunication_views');
        $this->dropColumn('event_communication', 'mailup_stats_number_comunication_clicks');
        $this->dropColumn('event_communication', 'mailup_stats_number_comunication_bounces');
        $this->dropColumn('event_communication', 'mailup_error_message');
    }

}
