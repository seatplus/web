<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\CharacterAffiliations\GetOwnedCharacterAffiliationsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\GetRecruitIdsService;
use Seatplus\Web\Services\Mails\EveMailService;

class MailsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = $this->getDispatchTransferObject();

        $ids = $this->getAffiliatedIds($dispatchTransferObject);

        return inertia('Character/Mail/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characterIds' => $ids,
        ]);
    }

    public function mailHeaders(Request $request)
    {
        $character_ids = $request->get('character_ids');

        return Mail::query()
            ->select('id', 'from', 'subject', 'timestamp')
            ->whereHas('recipients', fn (Builder $query) => $query->whereHasMorph(
                'receivable',
                CharacterInfo::class,
                fn (Builder $query) => $query->whereIn('character_id', $character_ids)
            ))
            ->orderByDesc('timestamp')
            ->paginate();
    }

    public function getMail(int $mail_id)
    {
        $permission = data_get($this->getDispatchTransferObject(), 'permission');
        $affiliated_ids = collect([...getAffiliatedIdsByPermission($permission), ...GetRecruitIdsService::get()])->unique();

        $mail = Mail::query()
            ->with(['recipients'])
            ->whereHas('recipients', fn (Builder $query) => $query->whereHasMorph(
                'receivable',
                CharacterInfo::class,
                fn (Builder $query) => $query->whereIn('character_id', $affiliated_ids->toArray())
            ))
            ->firstWhere('id', $mail_id);

        return $mail ? EveMailService::make($mail)->getThreads() : [];
    }

    private function getDispatchTransferObject(): object
    {
        return CreateDispatchTransferObject::new()->create(Mail::class);
    }

    private function getAffiliatedIds(object $dispatchTransferObject): Collection
    {
        $affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user()
        );

        $owned_characters = GetOwnedAffiliatedIdsService::make($affiliations_dto)
            ->getQuery();

        return CharacterInfo::query()
            ->has('mails')
            ->when(
                request()->has('character_ids'),
                fn ($query) => $query->whereIn('character_id', request()->get('character_ids')),
                fn ($query) => $query->joinSub($owned_characters, 'owned_characters', 'owned_characters.affiliated_id', '=', 'character_infos.character_id')
            )->pluck('character_infos.character_id');
    }
}
