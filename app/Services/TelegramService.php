<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;

    protected string $chatId;

    protected bool $enabled;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token', '');
        $this->chatId = config('telegram.chat_id', '');
        $this->enabled = config('telegram.enabled', true) && $this->botToken && $this->chatId;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Send a message to Telegram
     */
    public function send(string $message, ?string $chatId = null, string $parseMode = 'HTML'): bool
    {
        if (! $this->enabled) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chatId ?? $this->chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if (! $response->successful()) {
                Log::error('Telegram send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram send exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Send a game started notification
     */
    public function notifyGameStarted(string $roomCode, int $playerCount, array $playerNames = []): bool
    {
        $playersText = $playerCount > 0 ? implode(', ', array_slice($playerNames, 0, 5)) : '';
        if (count($playerNames) > 5) {
            $playersText .= ' +'.(count($playerNames) - 5).' more';
        }

        $message = "🎮 <b>New Game Started!</b>\n\n"
            ."🏠 Room: <code>{$roomCode}</code>\n"
            ."👥 Players: {$playerCount}\n";

        if ($playersText) {
            $message .= "📝 Names: {$playersText}\n";
        }

        $message .= "\n⏰ ".now()->format('Y-m-d H:i:s');

        return $this->send($message);
    }

    /**
     * Send a Sentry error alert
     */
    public function notifySentryError(array $data): bool
    {
        $title = $data['event']['title'] ?? 'Unknown Error';
        $project = $data['project_name'] ?? 'mulatham';
        $url = $data['url'] ?? '';
        $level = $data['level'] ?? 'error';
        $culprit = $data['culprit'] ?? '';

        $levelEmoji = match ($level) {
            'fatal' => '💀',
            'error' => '🔴',
            'warning' => '⚠️',
            'info' => 'ℹ️',
            default => '❗',
        };

        $message = "{$levelEmoji} <b>Sentry Alert</b>\n\n"
            ."📦 Project: {$project}\n"
            ."🏷 Level: {$level}\n"
            .'📝 Error: <code>'.htmlspecialchars(substr($title, 0, 200))."</code>\n";

        if ($culprit) {
            $message .= '📍 Location: <code>'.htmlspecialchars(substr($culprit, 0, 100))."</code>\n";
        }

        if ($url) {
            $message .= "\n🔗 <a href=\"{$url}\">View in Sentry</a>";
        }

        return $this->send($message);
    }

    /**
     * Send daily statistics report
     */
    public function sendDailyReport(array $stats): bool
    {
        $message = '📊 <b>Daily Report - '.now()->format('Y-m-d')."</b>\n\n"
            ."🎮 <b>Games</b>\n"
            ."   • Created today: {$stats['games_today']}\n"
            ."   • Finished today: {$stats['games_finished_today']}\n"
            ."   • Currently active: {$stats['games_active']}\n\n"
            ."👥 <b>Players</b>\n"
            ."   • Joined today: {$stats['players_today']}\n"
            ."   • Total all time: {$stats['players_total']}\n\n"
            ."❓ <b>Questions</b>\n"
            ."   • Asked today: {$stats['questions_today']}\n"
            ."   • Total all time: {$stats['questions_total']}\n\n"
            ."💡 <b>Suggestions</b>\n"
            ."   • Pending review: {$stats['suggestions_pending']}\n"
            ."   • Total approved: {$stats['suggestions_approved']}\n\n"
            ."📈 <b>Totals</b>\n"
            ."   • Total rooms: {$stats['rooms_total']}\n"
            ."   • Total games played: {$stats['games_total']}\n";

        return $this->send($message);
    }
}
