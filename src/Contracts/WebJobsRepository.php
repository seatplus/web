<?php

namespace Seatplus\Web\Contracts;

use Seatplus\Eveapi\Jobs\Assets\CharacterAssetJob;
use Seatplus\Eveapi\Jobs\Assets\CharacterAssetsNameJob;
use Seatplus\Eveapi\Jobs\Contacts\AllianceContactJob;
use Seatplus\Eveapi\Jobs\Contacts\AllianceContactLabelJob;
use Seatplus\Eveapi\Jobs\Contacts\CharacterContactJob;
use Seatplus\Eveapi\Jobs\Contacts\CharacterContactLabelJob;
use Seatplus\Eveapi\Jobs\Contacts\CorporationContactJob;
use Seatplus\Eveapi\Jobs\Contacts\CorporationContactLabelJob;
use Seatplus\Eveapi\Jobs\Contracts\CharacterContractsJob;
use Seatplus\Eveapi\Jobs\Corporation\CorporationMemberTrackingJob;
use Seatplus\Eveapi\Jobs\Mail\MailHeaderJob;
use Seatplus\Eveapi\Jobs\Skills\SkillsJob;
use Seatplus\Eveapi\Jobs\Wallet\CharacterBalanceJob;
use Seatplus\Eveapi\Jobs\Wallet\CharacterWalletJournalJob;
use Seatplus\Eveapi\Jobs\Wallet\CharacterWalletTransactionJob;
use Seatplus\Eveapi\Jobs\Wallet\CorporationBalanceJob;
use Seatplus\Eveapi\Jobs\Wallet\CorporationWalletJournalJob;
use Seatplus\Eveapi\Models\RefreshToken;

class WebJobsRepository
{
    private array $jobs = [];

    public function __construct()
    {
        $this->jobs = [
            // Character
            'contacts' => fn (RefreshToken $refresh_token) => $this->getContactJobs($refresh_token),
            'assets' => fn (RefreshToken $refresh_token) => $this->getAssetJobs($refresh_token),
            'wallet' => fn (RefreshToken $refresh_token) => $this->getWalletJobs($refresh_token),
            'contract' => fn (RefreshToken $refresh_token) => $this->getContractJobs($refresh_token),
            'skills' => fn (RefreshToken $refresh_token) => $this->getSkillsJobs($refresh_token),
            'mails' => fn (RefreshToken $refresh_token) => $this->getMailsJobs($refresh_token),
            // Corporation
            'corporation.wallet' => fn (RefreshToken $refresh_token) => $this->getCorporationWalletJobs($refresh_token),
            'membertracking' => fn (RefreshToken $refresh_token) => $this->getCorporationWalletJobs($refresh_token),
        ];
    }

    public function addJob(string $key, \Closure $build_function): void
    {
        $this->jobs[$key] = $build_function;
    }

    public function addJobs(array $jobs): void
    {
        $this->jobs = array_merge($this->jobs, $jobs);
    }

    public function getJobKeys(): array
    {
        return array_keys($this->jobs);
    }

    public function getConstructedJobs(string $key, RefreshToken $refresh_token): array
    {
        return $this->jobs[$key]($refresh_token);
    }

    private function getContactJobs(RefreshToken $refresh_token): array
    {
        $jobs = [];

        // if refresh token has scope for reading contacts add the job to the jobs array
        if ($refresh_token->hasScope('esi-characters.read_contacts.v1')) {
            $jobs[] = [
                new CharacterContactJob($refresh_token->character_id),
                new CharacterContactLabelJob($refresh_token->character_id),
            ];
        }

        // if refresh token has scope for reading corporation contacts add the job to the jobs array
        if ($refresh_token->hasScope('esi-corporations.read_contacts.v1')) {
            $jobs[] = [
                new CorporationContactJob($refresh_token->character_id),
                new CorporationContactLabelJob($refresh_token->character_id),
            ];
        }

        // if refresh token has scope for reading alliance contacts add the job to the jobs array
        if ($refresh_token->hasScope('esi-alliances.read_contacts.v1')) {
            $jobs[] = [
                new AllianceContactJob($refresh_token->character_id),
                new AllianceContactLabelJob($refresh_token->character_id),
            ];
        }

        return $jobs;
    }

    private function getAssetJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-assets.read_assets.v1')) {
            return [];
        }

        return [
            new CharacterAssetJob($refresh_token->character_id),
            new CharacterAssetsNameJob($refresh_token->character_id),
        ];
    }

    private function getWalletJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-wallet.read_character_wallet.v1')) {
            return [];
        }

        return [
            new CharacterWalletJournalJob($refresh_token->character_id),
            new CharacterWalletTransactionJob($refresh_token->character_id),
            new CharacterBalanceJob($refresh_token->character_id),
        ];
    }

    private function getContractJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-contracts.read_character_contracts.v1')) {
            return [];
        }

        return [
            new CharacterContractsJob($refresh_token->character_id),
        ];
    }

    private function getSkillsJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-skills.read_skills.v1')) {
            return [];
        }

        return [
            new SkillsJob($refresh_token->character_id),
        ];
    }

    private function getMailsJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-mail.read_mail.v1')) {
            return [];
        }

        return [
            new MailHeaderJob($refresh_token->character_id),
        ];
    }

    private function getCorporationWalletJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-wallet.read_corporation_wallets.v1')) {
            return [];
        }

        return [
            new CorporationWalletJournalJob($refresh_token->corporation_id),
            new CorporationBalanceJob($refresh_token->corporation_id),
        ];
    }

    public function getCorporationMemberTrackingJobs(RefreshToken $refresh_token): array
    {
        if (! $refresh_token->hasScope('esi-corporations.track_members.v1')) {
            return [];
        }

        return [
            new CorporationMemberTrackingJob($refresh_token->corporation_id),
        ];
    }
}
