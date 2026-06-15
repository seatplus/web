import OverviewController from './OverviewController'
import SsoSettingsController from './SsoSettingsController'

const SsoSettings = {
    OverviewController: Object.assign(OverviewController, OverviewController),
    SsoSettingsController: Object.assign(SsoSettingsController, SsoSettingsController),
}

export default SsoSettings