<?php

declare(strict_types=1);

namespace Seatplus\Web\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Seatplus\Eveapi\Models\Application;

class ApplicationGroupMember extends Model
{
    use HasFactory;

    /** @var array<string> */
    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }
}
