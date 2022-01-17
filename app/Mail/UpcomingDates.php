<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UpcomingDates extends Mailable
{
    use Queueable, SerializesModels;

    protected string $siteName;

    protected Collection $birthDates;

    protected Collection $deathDates;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $siteName, Collection $birthDates, Collection $deathDates)
    {
        $this->siteName = $siteName;
        $this->birthDates = $birthDates;
        $this->deathDates = $deathDates;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.upcoming-dates', [
            'siteName' => $this->siteName,
            'birthDates' => $this->birthDates,
            'deathDates' => $this->deathDates,
        ])->from(config('mail.from.address'), $this->siteName)
            ->subject(__('Upcoming Dates on '.$this->siteName));
    }
}
