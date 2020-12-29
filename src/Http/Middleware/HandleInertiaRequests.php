<?php


namespace Seatplus\Web\Http\Middleware;


use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\Sidebar\SidebarEntries;

class HandleInertiaRequests extends Middleware
{
    // Set root template via method
    public function rootView(Request $request)
    {
        return 'web::app';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'flash' => function () {
                return [
                    'success' => session()->pull('success'),
                    'info' => session()->pull('info'),
                    'warning' => session()->pull('warning'),
                    'error' => session()->pull('error'),
                ];
            },
            'sidebar' => function () {
                return auth()->guest() ? [] : (new SidebarEntries)->filter();
            },
            'user' => function () {
                return auth()->guest() ? '' : UserRessource::make(
                    User::with('main_character', 'characters', 'characters.refresh_token')
                        ->where('id', auth()->user()->id)
                        ->first()
                );
            },
            'translation' => function () {
                return [
                    'success' => trans('web::notifications.success'),
                    'info' => trans('web::notifications.info'),
                    'warning' => trans('web::notifications.warning'),
                    'error' => trans('web::notifications.error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'images' => function () {
                return [
                    'logo' => asset('img/seat_plus.svg'),
                ];
            },
        ]);
    }
}
