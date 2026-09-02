<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/**
 * Private User Channel
 * Used for specific user notifications (The "Bell" alerts).
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Basic Security: The ID in the channel name must match the authenticated User's UUID
    return (string) $user->id === (string) $id;
});

/**
 * Private Organization Channel (Future Proofing)
 * Use this if you want to broadcast an alert to EVERYONE currently online in a Bank or Supplier org.
 */
Broadcast::channel('organization.{orgId}', function ($user, $orgId) {
    return (string) $user->organization_id === (string) $orgId;
});
