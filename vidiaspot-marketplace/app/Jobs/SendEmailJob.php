<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            // Extract email data
            $to = $this->data['to'] ?? '';
            $subject = $this->data['subject'] ?? 'No Subject';
            $template = $this->data['template'] ?? 'emails.default';
            $variables = $this->data['variables'] ?? [];

            // Send email
            Mail::send($template, $variables, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            Log::info("Email sent successfully to: {$to}");
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            $this->fail($e);
        }
    }
}