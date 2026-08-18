<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class SendAlertReminders extends Command
{
    protected $signature = 'alerts:send-reminders';

    protected $description = 'Send Telegram reminders for alerts: 1-day-before pings, and the repeating nag while an alert is active or overdue';

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
                $sent += (int) $this->send($alert, 'before_start',
                    "🔔 *{$alert->title}*\nStarts tomorrow — " . $alert->start_date->format('D, d M Y h:i A'));
            }

            if ($alert->end_date->isTomorrow() && ! $this->alreadySentForOccurrence($alert, 'before_end')) {
                $sent += (int) $this->send($alert, 'before_end',
                    "⏰ *{$alert->title}*\nEnds tomorrow — " . $alert->end_date->format('D, d M Y h:i A'));
            }

            if ($alert->remind_enabled && $alert->remind_interval_minutes && now()->gte($alert->start_date)) {
                $last = $alert->lastLogAt('reminder');
                $due  = ! $last || $last->diffInMinutes(now()) >= $alert->remind_interval_minutes;

                if ($due) {
                    $overdueTag = $alert->isOverdue() ? "\n🚨 *OVERDUE*" : '';
                    $sent += (int) $this->send($alert, 'reminder',
                        "🔁 *{$alert->title}*\n{$alert->content}{$overdueTag}");
                }
            }
        }

        $this->info("Sent {$sent} reminder(s).");
        return self::SUCCESS;
    }

    /**
     * A "before_start"/"before_end" ping should fire once per occurrence, not
     * once per calendar day — after a repeating alert advances, start_date
     * moves forward, so scoping the dedup check to "since 2 days before the
     * current start_date" naturally re-arms it for the next occurrence
     * without needing to track occurrence numbers separately.
     */
    protected function alreadySentForOccurrence(Alert $alert, string $type): bool
    {
        $anchor = $type === 'before_end' ? $alert->end_date : $alert->start_date;

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
