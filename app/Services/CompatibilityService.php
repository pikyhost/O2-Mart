<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CarModel;
use App\Models\CompatibilityRule;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CompatibilityService
{
    /**
     * Get all products that are compatible with a specific car model
     * This is the main method for finding compatible products
     */
    public function getCompatibleProductsForCarModel(CarModel $carModel): Collection
    {
        // Cache key for performance
        $cacheKey = "compatible_products_car_model_{$carModel->id}";

        return Cache::remember($cacheKey, 3600, function () use ($carModel) {
            // Get all products that have compatibility rules
            $productsWithRules = Product::whereHas('compatibilityRules')
                ->with(['compatibilityRules.attribute', 'categories'])
                ->get();

            // Filter products based on compatibility
            return $productsWithRules->filter(function ($product) use ($carModel) {
                return $this->isProductCompatibleWithCarModel($product, $carModel);
            });
        });
    }

    /**
     * Check if a product is compatible with a car model
     * A product is compatible if ALL its compatibility rules pass
     */
    public function isProductCompatibleWithCarModel(Product $product, CarModel $carModel): bool
    {
        $rules = $product->compatibilityRules()->with('attribute')->get();

        if ($rules->isEmpty()) {
            // No rules means product is compatible with everything
            return true;
        }

        // Load all car model attributes at once for efficiency
        $carModelAttributes = $carModel->attributes()
            ->withPivot('value')
            ->get()
            ->keyBy('id');

        // Check each rule
        foreach ($rules as $rule) {
            $attribute = $carModelAttributes->get($rule->attribute_id);

            if (!$attribute) {
                // Car model doesn't have this attribute, rule fails
                return false;
            }

            $carModelValue = $attribute->pivot->value;

            if (!$this->evaluateRule($rule, $carModelValue)) {
                // If any rule fails, product is not compatible
                return false;
            }
        }

        // All rules passed
        return true;
    }

    /**
     * Get all car models that are compatible with a specific product
     */
    public function getCompatibleCarModelsForProduct(Product $product): Collection
    {
        $cacheKey = "compatible_car_models_product_{$product->id}";

        return Cache::remember($cacheKey, 3600, function () use ($product) {
            $rules = $product->compatibilityRules()->with('attribute')->get();

            if ($rules->isEmpty()) {
                // No rules means compatible with all car models
                return CarModel::where('is_active', true)->get();
            }

            // Get all car models and filter by compatibility
            $carModels = CarModel::where('is_active', true)
                ->with(['attributes', 'make'])
                ->get();

            return $carModels->filter(function ($carModel) use ($product) {
                return $this->isProductCompatibleWithCarModel($product, $carModel);
            });
        });
    }

    /**
     * Evaluate a single compatibility rule against a value
     */
    public function evaluateRule(CompatibilityRule $rule, $value): bool
    {
        $ruleValue = $rule->value;

        return match($rule->operator) {
            '=' => $this->compareValues($value, $ruleValue, '='),
            '!=' => $this->compareValues($value, $ruleValue, '!='),
            '>' => $this->compareNumeric($value, $ruleValue, '>'),
            '<' => $this->compareNumeric($value, $ruleValue, '<'),
            '>=' => $this->compareNumeric($value, $ruleValue, '>='),
            '<=' => $this->compareNumeric($value, $ruleValue, '<='),
            'in' => $this->evaluateIn($value, $ruleValue),
            'not_in' => !$this->evaluateIn($value, $ruleValue),
            'between' => $this->evaluateBetween($value, $ruleValue),
            'contains' => $this->evaluateContains($value, $ruleValue),
            'starts_with' => $this->evaluateStartsWith($value, $ruleValue),
            'ends_with' => $this->evaluateEndsWith($value, $ruleValue),
            'regex' => $this->evaluateRegex($value, $ruleValue),
            default => false,
        };
    }

    /**
     * Compare two values with type awareness
     */
    private function compareValues($value, $ruleValue, $operator): bool
    {
        // Handle numeric comparison
        if (is_numeric($value) && is_numeric($ruleValue)) {
            return match($operator) {
                '=' => (float)$value === (float)$ruleValue,
                '!=' => (float)$value !== (float)$ruleValue,
                default => false,
            };
        }

        // Handle string comparison (case-insensitive)
        return match($operator) {
            '=' => strtolower(trim($value)) === strtolower(trim($ruleValue)),
            '!=' => strtolower(trim($value)) !== strtolower(trim($ruleValue)),
            default => false,
        };
    }

    /**
     * Compare numeric values
     */
    private function compareNumeric($value, $ruleValue, $operator): bool
    {
        if (!is_numeric($value) || !is_numeric($ruleValue)) {
            return false;
        }

        $numValue = (float)$value;
        $numRuleValue = (float)$ruleValue;

        return match($operator) {
            '>' => $numValue > $numRuleValue,
            '<' => $numValue < $numRuleValue,
            '>=' => $numValue >= $numRuleValue,
            '<=' => $numValue <= $numRuleValue,
            default => false,
        };
    }

    /**
     * Evaluate 'in' operator (value is in list)
     */
    private function evaluateIn($value, $ruleValue): bool
    {
        $allowedValues = array_map('trim', explode(',', $ruleValue));
        $valueStr = strtolower(trim($value));

        foreach ($allowedValues as $allowedValue) {
            if (strtolower($allowedValue) === $valueStr) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate 'between' operator (expects format like "min,max")
     */
    private function evaluateBetween($value, $ruleValue): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $range = array_map('trim', explode(',', $ruleValue));
        if (count($range) !== 2) {
            return false;
        }

        $min = $range[0];
        $max = $range[1];

        if (!is_numeric($min) || !is_numeric($max)) {
            return false;
        }

        $numValue = (float)$value;
        return $numValue >= (float)$min && $numValue <= (float)$max;
    }

    /**
     * Evaluate 'contains' operator
     */
    private function evaluateContains($value, $ruleValue): bool
    {
        return str_contains(strtolower($value), strtolower($ruleValue));
    }

    /**
     * Evaluate 'starts_with' operator
     */
    private function evaluateStartsWith($value, $ruleValue): bool
    {
        return str_starts_with(strtolower($value), strtolower($ruleValue));
    }

    /**
     * Evaluate 'ends_with' operator
     */
    private function evaluateEndsWith($value, $ruleValue): bool
    {
        return str_ends_with(strtolower($value), strtolower($ruleValue));
    }

    /**
     * Evaluate 'regex' operator
     */
    private function evaluateRegex($value, $ruleValue): bool
    {
        try {
            return preg_match($ruleValue, $value) === 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get compatibility summary for a product-car model pair
     */
    public function getCompatibilitySummary(Product $product, CarModel $carModel): array
    {
        $rules = $product->compatibilityRules()->with('attribute')->get();
        $carModelAttributes = $carModel->attributes()->withPivot('value')->get()->keyBy('id');

        $summary = [
            'is_compatible' => true,
            'total_rules' => $rules->count(),
            'passed_rules' => 0,
            'failed_rules' => 0,
            'rule_details' => []
        ];

        foreach ($rules as $rule) {
            $attribute = $carModelAttributes->get($rule->attribute_id);
            $carModelValue = $attribute?->pivot?->value;

            $ruleResult = [
                'attribute_name' => $rule->attribute->name,
                'operator' => $rule->operator,
                'rule_value' => $rule->value,
                'car_model_value' => $carModelValue ?? 'N/A',
                'passes' => false,
                'formatted_rule' => $this->formatRule($rule)
            ];

            if ($carModelValue && $this->evaluateRule($rule, $carModelValue)) {
                $ruleResult['passes'] = true;
                $summary['passed_rules']++;
            } else {
                $summary['is_compatible'] = false;
                $summary['failed_rules']++;
            }

            $summary['rule_details'][] = $ruleResult;
        }

        return $summary;
    }

    /**
     * Format a rule for display
     */
    public function formatRule(CompatibilityRule $rule): string
    {
        return match($rule->operator) {
            '=' => "Must be {$rule->value}",
            '!=' => "Must not be {$rule->value}",
            '>' => "Must be greater than {$rule->value}",
            '<' => "Must be less than {$rule->value}",
            '>=' => "Must be greater than or equal to {$rule->value}",
            '<=' => "Must be less than or equal to {$rule->value}",
            'in' => "Must be one of: {$rule->value}",
            'not_in' => "Must not be any of: {$rule->value}",
            'between' => "Must be between {$rule->value}",
            'contains' => "Must contain: {$rule->value}",
            'starts_with' => "Must start with: {$rule->value}",
            'ends_with' => "Must end with: {$rule->value}",
            'regex' => "Must match pattern: {$rule->value}",
            default => "{$rule->operator} {$rule->value}",
        };
    }

    /**
     * Clear compatibility cache for a car model
     */
    public function clearCarModelCache(CarModel $carModel): void
    {
        Cache::forget("compatible_products_car_model_{$carModel->id}");
    }

    /**
     * Clear compatibility cache for a product
     */
    public function clearProductCache(Product $product): void
    {
        Cache::forget("compatible_car_models_product_{$product->id}");
    }

    /**
     * Clear all compatibility caches
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }
}
