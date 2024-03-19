<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailCampaign extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        $qrCodePath = $this->data['qrCodePath'] ?? null;

        if (!blank($qrCodePath)) {
            $qrFile = storage_path($qrCodePath);

            if (file_exists($qrFile)) {
                // Define the attachment with a content ID
                $attachment = Attachment::fromPath($qrFile)
                    ->as('qr_code_image')
                    ->withMime('image/png');

                // Remove the image file after attaching
                unlink($qrFile);

                $attachments[] = $attachment;
            }
        }

        return $attachments;
    }
}
