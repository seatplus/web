<?php


namespace Seatplus\Web\Services\SsoSettings;


use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

class GetSsoScopeEntries
{
    public function execute(): Collection
    {
        return SsoScopes::with('morphable')->get()->map(function ($scope) {

            $selectedEntity = [
                'id' => $scope->morphable_id,
                'name' => optional($scope->morphable)->name
            ];

            $selectedEntity = array_merge($selectedEntity, $scope->morphable_type === CorporationInfo::class
                ? ['corporation_id' => $scope->morphable_id]
                : ['alliance_id' => $scope->morphable_id]
            );

            return [
                'selectedEntity' => $selectedEntity,
                'selectedScopes' => $scope->selected_scopes
            ];
        });
    }

}
