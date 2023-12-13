<?php

namespace AmazingTraits\Events;

use AmazingTraits\Helpers\Cryptor;
use AmazingTraits\Traits\ModelTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ModelEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $action;
    public $model;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct($model, $data, $lifecycle)
    {
        $this->model = $model;
        $this->data = json_decode(json_encode($data));

        if ($lifecycle === 'onCreated') {
            $this->action = 'created';
        } else if ($lifecycle === 'onUpdated') {
            $this->action = 'updated';
        } else if ($lifecycle === 'onSaved') {
            $this->action = 'saved';
        } else if ($lifecycle === 'onDeleted') {
            $this->action = 'deleted';
        }
    }

    public function decrypt(string $encryptedText): mixed
    {
        return (new Cryptor)->decrypt($encryptedText);
    }

    public function autoDecrypt($val)
    {
        return $val === null ? null : (is_numeric($val) && isset(app()->request->encrypt) && app()->request->encrypt === 'false' ?
            $val :
            (is_numeric($val) ? $val : $this->decrypt($val)));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("public-channel"),
            new Channel("public-channel.{$this->data->id}"),
            new PrivateChannel("private-channel"),
            new PrivateChannel("private-channel.{$this->data->id}"),
        ];
    }

    public function broadcastAs()
    {
        return $this->model;
    }
}
