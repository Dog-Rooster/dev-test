<?php

namespace App\Http\Controllers;

use App\Mail\EventConfirmationMail;
use App\Models\Booking;
use App\Models\Event;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Services\BookingService;
use App\Services\CommonService;
use App\Services\IcsGeneratorService;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    protected $commonService;
    protected $bookingRepository;
    protected $eventRepository;

    public function __construct(
        CommonService $commonService,
        BookingRepositoryInterface $bookingRepository,
        EventRepositoryInterface $eventRepository,
    )
    {
        $this->commonService = $commonService;
        $this->bookingRepository = $bookingRepository;
        $this->eventRepository = $eventRepository;

    }

    public function index()
    {
        $bookings = $this->bookingRepository->all();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, $eventId)
    {
        $event = $this->eventRepository->find($eventId);
        $bookingResult = $this->bookingRepository->bookEvent($request, $event);
        if($bookingResult->get('result')){
            $booking = $bookingResult->get('booking');
            return view('bookings.thank-you', compact('booking'));
        }
        else{
            $errorMessage = $bookingResult->get('message');
            return view('bookings.error', compact('errorMessage'));
        }

    }

    public function create(Request $request, $eventId)
    {
        $event = $this->eventRepository->find($eventId);
        $selectedDate = $request->input('booking_date', now()->toDateString());
        $selectedTimeZone = $request->input('booking_timezone', 'America/New_York');

        $timeSlots = $this->commonService->generateTimeSlots($selectedDate, $selectedTimeZone);
        $timeZones = $this->commonService->generateTimeZones();

        return view('bookings.calendar', compact('event', 'timeSlots', 'selectedDate', 'selectedTimeZone', 'timeZones'));
    }
}
