<?php
namespace App\Http\Controllers;

use App\Contracts\BookingServiceInterface;
use App\Contracts\EventServiceInterface;
use App\Contracts\TimeSlotServiceInterface;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $bookingService;
    protected $eventService;
    protected $timeSlotService;

    public function __construct(
        BookingServiceInterface $bookingService,
        EventServiceInterface $eventService,
        TimeSlotServiceInterface $timeSlotService
    )
    {
        $this->bookingService = $bookingService;
        $this->eventService = $eventService;
        $this->timeSlotService = $timeSlotService;
    }

    public function index()
    {
        $bookings = $this->bookingService->getAllBookings();
        return view('bookings.index', compact('bookings'));
    }

    public function store(StoreBookingRequest $request, $eventId)
    {
        $event = $this->eventService->getEvent($eventId);
        $selectedTimeZone = $request->input('timezone', 'Asia/Manila');
        $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('booking_date') . ' ' . date('H:i',strtotime($request->input('booking_time'))) , $selectedTimeZone);
        $endTime = $bookingDateTime->copy()->addMinutes($event->duration);

        $data = [
            'attendee_name' => $request->input('attendee_name'),
            'attendee_email' => $request->input('attendee_email'),
            'event_id' => $eventId,
            'booking_date' => $request->input('booking_date'),
            'booking_time' => $request->input('booking_time'),
            'timezone' => $selectedTimeZone,
            'event_id' => $event->id
        ];

        DB::beginTransaction();

        try{
            $booking = $this->bookingService->createBooking($data);

            $googleEvent = new GoogleEvent();
            $googleEvent->name = $event->name .' - '. $request->input('attendee_name');
            $googleEvent->startDateTime = $bookingDateTime;
            $googleEvent->endDateTime = $endTime;
            $googleEvent->description = "Booking confirmed with ". $request->input('attendee_name');
            $googleEvent->save();
        
            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            
            \Log::error('Error during booking process: ' . $e->getMessage());
            abort(500);
        }

        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function booking_thankyou($bookingId)
    {
        $booking = $this->bookingService->getBooking($bookingId);
        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function create(Request $request, $eventId)
    {
        $event = $this->eventService->getEvent($eventId);
        $event_duration = $event->duration;
        $selectedDate = $request->input('booking_date', now()->toDateString());
        $selectedTimeZone = $request->input('timezone', 'Asia/Manila');
        $timeZones = DateTimeZone::listIdentifiers();

        $timeSlots = $this->timeSlotService->generateTimeSlots($selectedDate, $event_duration, $event->id, $selectedTimeZone);

        return view('bookings.calendar', compact('event', 'timeSlots', 'selectedDate', 'timeZones', 'selectedTimeZone'));
    }
}