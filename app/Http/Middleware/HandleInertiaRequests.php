<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Butschster\Head\Hydrator\VueMetaHydrator;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    protected $meta;

    public function __construct(MetaInterface $meta)
    {
        $this->meta = $meta;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
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
     *
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'settings' => SiteSetting::first()->only('title', 'description', 'registration'),
            'meta' => fn (VueMetaHydrator $hydrator) => ($hydrator->hydrate($this->meta)),
        ]);
    }
}
