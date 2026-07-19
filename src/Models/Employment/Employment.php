<?php

declare(strict_types=1);

namespace Seatplus\Web\Models\Employment;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Enums\EmploymentStatus;

class Employment extends Model
{
    use HasFactory;

    /** @var array<string> */
    protected $guarded = [];

    /**
     * The observed subject: a whole User (account-wide) or a single CharacterInfo, mirroring the
     * application's applicationable.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function corporation(): BelongsTo
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id');
    }

    /**
     * The application this hire originated from. Null for pre-existing members reconciled from
     * corporation membership.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', EmploymentStatus::Active);
    }

    #[Scope]
    protected function alumni(Builder $query): Builder
    {
        return $query->where('status', EmploymentStatus::Alumni);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'corporation_id' => 'integer',
            'status' => EmploymentStatus::class,
            'hired_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }
}
