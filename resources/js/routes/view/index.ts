import scopes from './scopes'
import global from './global'
import create from './create'

const view = {
    scopes: Object.assign(scopes, scopes),
    global: Object.assign(global, global),
    create: Object.assign(create, create),
}

export default view