<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoPart;
use App\Models\Battery;
use App\Models\Rim;
use App\Models\Tyre;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia; // لعمل fallback على الميديا لو متاح

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = WishlistService::getCurrentWishlist()->load('items.buyable');

        $items = $wishlist->items->map(function ($item) {
            $buyable = $item->buyable;
            if (!$buyable) {
                return null;
            }

            $name = $this->resolveName($buyable);
            $price = $this->resolvePrice($buyable);
            $image = $this->resolveImage($buyable);
            $brand = $this->resolveBrand($buyable);

            $item = [
                'type' => class_basename($item->buyable_type),
                'id' => $item->buyable_id,
                'name' => $name,
                'price_per_unit' => $price,
                'quantity' => 1,
                'subtotal' => $price,
                'image' => $image,
                'brand' => $brand,
            ];

            // Add set of 4 keys for tyres and rims
            if ($buyable instanceof Tyre || $buyable instanceof Rim) {
                $item['is_set_of_4'] = $buyable->is_set_of_4 ? $price : 0;
                $item['set_of_4'] = (bool) $buyable->is_set_of_4;
            }

            return $item;
        })->filter(); 

        $subtotal = $items->sum('subtotal');

        return response()->json([
            'message' => 'Wishlist loaded successfully',
            'session_id' => session()->getId(),
            'wishlist' => [
                'items' => $items->values(),
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id' => 'required|integer',
        ]);

        $modelClass = match ($request->buyable_type) {
            'auto_part' => AutoPart::class,
            'battery'   => Battery::class,
            'tyre'      => Tyre::class,
            'rim'       => Rim::class,
        };

        $wishlist = WishlistService::getCurrentWishlist();

        // Use firstOrCreate for atomic operation
        $wishlist->items()->firstOrCreate([
            'buyable_type' => $modelClass,
            'buyable_id' => $request->buyable_id,
        ]);

        return response()->json([
            'message' => 'Added to wishlist',
            'session_id' => session()->getId()
        ], 200);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|string|in:auto_part,battery,tyre,rim',
            'buyable_id' => 'required|integer',
        ]);

        $modelClass = match ($request->buyable_type) {
            'auto_part' => AutoPart::class,
            'battery'   => Battery::class,
            'tyre'      => Tyre::class,
            'rim'       => Rim::class,
        };

        $wishlist = WishlistService::getCurrentWishlist();

        $wishlist->items()
            ->where('buyable_type', $modelClass)
            ->where('buyable_id', $request->buyable_id)
            ->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }

    private function resolvePrice($buyable): float
    {
        // Handle discounted price first for all types
        if (isset($buyable->discounted_price) && $buyable->discounted_price > 0) {
            $basePrice = (float) $buyable->discounted_price;
        } else {
            // Get base price based on product type
            $basePrice = match (class_basename($buyable)) {
                'Tyre' => (float) ($buyable->price_vat_inclusive ?? 0),
                'Rim' => (float) ($buyable->regular_price ?? 0),
                'Battery' => (float) ($buyable->regular_price ?? 0),
                'AutoPart' => (float) ($buyable->price_including_vat ?? 0),
                default => 0.0
            };
        }

        // Multiply by 4 for set of 4 products (tyres and rims)
        if (($buyable instanceof Tyre || $buyable instanceof Rim) && $buyable->is_set_of_4) {
            $basePrice *= 4;
        }

        return $basePrice;
    }

    /**
     */
    private function resolveName($buyable): string
    {
        return $buyable->name
            ?? ($buyable->product_name ?? null)
            ?? ($buyable->title ?? '')
            ?? '';
    }

    private function resolveBrand($buyable): ?array
    {
        if ($buyable instanceof \App\Models\AutoPart && $buyable->autoPartBrand) {
            return [
                'id' => $buyable->autoPartBrand->id,
                'name' => $buyable->autoPartBrand->name,
                'logo_url' => $buyable->autoPartBrand->logo_url 
                    ?? $buyable->autoPartBrand->getFirstMediaUrl('brand_logo'),
            ];
        }

        if ($buyable instanceof \App\Models\Battery && $buyable->batteryBrand) {
            return [
                'id' => $buyable->batteryBrand->id,
                'name' => $buyable->batteryBrand->name,
                'logo_url' => $buyable->batteryBrand->logo_url 
                    ?? $buyable->batteryBrand->getFirstMediaUrl('brand_logo'),
            ];
        }

        if ($buyable instanceof \App\Models\Rim && $buyable->rimBrand) {
            return [
                'id' => $buyable->rimBrand->id,
                'name' => $buyable->rimBrand->name,
                'logo_url' => $buyable->rimBrand->logo_url 
                    ?? $buyable->rimBrand->getFirstMediaUrl('brand_logo'),
            ];
        }

        if ($buyable instanceof \App\Models\Tyre && $buyable->tyreBrand) {
            return [
                'id' => $buyable->tyreBrand->id,
                'name' => $buyable->tyreBrand->name,
                'logo_url' => $buyable->tyreBrand->logo_url 
                    ?? $buyable->tyreBrand->getFirstMediaUrl('brand_logo'),
            ];
        }

        return null;
    }



    /**
     */
    private function resolveImage($buyable): ?string
    {
        if (method_exists($buyable, 'getFeatureImageUrl')) {
            $url = $buyable->getFeatureImageUrl();
            if (!empty($url)) return $url;
        }

        $methodCandidates = [
            'getAutoPartSecondaryImageUrl',
            'getAutoPartFeatureImageUrl',
            'getTyreFeatureImageUrlAttribute',
            'getFeatureImageUrlAttribute',
            'getRimFeatureImageUrl',
        ];

        foreach ($methodCandidates as $method) {
            if (method_exists($buyable, $method)) {
                $url = $buyable->{$method}();
                if (!empty($url)) return $url;
            }
        }

        if ($buyable instanceof AutoPart && !empty($buyable->photo_link)) {
            return asset('storage/' . $buyable->photo_link);
        }


        if ($buyable instanceof HasMedia) {
            $url = $buyable->getFirstMediaUrl('feature');
            if (!empty($url)) return $url;

            $url = $buyable->getFirstMediaUrl();
            if (!empty($url)) return $url;
        }

        return null;
    }
}
