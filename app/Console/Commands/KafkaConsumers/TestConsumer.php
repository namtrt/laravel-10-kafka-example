<?php

namespace App\Console\Commands\KafkaConsumers;

class TestConsumer extends AbstractBaseConsumer
{
    /** @var string */
    protected $signature = 'kafka-consumer:test';

    /** @var string */
    protected $description = 'Kafka consumer test';

    /**
     * @return string
     */
    protected function getTopic(): string
    {
        return config('kafka.topics.test');
    }

    /**
     * @return string
     */
    protected function getSchemaName(): string
    {
        return config('kafka.schema_avro.test');
    }

    /**
     * @return string
     */
    protected function getGroupId(): string
    {
        return config('kafka.consumer_group_id.test');
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return config('kafka.offset_reset.test');
    }

    /**
     * @param $message
     * @return void
     */
    protected function handleMessage($message): void
    {
        dump($message);
    }
}
