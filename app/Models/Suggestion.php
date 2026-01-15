<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author_name',
        'author_session_id',
        'status',
        'category',
        'votes_count',
        'room_id',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Category constants
    const CATEGORY_FEATURE = 'feature';
    const CATEGORY_BUG = 'bug';
    const CATEGORY_IMPROVEMENT = 'improvement';
    const CATEGORY_OTHER = 'other';

    public function votes(): HasMany
    {
        return $this->hasMany(SuggestionVote::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOrderByVotes($query, $direction = 'desc')
    {
        return $query->orderBy('votes_count', $direction);
    }

    // Helper methods
    public function hasVotedBy(string $sessionId): bool
    {
        return $this->votes()->where('voter_session_id', $sessionId)->exists();
    }

    public function approve(): void
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function reject(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

    public function incrementVotes(): void
    {
        $this->increment('votes_count');
    }

    public function decrementVotes(): void
    {
        $this->decrement('votes_count');
    }

    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_FEATURE => 'ميزة جديدة',
            self::CATEGORY_BUG => 'خطأ تقني',
            self::CATEGORY_IMPROVEMENT => 'تحسين',
            self::CATEGORY_OTHER => 'أخرى',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }
}
