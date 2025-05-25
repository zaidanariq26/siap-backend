<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
	use Queueable, SerializesModels;

	public $resetLink;
	public $name;

	/**
	 * Create a new message instance.
	 */
	public function __construct($resetLink, $name)
	{
		$this->resetLink = $resetLink;
		$this->name = $name;
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(subject: "Reset Password Mail");
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(markdown: "mail.reset-password-mail");
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
