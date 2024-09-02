<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;
use Illuminate\Http\Request;

interface BookingRepositoryInterface
{
    public function all();
    public function bookEvent(Request $request, Event $event);

}
