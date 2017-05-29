<?php

namespace App\Jobs;

use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

class QueueItemJob extends Job
{
    protected $data;

    /**
     * Create a new job instance.
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->data = $payload;
    }

    /**
     * Execute the job.
     *
     * @param RabbitMQJob $job
     * @return void
     */
    public function handle(RabbitMQJob $job)
    {
        $job->delete();
    }
}
