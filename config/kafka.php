<?php

return [
    'brokers' => env('KAFKA_BROKERS'),
    'base_uri_serializer' => env('KAFKA_SCHEMA_REGISTRY'),
    'topics' => [
        'test' => env('KAFKA_TOPIC_TEST'),
    ],
    'schema_avro' => [
        'test' => env('KAFKA_SCHEMA_AVRO_TEST'),
    ],
    'consumer_group_id' => [
        'test' => env('KAFKA_CONSUMER_TEST'),
    ],
    'offset_reset' => [
        'test' => env('KAFKA_OFFSET_TEST')
    ],
];
