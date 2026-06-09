<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SportMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'external_id',
        'scores_game_id',
        'tournament_id',
        'team_one_name',
        'team_one_name_ar',
        'team_one_flag',
        'team_two_name',
        'team_two_name_ar',
        'team_two_flag',
        'team_one_score',
        'team_two_score',
        'match_time',
        'match_date',
        'is_live',
        'is_world_cup',
        'stream_url',
        'channel_id',
        'is_active',
        'sort_order',
        'match_details',
    ];

    protected $casts = [
        'tournament_id' => 'integer',
        'team_one_score' => 'integer',
        'team_two_score' => 'integer',
        'match_date' => 'date',
        'is_live' => 'boolean',
        'is_world_cup' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'match_details' => 'array',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
