<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestionVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'suggestion_id',
        'voter_session_id',
    ];

    public function suggestion(): BelongsTo
    {
        return $this->belongsTo(Suggestion::class);
    }
}
