<?php
declare(strict_types = 1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }


    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.forgotPwdMail')
            ->with(['token' => $this->token]);
    }
}
