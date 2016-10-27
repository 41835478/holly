<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserVIPChanged
{
    use SerializesModels;

    /**
     * The user.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The old `vip_expired_at`.
     *
     * @var \Carbon\Carbon|null
     */
    public $from;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $from)
    {
        $this->user = $user;
        $this->from = $from;
    }
}
