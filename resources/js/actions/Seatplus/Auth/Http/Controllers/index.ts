import Auth from './Auth'
import SwitchMainCharacterController from './SwitchMainCharacterController'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    SwitchMainCharacterController: Object.assign(SwitchMainCharacterController, SwitchMainCharacterController),
}

export default Controllers