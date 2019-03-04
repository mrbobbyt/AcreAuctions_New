<?php
declare(strict_types = 1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $clientUrl;
    protected $token;

    public function __construct(string $clientUrl, string $token)
    {
        $this->clientUrl = $clientUrl;
        $this->token = $token;
    }


    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.registerMail')
            ->with([
                'clientUrl' => $this->clientUrl,
                'token' => $this->token
            ]);
    }
}
