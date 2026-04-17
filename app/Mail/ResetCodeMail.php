<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $code;

    /**
     * Tạo mail với mã xác nhận
     */
    public function __construct($code)
    {
        $this->code=$code;
    }

    /**
     * Tiêu đề email
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã xác nhận đặt lại mật khẩu',
        );
    }

    /**
     * Nội dung mail (view + dữ liệu)
     */
    public function content(): Content
    {
        return new Content(
        view: 'emails.reset_code',
        with: [
            'code' => $this->code
        ],
    );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
