import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import contacts677fa8 from './contacts'
import wallet_journal from './wallet_journal'
import wallet_transaction from './wallet_transaction'
import contracts56d39a from './contracts'
/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::assets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
export const assets = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: assets.url(options),
    method: 'get',
})

assets.definition = {
    methods: ["get","head"],
    url: '/character/assets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::assets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
assets.url = (options?: RouteQueryOptions) => {
    return assets.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::assets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
assets.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: assets.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::assets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:44
* @route '/character/assets'
*/
assets.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: assets.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
export const item = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: item.url(args, options),
    method: 'get',
})

item.definition = {
    methods: ["get","head"],
    url: '/character/assets/{character_id}/item/{item_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.url = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
            item_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
        item_id: args.item_id,
    }

    return item.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace('{item_id}', parsedArgs.item_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.get = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: item.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\AssetsController::item
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/AssetsController.php:65
* @route '/character/assets/{character_id}/item/{item_id}'
*/
item.head = (args: { character_id: string | number, item_id: string | number } | [character_id: string | number, item_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: item.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::contacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
export const contacts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contacts.url(options),
    method: 'get',
})

contacts.definition = {
    methods: ["get","head"],
    url: '/character/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::contacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
contacts.url = (options?: RouteQueryOptions) => {
    return contacts.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::contacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
contacts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contacts.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContactsController::contacts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContactsController.php:43
* @route '/character/contacts'
*/
contacts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contacts.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::wallets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
export const wallets = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallets.url(options),
    method: 'get',
})

wallets.definition = {
    methods: ["get","head"],
    url: '/character/wallets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::wallets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
wallets.url = (options?: RouteQueryOptions) => {
    return wallets.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::wallets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
wallets.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallets.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::wallets
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:45
* @route '/character/wallets'
*/
wallets.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: wallets.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
export const balance = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

balance.definition = {
    methods: ["get","head"],
    url: '/character/wallets/{character_id}/balance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { character_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            character_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        character_id: args.character_id,
    }

    return balance.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\WalletsController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/WalletsController.php:80
* @route '/character/wallets/{character_id}/balance'
*/
balance.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: balance.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::contracts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
export const contracts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contracts.url(options),
    method: 'get',
})

contracts.definition = {
    methods: ["get","head"],
    url: '/character/contracts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::contracts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
contracts.url = (options?: RouteQueryOptions) => {
    return contracts.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::contracts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
contracts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contracts.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\ContractsController::contracts
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/ContractsController.php:43
* @route '/character/contracts'
*/
contracts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contracts.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:39
* @route '/character/skills'
*/
export const skills = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skills.url(options),
    method: 'get',
})

skills.definition = {
    methods: ["get","head"],
    url: '/character/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:39
* @route '/character/skills'
*/
skills.url = (options?: RouteQueryOptions) => {
    return skills.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:39
* @route '/character/skills'
*/
skills.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: skills.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\SkillsController::skills
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/SkillsController.php:39
* @route '/character/skills'
*/
skills.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: skills.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
export const mails = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mails.url(options),
    method: 'get',
})

mails.definition = {
    methods: ["get","head"],
    url: '/character/mails',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
mails.url = (options?: RouteQueryOptions) => {
    return mails.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
mails.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mails.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\MailsController::mails
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/MailsController.php:43
* @route '/character/mails'
*/
mails.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mails.url(options),
    method: 'head',
})

const character = {
    assets: Object.assign(assets, assets),
    item: Object.assign(item, item),
    contacts: Object.assign(contacts, contacts677fa8),
    wallets: Object.assign(wallets, wallets),
    wallet_journal: Object.assign(wallet_journal, wallet_journal),
    balance: Object.assign(balance, balance),
    wallet_transaction: Object.assign(wallet_transaction, wallet_transaction),
    contracts: Object.assign(contracts, contracts56d39a),
    skills: Object.assign(skills, skills),
    mails: Object.assign(mails, mails),
}

export default character