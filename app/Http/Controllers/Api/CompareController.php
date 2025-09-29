<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompareList;
use App\Models\CompareItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CompareController extends Controller
{
    protected function getOrCreateCompareList(): CompareList
    {
        $user = auth()->user();
        $sessionId = request()->header('X-Session-Id', session()->getId());

        if ($user) {
            $compareList = CompareList::where('user_id', $user->id)->first()
                ?? CompareList::create(['user_id' => $user->id, 'session_id' => null]);
        } else {
            $compareList = CompareList::where('session_id', $sessionId)->first()
                ?? CompareList::create(['user_id' => null, 'session_id' => $sessionId]);
        }

        return $compareList;
    }

    public function index()
    {
        $compareList = $this->getOrCreateCompareList();
        
        // Get items without eager loading first
        $items = $compareList->items()->get();
        
        if ($items->isEmpty()) {
            return response()->json(['items' => []]);
        }
        
        // Group items by type for efficient loading
        $itemsByType = $items->groupBy('buyable_type');
        $loadedProducts = collect();
        
        foreach ($itemsByType as $type => $typeItems) {
            $ids = $typeItems->pluck('buyable_id');
            
            $products = match ($type) {
                'auto_part' => \App\Models\AutoPart::with(['category', 'autoPartBrand', 'autoPartCountry', 'viscosityGrade'])
                    ->whereIn('id', $ids)->get(),
                'battery' => \App\Models\Battery::with(['batteryBrand', 'capacity', 'dimension', 'batteryCountry'])
                    ->whereIn('id', $ids)->get(),
                'tyre' => \App\Models\Tyre::with(['tyreBrand', 'tyreModel', 'tyreSize', 'tyreCountry', 'tyreAttribute'])
                    ->whereIn('id', $ids)->get(),
                'rim' => \App\Models\Rim::with(['rimBrand', 'rimSize', 'rimCountry', 'rimAttribute', 'attributes'])
                    ->whereIn('id', $ids)->get(),
                default => collect()
            };
            
            foreach ($products as $product) {
                $loadedProducts->put($type . '_' . $product->id, $product);
            }
        }
        
        return response()->json([
            'items' => $items->map(function ($item) use ($loadedProducts) {
                $product = $loadedProducts->get($item->buyable_type . '_' . $item->buyable_id);
                
                if (!$product) {
                    return null;
                }
                
                return [
                    'id' => $product->id,
                    'type' => $item->buyable_type,
                    'name' => $product->name ?? $product->title,
                    'image' => $this->getProductImageUrl($product, $item->buyable_type),
                    'attributes' => $this->transformAttributes($product, $item->buyable_type),
                ];
            })->filter()->values(),
        ]);
    }

    private function normalizePhotoLink(?string $photoLink): ?string
    {
        if (!$photoLink || trim($photoLink) === '') return null;
        
        // If it's already a full URL (http/https), return as is
        if (str_starts_with($photoLink, 'http')) {
            return $photoLink;
        }
        
        // Otherwise, treat as storage path
        return asset('storage/' . ltrim($photoLink, '/'));
    }

    private function getProductImageUrl($product, string $type): ?string
    {
        return match ($type) {
            'auto_part' => $this->normalizePhotoLink($product->photo_link)
                ?? $product->getAutoPartSecondaryImageUrl()
                ?? $this->normalizePhotoLink($product->image ?? null)
                ?? $product->getFirstMediaUrl('auto_part_secondary_image')
                ?? $product->getFirstMediaUrl('feature')
                ?? ($product->getFirstMediaUrl() ?: null),

            'tyre' => $product->tyre_feature_image_url
                ?? $product->tyre_secondary_image_url
                ?? $this->normalizePhotoLink($product->image)
                ?? ($product->getFirstMediaUrl() ?: null),

            'battery' => $product->getFirstMediaUrl('battery_feature_image')
                ?? $product->getFirstMediaUrl('battery_secondary_image')
                ?? $product->feature_image_url
                ?? $product->image_url
                ?? $this->normalizePhotoLink($product->photo_link)
                ?? ($product->getFirstMediaUrl() ?: null),

            'rim' => $product->rim_feature_image_url
                ?? $product->rim_secondary_image_url
                ?? $this->normalizePhotoLink($product->photo_link)
                ?? ($product->getFirstMediaUrl() ?: null),

            default => ($product->getFirstMediaUrl() ?: null),
        };
    }



    private function transformAttributes($p, string $type): array
    {
        switch ($type) {
            case 'auto_part':
                return [
                    'category'         => optional($p->category)->name,
                    'brand'            => optional($p->autoPartBrand)->name,
                    'brand_logo'       => optional($p->autoPartBrand)->logo_url,
                    'country'          => optional($p->autoPartCountry)->name,
                    'viscosity_grade'  => optional($p->viscosityGrade)->name,

                    'sku'              => $p->sku,
                    'price_including_vat' => $p->price_including_vat,
                    'discount_percentage' => $p->discount_percentage,
                    'discounted_price'    => $p->discounted_price,

                    'weight' => $p->weight,
                    'length' => $p->length,
                    'width'  => $p->width,
                    'height' => $p->height,
                    'dimensions' => [
                        'length' => $p->length,
                        'width'  => $p->width,
                        'height' => $p->height,
                    ],

                    'slug'        => $p->slug,
                    'alt_text'    => $p->alt_text ?? $p->photo_alt_text,
                    'description' => $p->description,
                    'details'     => $p->details,
                    'share_url'   => $p->share_url ?? null,
                ];

            case 'battery':
                return [
                    'category'     => optional($p->category)->name,
                    'brand'        => optional($p->batteryBrand)->value,
                    'brand_logo'   => optional($p->batteryBrand)->logo_url,
                    'capacity'     => optional($p->capacity)->value,
                    'dimension'    => optional($p->dimension)->value,
                    'country'      => optional($p->batteryCountry)->name,
                    'warranty'     => $p->warranty ?? null,
                    'weight'       => $p->weight ?? null,

                    'sku'              => $p->sku,
                    'price_including_vat' => $p->regular_price ?? null,
                    'discount_percentage' => $p->discount_percentage ?? null,
                    'discounted_price'    => $p->discounted_price ?? null,

                    'slug'        => $p->slug,
                    'alt_text'    => $p->alt_text ?? null,
                    'description' => $p->description ?? null,
                    'share_url'   => $p->share_url ?? null,
                ];

            case 'tyre':
                $basePrice = $p->price_vat_inclusive ?? null;
                $discountedPrice = $p->discounted_price ?? null;
                $setOf4Price = $basePrice ? $basePrice * 4 : null;
                $setOf4DiscountedPrice = $discountedPrice ? $discountedPrice * 4 : null;
                
                return [
                    'brand'       => optional($p->tyreBrand)->name,
                    'brand_logo'  => optional($p->tyreBrand)->logo_url,
                    'model'       => optional($p->tyreModel)->name,
                    'size'        => optional($p->tyreSize)->size,
                    'country'     => optional($p->tyreCountry)->name,
                    'attribute'   => optional($p->tyreAttribute)->name,
                    
                    'warranty'        => $p->warranty ?? null,
                    'width'           => $p->width ?? null,
                    'height'          => $p->height ?? null,
                    'wheel_diameter'  => $p->wheel_diameter ?? null,
                    'production_year' => $p->production_year ?? null,
                    'weight'          => $p->weight_kg ?? null,

                    'price_including_vat' => $basePrice,
                    'discount_percentage' => $p->discount_percentage ?? null,
                    'discounted_price'    => $discountedPrice,
                    'set_of_4'           => $setOf4Price,

                    'slug'        => $p->slug,
                    'alt_text'    => $p->alt_text ?? null,
                    'description' => $p->description ?? null,
                    'share_url'   => $p->share_url ?? null,
                ];

            case 'rim':
                $basePrice = $p->regular_price ?? null;
                $discountedPrice = $p->discounted_price ?? null;
                
                // Multiply by 4 if it's a set of 4
                if ($p->is_set_of_4) {
                    $basePrice = $basePrice ? $basePrice * 4 : null;
                    $discountedPrice = $discountedPrice ? $discountedPrice * 4 : null;
                }
                
                return [
                    'category'   => optional($p->category)->name,
                    'brand'      => optional($p->rimBrand)->name,
                    'brand_logo' => optional($p->rimBrand)->logo_url,
                    'size'       => optional($p->rimSize)->size,
                    'country'    => optional($p->rimCountry)->name,
                    'attribute'  => optional($p->rimAttribute)->name,

                    'sku'            => $p->sku ?? null,
                    'bolt_pattern'   => $p->bolt_pattern ?? null,
                    'centre_caps'    => $p->centre_caps ?? null,
                    'item_code'      => $p->item_code ?? null,
                    'warranty'       => $p->warranty ?? null,
                    'wheel_attribute' => $p->attributes->first()?->name ?? optional($p->rimAttribute)->name,
                    'weight_kg'      => $p->weight ?? null,
                    'set_of_4'       => $p->is_set_of_4 ? ($basePrice ? $basePrice * 4 : null) : null,
                    'offsets'        => $p->offsets ?? null,
                    
                    'colour'     => $p->colour ?? null,
                    'condition'  => $p->condition ?? null,
                    'specification' => $p->specification ?? null,

                    'price_including_vat' => $basePrice,
                    'discount_percentage' => $p->discount_percent ?? null,
                    'discounted_price'    => $discountedPrice,

                    'slug'        => $p->slug,
                    'alt_text'    => $p->alt_text ?? null,
                    'description' => $p->product_full_description ?? $p->description ?? null,
                    'share_url'   => $p->share_url ?? null,
                ];

            default:
                return collect($p->getAttributes())
                    ->reject(fn($v, $k) => str_ends_with($k, '_id'))
                    ->all();
        }
    }



    public function add(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|in:auto_part,battery,tyre,rim',
            'buyable_id' => 'required|integer',
        ]);

        $shortKey = $request->buyable_type;
        $compareList = $this->getOrCreateCompareList();

        // Check product type compatibility
        if ($compareList->product_type && $compareList->product_type !== $shortKey) {
            return response()->json(['message' => 'Cannot compare different product types.'], 422);
        }

        // Verify product exists without loading it
        $modelClass = match ($shortKey) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery'   => \App\Models\Battery::class,
            'tyre'      => \App\Models\Tyre::class,
            'rim'       => \App\Models\Rim::class,
        };
        
        if (!$modelClass::where('id', $request->buyable_id)->exists()) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Update product type if needed
        if (!$compareList->product_type) {
            $compareList->update(['product_type' => $shortKey]);
        }

        // Add to compare list
        $compareList->items()->firstOrCreate([
            'buyable_type' => $shortKey,
            'buyable_id' => $request->buyable_id,
        ]);

        return response()->json(['message' => 'Product added to compare list.']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'buyable_type' => 'required|in:auto_part,battery,tyre,rim',
            'buyable_id' => 'required|integer',
        ]);

        $shortKey = $request->buyable_type;

        $compareList = $this->getOrCreateCompareList();

        $compareList->items()
            ->where('buyable_type', $shortKey)
            ->where('buyable_id', $request->buyable_id)
            ->delete();

        if ($compareList->items()->count() === 0) {
            $compareList->update(['product_type' => null]);
        }

        return response()->json(['message' => 'Product removed from compare list.']);
    }

    public function clear()
    {
        $compareList = $this->getOrCreateCompareList();
        $compareList->items()->delete();
        $compareList->update(['product_type' => null]);

        return response()->json(['message' => 'Compare list cleared.']);
    }

    public function debug(Request $request)
    {
        $ids = $request->input('ids', [14, 15, 16]); // Default to your problematic IDs
        $type = $request->input('type', 'auto_part');
        
        $modelClass = match ($type) {
            'auto_part' => \App\Models\AutoPart::class,
            'battery' => \App\Models\Battery::class,
            'tyre' => \App\Models\Tyre::class,
            'rim' => \App\Models\Rim::class,
        };
        
        $products = $modelClass::whereIn('id', $ids)->get();
        
        $debug = [];
        foreach ($products as $product) {
            $debug[] = [
                'id' => $product->id,
                'name' => $product->name ?? $product->title,
                'photo_link' => $product->photo_link ?? null,
                'image' => $product->image ?? null,
                'media_url' => $product->getFirstMediaUrl(),
                'secondary_media' => $product->getFirstMediaUrl('auto_part_secondary_image'),
                'feature_media' => $product->getFirstMediaUrl('feature'),
                'resolved_image' => $this->getProductImageUrl($product, $type),
                'has_media' => $product->getMedia()->count(),
            ];
        }
        
        return response()->json([
            'type' => $type,
            'products' => $debug
        ]);
    }
}
