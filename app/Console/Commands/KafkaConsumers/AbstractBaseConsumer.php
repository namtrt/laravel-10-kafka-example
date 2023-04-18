<?php

namespace App\Console\Commands\KafkaConsumers;

use AvroIOException;
use Carbon\Exceptions\Exception;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;
use Junges\Kafka\Message\Deserializers\AvroDeserializer;
use Junges\Kafka\Message\KafkaAvroSchema;
use Junges\Kafka\Message\Registry\AvroSchemaRegistry;

abstract class AbstractBaseConsumer extends Command
{
    /**
     * @return void
     * @throws AvroIOException
     * @throws Exception
     */
    public function handle(): void
    {
        $arrTopic = [$this->getTopic()];
        $deserializer = $this->AVRODeserializer();

        $consumer = Kafka::createConsumer(
            topics: $arrTopic,
            groupId: $this->getGroupId(),
            brokers: $this->getBrokers()
        )->withOption('auto.offset.reset', $this->getOffset())
            ->usingDeserializer($deserializer);

        $handler = $this->handleMessage(...);
        $consumer = $consumer->withHandler(static function (ConsumedMessage $message) use ($handler) {
            $handler($message->getBody());
        })->build();

        $consumer->consume();
    }

    /**
     * @param $message
     * @return void
     */
    abstract protected function handleMessage($message): void;

    /**
     * @return string
     */
    abstract protected function getTopic(): string;

    /**
     * @return string
     */
    abstract protected function getSchemaName(): string;

    /**
     * @return string
     */
    abstract protected function getGroupId(): string;

    /**
     * @return string
     */
    abstract public function getOffset(): string;

    /**
     * @return string
     */
    protected function getBrokers(): string
    {
        return config('kafka.brokers');
    }

    /**
     * @return string
     */
    protected function getBaseUriSerializer(): string
    {
        return config('kafka.base_uri_serializer');
    }

    /**
     * @return AvroDeserializer
     * @throws AvroIOException
     */
    public function AVRODeserializer(): AvroDeserializer
    {
        $cachedRegistry = new CachedRegistry(
            new BlockingRegistry(
                new PromisingRegistry(
                    new Client(['base_uri' => $this->getBaseUriSerializer()])
                )
            ),
            new AvroObjectCacheAdapter()
        );

        $registry = new AvroSchemaRegistry($cachedRegistry);
        $recordSerializer = new RecordSerializer($cachedRegistry);

        $registry->addBodySchemaMappingForTopic(
            $this->getTopic(),
            new KafkaAvroSchema($this->getSchemaName())
        );

        return new AvroDeserializer($registry, $recordSerializer);
    }
}
