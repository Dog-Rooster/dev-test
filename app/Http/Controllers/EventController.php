<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\GoogleCalendarService;


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
