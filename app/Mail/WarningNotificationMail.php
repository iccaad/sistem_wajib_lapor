<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarningNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new mailable instance.
     *
     * @param  int  $level  1, 2, or 3 — indicates severity of the warning.
     */
    public function __construct(
        public readonly Participant $participant,
        public readonly int $level,
    ) {}

    /**
     * Get the message envelope (subject line).
     */
    public function envelope(): Envelope
    {
        $levelLabel = match ($this->level) {
            2       => 'Level 2 — Mangkir',
            3       => 'Level 3 — ESKALASI KRITIS',
            default => "Level {$this->level}",
        };

        return new Envelope(
            subject: "⚠️ Peringatan {$levelLabel} - {$this->participant->full_name} | Sistem Wajib Lapor",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.warning-notification',
            with: [
                'participant'  => $this->participant,
                'level'        => $this->level,
                'adminPanelUrl'=> url('/admin/participants/' . $this->participant->id),
            ],
        );
    }
}
