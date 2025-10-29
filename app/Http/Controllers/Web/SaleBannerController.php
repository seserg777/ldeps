<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Exussalebanner;
use App\Models\ExussalebannerTag;
use Illuminate\Http\Request;

class SaleBannerController extends Controller
{
    /**
     * Display a listing of sale banners.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Exussalebanner::published()->ordered();

        // Filter by status (active/completed/all)
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'completed') {
                $now = now();
                $query->where(function($q) use ($now) {
                    $q->where('sale_start', '>', $now)
                      ->orWhere('sale_end', '<', $now);
                });
            }
            // 'all' status shows all published banners without date filtering
        } else {
            // Default: show active banners
            $query->active();
        }

        // Filter by tag if provided
        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('alias', $request->tag);
            });
        }

        $saleBanners = $query->paginate(12);

        // Get all tags for filter
        $tags = ExussalebannerTag::published()->ordered()->get();

        return view('front.sale-banners.index', compact('saleBanners', 'tags'));
    }

    /**
     * Display the specified sale banner.
     *
     * @param string $alias
     * @return \Illuminate\View\View
     */
    public function show($alias)
    {
        $saleBanner = Exussalebanner::published()
            ->where('alias', $alias)
            ->with(['tags', 'specialOffers', 'jshopProducts'])
            ->firstOrFail();

        // Increment hits
        $saleBanner->incrementHits();

        // Get related sale banners
        $relatedBanners = Exussalebanner::published()
            ->active()
            ->where('id', '!=', $saleBanner->id)
            ->whereHas('tags', function ($q) use ($saleBanner) {
                $q->whereIn('tag_id', $saleBanner->tags->pluck('id'));
            })
            ->ordered()
            ->limit(4)
            ->get();

        return view('front.sale-banners.show', compact('saleBanner', 'relatedBanners'));
    }

    /**
     * Display sale banners by tag.
     *
     * @param string $tagAlias
     * @return \Illuminate\View\View
     */
    public function tag($tagAlias)
    {
        $tag = ExussalebannerTag::published()
            ->where('alias', $tagAlias)
            ->firstOrFail();

        $saleBanners = Exussalebanner::published()
            ->active()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tag_id', $tag->id);
            })
            ->ordered()
            ->paginate(12);

        // Get all tags for filter
        $tags = ExussalebannerTag::published()->ordered()->get();

        return view('front.sale-banners.tag', compact('saleBanners', 'tag', 'tags'));
    }

    /**
     * Get sale banners for AJAX requests.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanners(Request $request)
    {
        $query = Exussalebanner::published()->active()->ordered();

        // Filter by tag if provided
        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('alias', $request->tag);
            });
        }

        $saleBanners = $query->limit($request->get('limit', 6))->get();

        return response()->json([
            'success' => true,
            'data' => $saleBanners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->getLocalizedField('title'),
                    'alias' => $banner->alias,
                    'image' => $banner->getLocalizedField('image'),
                    'introtext' => $banner->getLocalizedField('introtext'),
                    'sale_start' => $banner->sale_start->format('d.m.Y'),
                    'sale_end' => $banner->sale_end->format('d.m.Y'),
                    'url' => $banner->getUrl(),
                    'is_active' => $banner->isActive(),
                ];
            })
        ]);
    }
}
