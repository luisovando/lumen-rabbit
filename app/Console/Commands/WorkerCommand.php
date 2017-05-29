<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class WorkerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'worker:receiver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume queue messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        /**
         * Specifies QoS
         * Don't dispatch a new message to a worker until it has processed and
         * acknowledged the previous one. Instead, it will dispatch it to the
         * next worker that is not still busy.
         *
         * @param int $prefetch_size Prefetch window size in octets, null meaning "no specific limit"
         * @param int $prefetch_count Prefetch window in terms of whole messages
         * @param bool $a_global If is NULL to mean that the QoS settings should apply per-consumer,
         *                             but if is TRUE to mean that the QoS settings should apply per-channel
         * @return mixed
         */
        $channel->basic_qos(
            null,
            1,
            null
        );

        /**
         * Starts a queue consumer
         *
         * Indicate interest in consuming messages from a particular queue. When they do
         * so, we say that they register a consumer or, simply put, subscribe to a queue.
         * Each consumer (subscription) has an identifier called a consumer tag
         *
         * @param string $queue Queue name
         * @param string $consumer_tag Identifier for the consumer, valid within the current channel. just string
         * @param bool $no_local If true, the server will not send messages to the connection that published them
         * @param bool $no_ack If false acks turned on, else off. Send a proper acknowledgment from the
         *                                      worker, once we're done with a task
         * @param bool $exclusive Queues may only be accessed by the current connection
         * @param bool $nowait If true the server will not respond to the method.
         *                                      The client should not wait for a reply method
         * @param callback|null $callback
         * @param int|null $ticket
         * @param array $arguments
         * @return mixed|string
         */
        $channel->basic_consume(
            'bulk',
            '',
            false,
            false,
            false,
            false,
            array($this, 'process')
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * Process received request
     * @param AMQPMessage $message
     */
    public function process(AMQPMessage $message)
    {
        echo "[x] Received " . $message->body . "\n";
        sleep(rand(5, 10));
        echo "[x] Done \n";

        /**
         * If a consumer dies without sending an acknowledgement the AMQP broker
         * will redeliver it to another consumer or, if none are available at the
         * time, the broker will wait until at least one consumer is registered
         * for the same queue before attempting redelivery
         */
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

}