# Documentation

# Installation
    # Run these commands
        - composer install
        - php artisan migrate
        - php artisan db:seed
        - npm install
        - npm run dev
        - php artisan serve

    - If thre's a problem installing the libraries. Run these commands
        - php artisan route:clear
        - php artisan view:clear
        - php artisan config:clear
        - php artisan cache:clear
        - composer dump-autoload

# Implemented Libraries
    - spatie/laravel-google-calendar
    - eluceo/ical

    ## spatie/laravel-google-calendar
        - Purpose: Used for integrating Google Calendar API.
        - Setup:
            - Save your credentials.json file to the following directory. If the directory doesn't exist, create it:
                - storage/app/google-calendar
            - Add your Google Calendar ID to the .env file:
                - GOOGLE_CALENDAR_ID=

    ## eluceo/ical
        - Purpose: Used for generating .ics file
                
# Mailer
    - Configuration: Uses Gmail for SMTP. To use a different mail host, update the .env file accordingly.

# Notification
    - Functionality:
        - Notify attendees after booking.
        - Send reminders to attendees 1 hour or less before the event.
    - Execution:
        - Notifications are processed continuously every minute to accommodate event durations measured in minutes.
        - Use Laravel Scheduler for email notifications:
            - php artisan queue:work
            - php artisan schedule:run
        - For live server deployment using Cron Job, add the following to crontab:
            - * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

# Database
    - Added Columns:
        - email_is_sent - Indicates if the email notification has been sent.
        - reminder_sent - Indicates if the reminder has been sent.

#Indexes:
    - Indexed columns in the bookings table for improved search performance:
        - event_id
        - booking_date
        - booking_time
        - email_is_sent
        - reminder_sent

# Cache
    - Purpose: To temporarily store database query results and reduce load on the database.
    - Configuration:
        - Implemented caching for notifications, with a cache duration of up to 1 minute. This balances performance and data freshness.

# Chunk
    - Method: Processes bookings in batches of 100 to manage memory usage and handle large volumes of bookings efficiently.

# Implementation of Best Practices
    - SOLID Principles:
        - Single Responsibility Principle (SRP):
            - Separated responsibilities into distinct classes:
                - BookingService: Manages business logic related to bookings.
                - EventService: Handles event-related logic.
                - TimeSlotService: Generates time slots for bookings.
                - BookingRepository and EventRepository: Manage data access.

            - Open/Closed Principle (OCP):
                - Used service and repository interfaces to allow extension or modification without altering existing code.

            - Liskov Substitution Principle (LSP):
                - Interfaces ensure that any implementation can be used interchangeably, enhancing flexibility.

            - Interface Segregation Principle (ISP):
                -Defined specific interfaces for services and repositories to focus on required operations, reducing unnecessary dependencies.

            - Dependency Inversion Principle (DIP):
                - Services depend on interfaces rather than specific implementations, improving modularity and testability.

# Design Pattern
    Repository Design Pattern
        - The repository pattern is used to abstract the data access logic in Laravel, allowing for a cleaner separation of concerns. 
        - It separates the actual database queries and data operations from the business logic. 
        - This makes the codebase more maintainable, testable, and flexible, as changes in the data layer (e.g., switching databases) don't require modifications to the business logic.
    
        1. Repository Interface
            - The repository interface defines the methods that will be used to interact with the data layer. Each repository will implement its respective interface. 
            - This allows the application to be decoupled from the specific implementation of the data access logic.

        2. Repository Implementations
            - The actual logic for interacting with the database is written in the concrete repository classes that implement the interface. These classes will handle the Eloquent queries.

        3. Binding Repositories to Interfaces in Service Providers
            - In order for Laravel to use the repository classes, we need to bind the interfaces to their corresponding implementations. 
            - This is typically done in a service provider, such as AppServiceProvider.

        4. Using the Repository in Services
            - The BookingService and EventService classes rely on these repositories to handle data operations. 
            - The services do not directly interact with the database or the models, but rather, they call methods on the repository, adhering to the repository interface.