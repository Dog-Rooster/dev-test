import cron from 'node-cron';
import shell from 'shelljs';

// Schedule tasks to be run on the server.
cron.schedule('* * * * *', function() {
    console.log('Running cronjobs');
    shell.exec('php artisan schedule:run >nul 2>&1');
});
