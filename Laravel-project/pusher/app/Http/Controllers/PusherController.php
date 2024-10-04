<?php

namespace App\Http\Controllers;

use App\Events\MakeCallEvent;
use App\Events\SendData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Pusher\Pusher;

class PusherController extends Controller
{
    function trigger()
    {

        event(new SendData('fjfj'));

        Broadcast::channel('test-c', function () {
            return true;
        });
        Broadcast::event('test-e', [
            'message' => 'Hello World!',
        ])->toOthers();


        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ]);
        $response = $pusher->trigger('test-c', 'test-e', 'abc');
        dd($response);


        event(new SendData('fjfj'));

        broadcast(new SendData('hello world'))->toOthers()->via('pusher');
        Broadcast::on('test-c')
            ->as('test-e')
            ->with([
                'message' => 'Hello World!'
            ])
            ->send();
    }


    function makeCall(Request $request)
    {
        event(new MakeCallEvent($request->peer_id));
    }
}
