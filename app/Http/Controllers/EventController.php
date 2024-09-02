<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    protected GoogleCalendarService $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
    }
}
