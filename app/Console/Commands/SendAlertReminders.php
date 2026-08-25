<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class SendAlertReminders extends Command
{
    protected $signature = 'alerts:send-reminders';

    protected $description = 'Send Telegram reminders for alerts: 1-day-before pings, a one-shot ping when it starts and when it\'s due, and the repeating nag while an alert is active or overdue';

    public function handle(): int
    {
        if (blank(config('services.telegram.bot_token')) || blank(config('services.telegram.chat_id'))) {
            $this->warn('Telegram bot_token/chat_id not configured — skipping.');
            return self::SUCCESS;
        }

        $alerts = Alert::query()
            ->where('status', 'pending')
            ->get();

        $sent = 0;

        foreach ($alerts as $alert) {
            if ($alert->isSnoozed()) {
                continue;
            }

            // Heads-up pings are automatic now — no per-alert opt-in.
            if ($alert->start_date->isTomorrow() && ! $this->alreadySentForOccurrence($alert, 'before_start')) {
                $sent += (int) $this->send($alert, 'before_start', $alert->telegramMessage('before_start'));
            }

            if ($alert->end_date->isTomorrow() && ! $this->alreadySentForOccurrence($alert, 'before_end')) {
                $sent += (int) $this->send($alert, 'before_end', $alert->telegramMessage('before_end'));
            }

            // One-shot pings the instant start/end actually arrive — independent of
            // the optional repeating nag, so even a short, non-repeating alert
            // still tells you when it begins and when it's due.
            if (now()->gte($alert->start_date) && ! $this->alreadySentForOccurrence($alert, 'on_start')) {
                $sent += (int) $this->send($alert, 'on_start', $alert->telegramMessage('on_start'));
            }

            if (now()->gte($alert->end_date) && ! $this->alreadySentForOccurrence($alert, 'on_end')) {
                $sent += (int) $this->send($alert, 'on_end', $alert->telegramMessage('on_end'));
            }

            if ($alert->remind_enabled && $alert->remind_interval_minutes && now()->gte($alert->start_date)) {
                $last = $alert->lastLogAt('reminder');
                $due  = ! $last || $last->diffInMinutes(now()) >= $alert->remind_interval_minutes;

                if ($due) {
                    $sent += (int) $this->send($alert, 'reminder',
                        $alert->telegramMessage('reminder', ['overdue' => $alert->isOverdue()]));
                }
            }
        }

        $this->info("Sent {$sent} reminder(s).");
        return self::SUCCESS;
    }

    /**
     * Each one-shot ping type should fire once per occurrence, not once per
     * calendar day — after a repeating alert advances, start_date/end_date
     * move forward, so scoping the dedup check to "since 2 days before the
     * anchor date" naturally re-arms it for the next occurrence without
     * needing to track occurrence numbers separately.
     */
    protected function alreadySentForOccurrence(Alert $alert, string $type): bool
    {
        $anchor = in_array($type, ['before_end', 'on_end'], true) ? $alert->end_date : $alert->start_date;

        return $alert->logs()
            ->where('type', $type)
            ->where('sent_at', '>=', $anchor->copy()->subDays(2))
            ->exists();
    }

    protected function send(Alert $alert, string $type, string $text): bool
    {
        $ok = TelegramNotifier::send($text);
        $alert->log($type, $text, $ok);
        return $ok;
    }
}
