<?php


namespace Seatplus\Web\Services\Controller;


use Illuminate\Support\Collection;
use Seatplus\Web\Services\GetRecruitIdsService;

class GetAffiliatedIdsService
{

    private string $request_parameter;

    private string $required_corporation_role = '';

    private string $permission;

    private function getPermission(): string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): GetAffiliatedIdsService
    {
        $this->permission = $permission;
        return $this;
    }


    private function getRequiredCorporationRole(): string
    {
        return $this->required_corporation_role;
    }

    public function setRequiredCorporationRole(string $required_corporation_role): GetAffiliatedIdsService
    {
        $this->required_corporation_role = $required_corporation_role;
        return $this;
    }

    private function getRequestParameter(): string
    {
        return $this->request_parameter;
    }

    public function setRequestFlavour(string $flavour): GetAffiliatedIdsService
    {
        $this->request_parameter = match ($flavour) {
            'character' => 'character_ids',
            'corporation' => 'corporation_ids'
        };

        return $this;
    }

    public static function make()
    {
        return new static();
    }

    public function get(): Collection
    {
        $ids = request()->has($this->getRequestParameter())
            ? request()->get($this->getRequestParameter())
            : $this->getOwnedIds();

        return collect($ids)->intersect([...$this->getAffiliatedIds(), ...GetRecruitIdsService::get()]);
    }

    private function getOwnedIds(): array
    {
        return match ($this->getRequestParameter()) {
            'character_ids' => auth()->user()->characters->pluck('character_id')->toArray(),
            'corporation_ids' => auth()->user()->characters->map(fn($character) => $character->corporation->corporation_id)->toArray()
        };
    }

    private function getAffiliatedIds(): array
    {
        return getAffiliatedIdsByPermission($this->getPermission(), $this->getRequiredCorporationRole());
    }

}