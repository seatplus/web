import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import wallet_journal from './wallet_journal'
import wallet_transaction from './wallet_transaction'
import review from './review'
/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::history
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
export const history = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(args, options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/character/corporation_history/{character_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::history
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
history.url = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return history.definition.url
            .replace('{character_id}', parsedArgs.character_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::history
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
history.get = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Character\CorporationHistoryController::history
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Character/CorporationHistoryController.php:35
* @route '/character/corporation_history/{character_id}'
*/
history.head = (args: { character_id: string | number } | [character_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::wallet
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
export const wallet = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallet.url(options),
    method: 'get',
})

wallet.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::wallet
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
wallet.url = (options?: RouteQueryOptions) => {
    return wallet.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::wallet
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
wallet.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: wallet.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::wallet
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:42
* @route '/corporation/wallet'
*/
wallet.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: wallet.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
export const balance = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

balance.definition = {
    methods: ["get","head"],
    url: '/corporation/wallet/{corporation_id}/balance/{division_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.url = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            division_id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        division_id: args.division_id,
    }

    return balance.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{division_id}', parsedArgs.division_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.get = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: balance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController::balance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Wallet/CorporationWalletController.php:63
* @route '/corporation/wallet/{corporation_id}/balance/{division_id}'
*/
balance.head = (args: { corporation_id: string | number, division_id: string | number } | [corporation_id: string | number, division_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: balance.url(args, options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
export const member_tracking = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_tracking.url(options),
    method: 'get',
})

member_tracking.definition = {
    methods: ["get","head"],
    url: '/corporation/tracking',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
member_tracking.url = (options?: RouteQueryOptions) => {
    return member_tracking.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
member_tracking.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_tracking.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController::member_tracking
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberTracking/MemberTrackingController.php:45
* @route '/corporation/tracking'
*/
member_tracking.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: member_tracking.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
export const recruitment = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruitment.url(options),
    method: 'get',
})

recruitment.definition = {
    methods: ["get","head"],
    url: '/corporation/recruitment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
recruitment.url = (options?: RouteQueryOptions) => {
    return recruitment.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
recruitment.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruitment.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController::__invoke
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/Recruitment/GetRecruitmentIndexController.php:43
* @route '/corporation/recruitment'
*/
recruitment.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recruitment.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::member_compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
export const member_compliance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_compliance.url(options),
    method: 'get',
})

member_compliance.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::member_compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
member_compliance.url = (options?: RouteQueryOptions) => {
    return member_compliance.definition.url + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::member_compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
member_compliance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: member_compliance.url(options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::member_compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:42
* @route '/corporation/compliance'
*/
member_compliance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: member_compliance.url(options),
    method: 'head',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
export const compliance = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(args, options),
    method: 'get',
})

compliance.definition = {
    methods: ["get","head"],
    url: '/corporation/compliance/{corporation_id}/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
compliance.url = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            corporation_id: args[0],
            type: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        corporation_id: args.corporation_id,
        type: args.type,
    }

    return compliance.definition.url
            .replace('{corporation_id}', parsedArgs.corporation_id.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
compliance.get = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(args, options),
    method: 'get',
})

/**
* @see \Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController::compliance
* @see Users/hufe/PhpstormProjects/core/packages/web/src/Http/Controllers/Corporation/MemberCompliance/MemberComplianceController.php:71
* @route '/corporation/compliance/{corporation_id}/{type}'
*/
compliance.head = (args: { corporation_id: string | number, type: string | number } | [corporation_id: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compliance.url(args, options),
    method: 'head',
})

const corporation = {
    history: Object.assign(history, history),
    wallet: Object.assign(wallet, wallet),
    wallet_journal: Object.assign(wallet_journal, wallet_journal),
    balance: Object.assign(balance, balance),
    wallet_transaction: Object.assign(wallet_transaction, wallet_transaction),
    member_tracking: Object.assign(member_tracking, member_tracking),
    recruitment: Object.assign(recruitment, recruitment),
    member_compliance: Object.assign(member_compliance, member_compliance),
    compliance: Object.assign(compliance, compliance),
    review: Object.assign(review, review),
}

export default corporation