<?php

namespace App\Helpers;

use App\Events\MobileQueueStatusUpdated;
use InvalidArgumentException;
use Exception;

class BroadcastMobileHelper
{
    /**
     * Broadcast update event for mobile queue.
     *
     * @param string $type      The type of queue (appointment, direct_queue, appointment_onsite)
     * @param mixed  $data      Eloquent model or array
     * @return void
     *
     * @throws \InvalidArgumentException|\Exception
     */
    public static function mobileQueueUpdate(string $type, $data): void
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            $payload = $data->toArray();
            $clientId = $data->client_id ?? null;
        } elseif (is_array($data)) {
            $payload = $data;
            $clientId = $data['client_id'] ?? null;
        } else {
            throw new InvalidArgumentException("Data must be Eloquent model or array");
        }

        if (!$clientId) {
            throw new Exception("client_id could not be found");
        }

        event(new MobileQueueStatusUpdated(
            $type,
            $payload,
            $clientId
        ));
    }
}
