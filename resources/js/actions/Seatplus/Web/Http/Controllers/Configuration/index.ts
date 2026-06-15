import SeatPlusController from './SeatPlusController'
import CommandsController from './CommandsController'
import SsoSettings from './SsoSettings'
import UserSettingsController from './UserSettingsController'
import Schedules from './Schedules'
import PerformanceController from './PerformanceController'

const Configuration = {
    SeatPlusController: Object.assign(SeatPlusController, SeatPlusController),
    CommandsController: Object.assign(CommandsController, CommandsController),
    SsoSettings: Object.assign(SsoSettings, SsoSettings),
    UserSettingsController: Object.assign(UserSettingsController, UserSettingsController),
    Schedules: Object.assign(Schedules, Schedules),
    PerformanceController: Object.assign(PerformanceController, PerformanceController),
}

export default Configuration