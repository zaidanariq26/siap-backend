<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordMail implements ShouldQueue
{
	use Queueable;

	public $email;
	public $resetLink;
	public $name;

	/**
	 * Create a new job instance.
	 */
	public function __construct($email, $resetLink, $name)
	{
		$this->email = $email;
		$this->resetLink = $resetLink;
		$this->name = $name;
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		Log::info("tes", [
			"link" => $this->resetLink,
		]);
		Mail::to($this->email)->send(new ResetPasswordMail($this->resetLink, $this->name));
		Log::info("Email berhasil dikirim ke: " . $this->email);
	}

	/**
	 * Save log if the job failed
	 *
	 * @param \Exception $exception
	 */
	public function failed(Exception $exception)
	{
		Log::error("Job SendResetPasswordMail gagal: " . $exception->getMessage());
	}
}
