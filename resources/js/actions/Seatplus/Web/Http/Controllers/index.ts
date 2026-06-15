import Auth from './Auth'
import HomeController from './HomeController'
import Queue from './Queue'
import Configuration from './Configuration'
import Shared from './Shared'
import Character from './Character'
import Corporation from './Corporation'
import Onboarding from './Onboarding'
import AccessControl from './AccessControl'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    HomeController: Object.assign(HomeController, HomeController),
    Queue: Object.assign(Queue, Queue),
    Configuration: Object.assign(Configuration, Configuration),
    Shared: Object.assign(Shared, Shared),
    Character: Object.assign(Character, Character),
    Corporation: Object.assign(Corporation, Corporation),
    Onboarding: Object.assign(Onboarding, Onboarding),
    AccessControl: Object.assign(AccessControl, AccessControl),
}

export default Controllers