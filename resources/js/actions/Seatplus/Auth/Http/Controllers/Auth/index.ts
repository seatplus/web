import RedirectSSOController from './RedirectSSOController'
import StepUpController from './StepUpController'
import CallbackController from './CallbackController'

const Auth = {
    RedirectSSOController: Object.assign(RedirectSSOController, RedirectSSOController),
    StepUpController: Object.assign(StepUpController, StepUpController),
    CallbackController: Object.assign(CallbackController, CallbackController),
}

export default Auth