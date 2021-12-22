<?php

namespace App\Mail;

use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\TeamInvitation as TeamInvitationModel;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     *
     * @var TeamInvitationModel
     */
    public $invitation;

    public string $secret;

    /**
     * Create a new message instance.
     *
     * @param  TeamInvitationModel  $invitation
     * @return void
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $this->invitation = $invitation;
        $this->secret = SiteSetting::first()->registration_secret;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appName = config('app.name');

        return $this->markdown('jetstream::mail.team-invitation', [
            'appName' => $appName,
            'secret' => $this->secret,
        ]
        )->subject(__(' Invitation to '.$appName));
    }
}
