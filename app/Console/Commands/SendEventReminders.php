<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\EventReminder;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SendEventReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send event reminders 1 hour before the event starts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDateTime = Carbon::now();
        $oneHourLater = $currentDateTime->copy()->addHour();
    
        $cacheKey = "reminders_to_send_{$oneHourLater->toDateTimeString()}";
    
        $bookings = Cache::get($cacheKey);
    
        if (!$bookings) {
            $query = Booking::where(function ($query) use ($oneHourLater, $currentDateTime) {
                $query->where('booking_date', $oneHourLater->toDateString())
                      ->where('booking_time', $oneHourLater->toTimeString())
                      ->where('reminder_sent', false);
            })
            ->orWhere(function ($query) use ($currentDateTime, $oneHourLater) {
                $query->where(function ($query) use ($currentDateTime, $oneHourLater) {
                    $query->whereDate('booking_date', '<=', $oneHourLater->toDateString())
                          ->whereTime('booking_time', '<=', $oneHourLater->toTimeString())
                          ->where('reminder_sent', false);
                });
            });
    
            $bookings = $query->get();
    
            Cache::put($cacheKey, $bookings->toArray(), now()->addMinutes(1)); // Cache for 1 minute
        } else {
            $bookings = collect($bookings);
        }
    
        foreach ($bookings->chunk(100) as $chunk) { 
            foreach ($chunk as $booking) {
                Notification::route('mail', $booking['attendee_email'])
                            ->notify(new EventReminder((object) $booking));
    
                Booking::where('id', $booking['id'])->update(['reminder_sent' => true]);
    
                $this->info('Reminder sent to ' . $booking['attendee_email']);
            }
        }
    
        return Command::SUCCESS;
    }
}

