<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;


    protected $token;


    /**
     * Create a new message instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
            ->view('emails.forgotPwdMail')
            ->with([
                'token' => $this->token,
            ]);

    }
}
