<?php

namespace Losys\Demo;

use GuzzleHttp\Utils as GuzzleUtils;

class Utils
{
    /**
     * The Losys-API allows filtering project-listings based on various
     * criteria. Some of those are rather complex.
     * For example the same filterable project-attribute may exist multiple
     * times - in different companies or languages - and thus have multiple
     * IDs. Based oin the type of the attribute the filter-value may also
     * require different inputs.
     * To simplify building a UI with multiple-choice filters we use a
     * different format for our HTML-UI.
     * This function converts the format received from the UI-HTML-form into
     * the format required by the Losys-API. Mind that this is demo-code. It
     * does by far not cover all available filters, fields or field-types.
     *
     * @param array<string, mixed>|null $uiFilterValues
     * @param string $prefix
     * @return array<string, mixed>
     */
    public static function convertUiFilterValues(?array $uiFilterValues = null,
                                                 string $prefix = 'filter_'): array
    {
        $filter_values =
            array_map(
                fn($value) =>
                is_array($value)
                    ? self::array_flatten(
                        array_merge(
                            array_map(
                                fn($item) => GuzzleUtils::jsonDecode($item ?: 'null', true),
                                $value
                            )))
                        : GuzzleUtils::jsonDecode($value, true),
                array_filter(
                    $uiFilterValues ?? $_REQUEST,
                    fn($value, $key) => str_starts_with($key, $prefix),
                    ARRAY_FILTER_USE_BOTH
                )
            );
        $filter_values =
            array_combine(
                array_map(
                    fn($key) => substr($key, strlen($prefix)),
                    array_keys($filter_values)
                ),
                $filter_values
            );

        // special handling for attributes
        $attribute_filters = [];
        $attribute_filter_keys = array_filter(array_keys($filter_values), fn($key) => str_starts_with($key, 'attributes_'));
        foreach($attribute_filter_keys as $key)
        {
            if (!empty($value = $filter_values[$key])
                && ($value !== [null]))
            {
                $ids = explode('_', substr($key, strlen('attributes_')));
                $attribute_filters[] = [
                    'id' => $ids,
                    'value' => $value
                ];
            }

            unset($filter_values[$key]);
        }
        $filter_values['attributes'] = $attribute_filters;

        return $filter_values;
    }

    public static function array_flatten(array $values): array
    {
        $result = [];
        foreach($values as $value)
            if (is_array($value)) {
                foreach(self::array_flatten($value) as $subValue)
                    $result[] = $subValue;
            } else
                $result[] = $value;
        return $result;
    }

    public static function array_hide_secrets(?array $array): ?array
    {
        if (is_null($array))
            return null;

        $result = [];

        foreach($array as $key => $value)
            if (str_contains($normalizedKey = strtolower($key), 'secret')
                || str_contains($normalizedKey, 'authentication')
                || str_contains($normalizedKey, 'authorization')) {
                $result[$key] = '***';
            } elseif (is_array($value)) {
                $result[$key] = self::array_hide_secrets($value);
            } else
                $result[$key] = $value;

        return $result;
    }
}