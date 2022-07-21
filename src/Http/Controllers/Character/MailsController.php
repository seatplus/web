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
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Mails\EveMailService;

class MailsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = $this->getDispatchTransferObject();

        $ids = $this->getCharacterIds($dispatchTransferObject, 'mails');

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
        $dispatchTransferObject = $this->getDispatchTransferObject();

        $mail = Mail::query()
            ->with(['recipients'])
            ->when(
                ! auth()->user()->can('superuser'),
                fn (Builder $query) => $query->whereHas('recipients', fn (Builder $query) => $query->whereHasMorph(
                    'receivable',
                    CharacterInfo::class,
                    fn (Builder $query) => $this->joinAffiliated(
                        $query,
                        'character_infos',
                        'character_id',
                        $dispatchTransferObject
                    )
                ))
            )
            ->firstWhere('id', $mail_id);

        abort_unless($mail, 404, 'Mail not found');

        return EveMailService::make($mail)->getThreads();
    }

    private function getDispatchTransferObject(): object
    {
        return CreateDispatchTransferObject::new()->create(Mail::class);
    }
}
