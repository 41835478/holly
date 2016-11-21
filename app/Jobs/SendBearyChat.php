<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use ElfSundae\BearyChat\Message;

class SendBearyChat implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The BearyChat client.
     *
     * @var \ElfSundae\BearyChat\Client
     */
    protected $client;

    /**
     * The Message instance to be sent.
     *
     * @var \ElfSundae\BearyChat\Message
     */
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $message  A Message instance, or parameters which can be handled
     *                          by the `send` method of a Message instance.
     */
    public function __construct($message = null)
    {
        if ($message instanceof Message) {
            $this->message = $message;
        } elseif (is_string($message)) {
            $this->text($message);

            if (func_num_args() > 1) {
                if (is_bool(func_get_arg(1))) {
                    $this->markdown(func_get_arg(1));

                    if (func_num_args() > 2) {
                        $this->notification(func_get_arg(2));
                    }
                } else {
                    call_user_func_array([$this, 'add'], array_slice(func_get_args(), 1));
                }
            }
        }
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->client) {
            $this->client->sendMessage($this->message);
        } elseif ($this->message) {
            $this->message->send();
        }
    }

    /**
     * Set the client with client name.
     *
     * @param  string  $name
     * @return $this
     */
    public function client($name)
    {
        $this->client = bearychat($name);

        return $this;
    }

    /**
     * Any unhandled methods will be sent to the Message instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (! $this->message) {
            $this->message = new Message($this->client ?: bearychat());
        }

        call_user_func_array([$this->message, $method], $parameters);

        return $this;
    }
}
