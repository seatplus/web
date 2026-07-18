<?php

declare(strict_types=1);

namespace Seatplus\Web\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;

class EnlistmentReviewRound extends Model
{
    use HasFactory;

    /** @var array<string> */
    protected $guarded = [];

    public function enlistment(): BelongsTo
    {
        return $this->belongsTo(Enlistments::class, 'corporation_id', 'corporation_id');
    }

    /**
     * The control-group whose members review this round. Null means the round falls back to
     * the flat 'can accept or deny applications' permission (legacy behaviour).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'corporation_id' => 'integer',
            'position' => 'integer',
            'role_id' => 'integer',
        ];
    }
}
