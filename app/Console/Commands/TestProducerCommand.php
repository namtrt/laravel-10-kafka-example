<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;

class TestProducerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:test-producer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kafka producer test';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): void
    {
        Kafka::publishOn('test-topic')
            ->withKafkaKey(Str::uuid())
            ->withBodyKey('foo', 'bar')
            ->withHeaders([
                'foo-header' => 'foo-value'
            ])
            ->send();
    }
}
