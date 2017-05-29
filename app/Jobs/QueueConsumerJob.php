<?php

namespace App\Jobs;

use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

class QueueConsumerJob extends Job
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @param RabbitMQJob $job
     * @return void
     */
    public function handle(RabbitMQJob $job)
    {
        dd($job);
    }
}
