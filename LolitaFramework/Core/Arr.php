<?php

namespace liveeditor\LolitaFramework\Core;

use \ArrayAccess;

class Arr
{

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function add($array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Build a new array using a callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     *
     * @deprecated since version 5.2.
     */
    public static function build($array, callable $callback)
    {
        $results = [];

        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     * @return array
     */
    public static function collapse($array)
    {
        $results = [];

        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }

            $results = array_merge($results, $values);
        }

        return $results;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     * @return array
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function except($array, $keys)
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? Data::interpret($default) : reset($array);
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return Data::interpret($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function last($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? Data::interpret($default) : end($array);
        }

        return static::first(array_reverse($array), $callback, $default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        $result = [];

        foreach ($array as $item) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (is_array($item)) {
                if ($depth === 1) {
                    $result = array_merge($result, $item);
                    continue;
                }

                $result = array_merge($result, static::flatten($item, $depth - 1));
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Flatten a multi-dimensional array into a single level with saving keys.
     *
     * @param  arrya $array
     * @param  string $prefix
     * @return array
     */
    public static function flattenWithKeys($array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + self::flattenWithKeys($value, $prefix . $key . '_');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return Data::interpret($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return Data::interpret($default);
            }
        }

        return $array;
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @return bool
     */
    public static function has($array, $key)
    {
        if (! $array) {
            return false;
        }

        if (is_null($key)) {
            return false;
        }

        if (static::exists($array, $key)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array  $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array  $array
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];

        list($value, $key) = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = Data::get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = Data::get($item, $key);

                $results[ $itemKey ] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @param  string  $separator
     * @return array
     */
    public static function set(&$array, $key, $value, $separator = '.')
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode($separator, $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @return array
     */
    public static function sortRecursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }

        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function where($array, callable $callback)
    {
        $filtered = [];

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Remove empty elements
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @param  array $arr --- array with empty elements.
     * @return array --- array without empty elements
     */
    public static function removeEmpty($arr)
    {
        return array_filter($arr, array( __CLASS__, 'removeEmptyCheck'));
    }

    /**
     * Check if empty.
     * It's need for PHP 5.2.4 version
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @param  [type] $var variable.
     * @return boolean
     */
    public static function removeEmptyCheck($var)
    {
        return '' != $var;
    }

    /**
     * Join array to string
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @param  array $arr array like 'key' => 'value'.
     * @param  string $pattern join patter.
     * @return string joined string.
     */
    public static function join($arr = array(), $pattern = '%s="%s"')
    {
        $arr    = static::removeEmpty($arr);
        $result = array();
        foreach ($arr as $key => $value) {
            $result[] = sprintf($pattern, $key, $value);
        }
        return implode(' ', $result);
    }

    /**
     * Making l10n script data from array
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @param  array $l10n array to convert into l10n script.
     * @return string l10n string.
     */
    public static function l10n($object_name, array $l10n)
    {
        if (is_array($l10n)) {
            foreach ($l10n as $key => $value) {
                if (!is_scalar($value)) {
                    continue;
                }

                $l10n[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
            }
            return sprintf(
                '<script type="text/javascript">var %s = %s;</script>',
                $object_name,
                wp_json_encode($l10n)
            );
        }
        return '';
    }

    /**
     * Filter
     *
     * @author Guriev Eugen <gurievcreative@gmail.com>
     * @param  array $array_to_filter
     * @param  array $query
     * @return array
     */
    public static function filter(array $array_to_filter, array $query)
    {
        return self::where(
            $array_to_filter,
            function ($key, $el) use ($query) {
                foreach ($query as $options) {
                    if (array_key_exists('key', $options) && array_key_exists('value', $options)) {
                        $key     = $options['key'];
                        $value   = $options['value'];
                        $compare = Arr::get($options, 'compare', '==');
                        switch ($compare) {
                            case '>':
                                if ((float) $value > (float)Arr::get($el, $key, null)) {
                                    return false;
                                }
                                break;
                            case '<':
                                if ((float) $value < (float)Arr::get($el, $key, null)) {
                                    return false;
                                }
                                break;
                            case '!=':
                                if ($value == Arr::get($el, $key, null)) {
                                    return false;
                                }
                                break;
                            default:
                                if ($value != Arr::get($el, $key, null)) {
                                    return false;
                                }
                        }
                    }
                }
                return true;
            }
        );
    }

    /**
     * Get key position by array
     *
     * @param  array $array
     * @param  mixed $key
     * @return int | false
     */
    public static function keyPosition($array, $key)
    {
        return array_search($key, array_keys($array));
    }

    /**
     * All items before this item key
     *
     * @param  array $array
     * @param  mixed $item_key
     * @param  boolean $inclusive
     * @return mixed
     */
    public static function before(array $array, $item_key, $inclusive = false)
    {
        $position = self::keyPosition($array, $item_key);
        if (false === $position) {
            return false;
        }
        if ($inclusive) {
            $position++;
        }
        return array_slice($array, 0, $position);
    }

    /**
     * All items after this item key
     *
     * @param  array $array
     * @param  mixed $item_key
     * @param  boolean $inclusive
     * @return mixed
     */
    public static function after(array $array, $item_key, $inclusive = false)
    {
        $position = self::keyPosition($array, $item_key);
        if (false === $position) {
            return false;
        }
        if ($inclusive) {
            $position--;
        }
        return array_slice($array, $position+1, count($array));
    }
}
