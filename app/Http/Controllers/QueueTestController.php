<?php

namespace App\Http\Controllers;

use App\Jobs\QueueItemJob;

class QueueTestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function sendToBulkQueue() {
        $item = json_decode('{
            "fooBar": "abc-123",
            "baz": false,
            "bazBar": 1
        }');

        $job = new QueueItemJob($item);

        \Queue::connection('rabbitmq_bulk')->push($job);
    }

    public function sendToFastQueue() {
        $item = json_decode('{ "Testing" : "Test data received" }');
        $job = (new QueueItemJob($item))
            ->onConnection('rabbitmq_fast')
            ->onQueue('fast');

        dispatch($job);
    }
}
