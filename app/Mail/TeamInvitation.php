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

    public TeamInvitationModel $invitation;

    public string $secret;

    public string $appName;

    /**
     * Create a new message instance.
     *
     * @param  TeamInvitationModel  $invitation
     * @return void
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $siteSetting = SiteSetting::first();

        $this->invitation = $invitation;
        $this->secret = $siteSetting->registration_secret;
        $this->appName = $siteSetting->title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('jetstream::mail.team-invitation', [
            'appName' => $this->appName,
            'secret' => $this->secret,
        ]
        )->subject(__(' Invitation to '.$this->appName));
    }
}
