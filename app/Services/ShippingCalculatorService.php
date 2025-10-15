<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\ShippingSetting;
use App\Models\Area;

class ShippingCalculatorService
{
    protected static string $view = 'filament.resources.pages.view-record-full-width';

    public static function calculate(Cart $cart, int $monthlyShipments, ?int $areaId = null): array
    {
        try {
            $setting = ShippingSetting::first();
            if (!$setting) {
                throw new \Exception('Shipping settings not found');
            }

            $areaId = $areaId ?? $cart->area_id ?? null;
            
            // Calculate base cost without area cost
            $isRemote = $areaId ? self::isRemote($areaId) : false;
            $baseCost = self::getBaseCost($monthlyShipments, $isRemote, $setting);
            
            // Add area-specific shipping cost only if area is provided
            $areaShippingCost = 0;
            if ($areaId) {
                $area = Area::find($areaId);
                $areaShippingCost = $area?->shipping_cost ?? 0;
            }

            $totalWeight = 0;

            foreach ($cart->items as $item) {
                if (!$item->buyable) {
                    continue;
                }
                
                $weight = max(
                    $item->buyable->weight ?? 0,
                    self::calculateVolumetricWeight($item->buyable, $setting)
                );

                $totalWeight += $weight * $item->quantity;
            }

            $extraWeight = max(0, $totalWeight - 5);
            $extraCharge = $extraWeight * ($setting->extra_per_kg ?? 2);

            $subtotal = $baseCost + $extraCharge + $areaShippingCost;

            $fuel = round($subtotal * ($setting->fuel_percent ?? 0.02), 2);
            $subtotal += $fuel;

            $subtotal += ($setting->packaging_fee ?? 5.25);

            $epg = max(
                round($subtotal * ($setting->epg_percent ?? 0.10), 2),
                $setting->epg_min ?? 2
            );
            $subtotal += $epg;

            // $vat = round($subtotal * ($setting->vat_percent ?? 0.05), 2);

            return [
                'total' => round($subtotal , 2),
                'breakdown' => [
                    'base_cost' => $baseCost - $areaShippingCost,
                    'area_shipping_cost' => $areaShippingCost,
                    'weight_charges' => $extraCharge,
                    'fuel_surcharge' => $fuel,
                    'packaging' => $setting->packaging_fee ?? 5.25,
                    'epg' => $epg,
                    // 'vat' => $vat,
                    'total_weight' => round($totalWeight, 2),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Use DB column is_remote instead of matching names
     */
    private static function isRemote(int $areaId): bool
    {
        return Area::where('id', $areaId)->where('is_remote', true)->exists();
    }

    private static function getBaseCost(int $monthlyShipments, bool $isRemote, ShippingSetting $setting): float
    {
        $tiers = is_array($setting->tiers)
            ? $setting->tiers
            : json_decode($setting->tiers ?? '[]', true);

        foreach ($tiers as $tier) {
            if ($monthlyShipments <= $tier['max']) {
                return $isRemote ? $tier['remote'] : $tier['normal'];
            }
        }

        return 0;
    }

    private static function calculateVolumetricWeight($product, ShippingSetting $setting): float
    {
        if (!$product || !isset($product->length) || !isset($product->width) || !isset($product->height)) {
            return 0;
        }
        
        if (!$product->length || !$product->width || !$product->height) {
            return 0;
        }

        $volume = $product->length * $product->width * $product->height;
        $divisor = $setting->volumetric_divisor ?? 5000;

        return $volume / $divisor;
    }
}
