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

use Seatplus\Eveapi\Jobs\Hydrate\Character\CharacterAssetsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\MailsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\SkillsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\WalletHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

class CreateDispatchTransferObject
{
    private array $transferObjectArray;

    private bool $isCharacter = true;

    public static function new()
    {
        return new static();
    }

    public function create(string $class): object
    {
        return (object) match ($class) {
            Contract::class => [
                'manual_job' => $this->getManualJob(SkillsHydrateBatch::class),
                'permission' => $this->getPermission(Contract::class),
                'required_scopes' => $this->getRequiredScopes('contracts'),
                'required_corporation_role' => $this->isCharacter() ? '' : '',
            ],
            Asset::class => [
                'manual_job' => $this->getManualJob(CharacterAssetsHydrateBatch::class),
                'permission' => $this->getPermission(Asset::class),
                'required_scopes' => $this->getRequiredScopes('assets'),
                'required_corporation_role' => '',
            ],
            WalletJournal::class => [
                'manual_job' => $this->getManualJob(WalletHydrateBatch::class),
                'permission' => $this->getPermission(WalletJournal::class),
                'required_scopes' => $this->isCharacter() ? $this->getRequiredScopes('wallet') : [...$this->getRequiredScopes('wallet'), 'esi-characters.read_corporation_roles.v1'],
                'required_corporation_role' => $this->isCharacter() ? '' : 'Accountant|Junior_Accountant',
            ],
            Contact::class => [
                'manual_job' => $this->getManualJob(ContactHydrateBatch::class),
                'permission' => $this->getPermission(Contact::class),
                'required_scopes' => $this->getRequiredScopes('contacts'),
                'required_corporation_role' => '',
            ],
            CorporationMemberTracking::class => [
                'manual_job' => $this->getManualJob(CorporationMemberTrackingHydrateBatch::class),
                'permission' => $this->getPermission(CorporationMemberTracking::class),
                'required_scopes' => $this->getRequiredScopes('membertracking'),
                'required_corporation_role' => 'Director',
            ],
            Skill::class => [
                'manual_job' => $this->getManualJob(SkillsHydrateBatch::class),
                'permission' => $this->getPermission(Skill::class),
                'required_scopes' => $this->getRequiredScopes('skills'),
                'required_corporation_role' => '',
            ],
            Mail::class => [
                'manual_job' => $this->getManualJob(MailsHydrateBatch::class),
                'permission' => $this->getPermission(Mail::class),
                'required_scopes' => $this->getRequiredScopes('mails'),
                'required_corporation_role' => '',
            ]
        };
    }

    private function getManualJob(string $needle): string
    {
        return array_search($needle, config('web.jobs'));
    }

    private function getPermission(string $class)
    {
        return config(sprintf('eveapi.permissions.%s', $class));
    }

    private function getRequiredScopes(string $scope)
    {
        return config(
            sprintf('eveapi.scopes.%s.%s',
                $this->isCharacter() ? 'character' : 'corporation',
                $scope)
        );
    }

    /**
     * @param bool $isCharacter
     * @return CreateDispatchTransferObject
     */
    public function setIsCharacter(bool $isCharacter): CreateDispatchTransferObject
    {
        $this->isCharacter = $isCharacter;

        return $this;
    }

    /**
     * @return bool
     */
    private function isCharacter(): bool
    {
        return $this->isCharacter;
    }
}
