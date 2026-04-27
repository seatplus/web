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

namespace Seatplus\Web\Services\Controller;

use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

class CreateDispatchTransferObject
{
    private bool $isCharacter = true;

    public static function new(): CreateDispatchTransferObject
    {
        return new self;
    }

    public function create(string $class): DispatchTransferObject
    {
        return match ($class) {
            Contract::class => new DispatchTransferObject(
                'contract',
                $this->getPermission(Contract::class),
                $this->getRequiredScopes('contracts'),
                null
            ),
            Asset::class => new DispatchTransferObject(
                'assets',
                $this->getPermission(Asset::class),
                $this->getRequiredScopes('assets'),
                null
            ),
            WalletJournal::class => new DispatchTransferObject(
                $this->isCharacter() ? 'wallet' : 'corporation.wallet',
                $this->getPermission(WalletJournal::class),
                $this->isCharacter() ? $this->getRequiredScopes('wallet') : [...$this->getRequiredScopes('wallet'), 'esi-characters.read_corporation_roles.v1'],
                $this->isCharacter() ? null : ['Accountant', 'Junior_Accountant']
            ),
            Contact::class => new DispatchTransferObject(
                'contacts',
                $this->getPermission(Contact::class),
                $this->getRequiredScopes('contacts'),
                null
            ),
            CorporationMemberTracking::class => new DispatchTransferObject(
                'membertracking',
                $this->getPermission(CorporationMemberTracking::class),
                $this->getRequiredScopes('membertracking'),
                ['Director']
            ),
            Skill::class => new DispatchTransferObject(
                'skills',
                $this->getPermission(Skill::class),
                $this->getRequiredScopes('skills'),
                null
            ),
            Mail::class => new DispatchTransferObject(
                'mails',
                $this->getPermission(Mail::class),
                $this->getRequiredScopes('mails'),
                null
            ),
            default => throw new \InvalidArgumentException("Unsupported class: {$class}"),
        };
    }

    private function getPermission(string $class): ?string
    {
        return config(sprintf('eveapi.permissions.%s', $class));
    }

    private function getRequiredScopes(string $scope): ?array
    {
        return config(
            sprintf(
                'eveapi.scopes.%s.%s',
                $this->isCharacter() ? 'character' : 'corporation',
                $scope
            )
        );
    }

    public function setIsCharacter(bool $isCharacter): CreateDispatchTransferObject
    {
        $this->isCharacter = $isCharacter;

        return $this;
    }

    private function isCharacter(): bool
    {
        return $this->isCharacter;
    }
}
