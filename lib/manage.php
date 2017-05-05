<?php

function getManagedUIDs($manageruid) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "getmanaged",
            'uid' => $manageruid
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['employees'];
    } else {
        return [];
    }
}
