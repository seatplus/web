<?php


namespace Seatplus\Web\Services\Controller;


use Seatplus\Eveapi\Jobs\Hydrate\Character\CharacterAssetsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContractHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\WalletHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
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
                'manual_job' => $this->getManualJob(ContractHydrateBatch::class),
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