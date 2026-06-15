import ManualLocationController from './ManualLocationController'
import GetAffiliatedCharactersController from './GetAffiliatedCharactersController'
import GetAffiliatedCorporationsController from './GetAffiliatedCorporationsController'
import HelperController from './HelperController'
import EnableEsiSearchController from './EnableEsiSearchController'
import StopImpersonateController from './StopImpersonateController'

const Shared = {
    ManualLocationController: Object.assign(ManualLocationController, ManualLocationController),
    GetAffiliatedCharactersController: Object.assign(GetAffiliatedCharactersController, GetAffiliatedCharactersController),
    GetAffiliatedCorporationsController: Object.assign(GetAffiliatedCorporationsController, GetAffiliatedCorporationsController),
    HelperController: Object.assign(HelperController, HelperController),
    EnableEsiSearchController: Object.assign(EnableEsiSearchController, EnableEsiSearchController),
    StopImpersonateController: Object.assign(StopImpersonateController, StopImpersonateController),
}

export default Shared