<?php

namespace liveeditor\LolitaFramework\Core\Decorators;

use \liveeditor\LolitaFramework\Core\Str;
use \liveeditor\LolitaFramework\Core\Arr;
use \Exception;
use \WP_Term;

class Term
{
    /**
     * Term ID.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $term_id;

    /**
     * The term's name.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $name = '';

    /**
     * The term's slug.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $slug = '';

    /**
     * The term's term_group.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $term_group = '';

    /**
     * Term Taxonomy ID.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $term_taxonomy_id = 0;

    /**
     * The term's taxonomy name.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $taxonomy = '';

    /**
     * The term's description.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $description = '';

    /**
     * ID of a term's parent term.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $parent = 0;

    /**
     * Cached object count for this term.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $count = 0;

    /**
     * Stores the term object's sanitization level.
     *
     * Does not correspond to a database field.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $filter = 'raw';

    /**
     * Retrieve Term instance.
     *
     * @since 4.4.0
     * @access public
     * @static
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @param int    $term_id  Term ID.
     * @param string $taxonomy Optional. Limit matched terms to those matching `$taxonomy`. Only used for
     *                         disambiguating potentially shared terms.
     * @return Term|WP_Error|false Term object, if found. WP_Error if `$term_id` is shared between taxonomies and
     *                                there's insufficient data to distinguish which term is intended.
     *                                False for other failures.
     */
    public static function getInstance($term_id, $taxonomy = null)
    {
        global $wpdb;

        $term_id = (int) $term_id;
        if (!$term_id) {
            return false;
        }

        $_term = wp_cache_get($term_id, 'terms');

        // If there isn't a cached version, hit the database.
        if (!$_term || ($taxonomy && $taxonomy !== $_term->taxonomy)) {
            // Grab all matching terms, in case any are shared between taxonomies.
            $terms = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE t.term_id = %d",
                    $term_id
                )
            );
            if (!$terms) {
                return false;
            }

            // If a taxonomy was specified, find a match.
            if ($taxonomy) {
                foreach ($terms as $match) {
                    if ($taxonomy === $match->taxonomy) {
                        $_term = $match;
                        break;
                    }
                }

            // If only one match was found, it's the one we want.
            } elseif (1 === count($terms)) {
                $_term = reset($terms);

            // Otherwise, the term must be shared between taxonomies.
            } else {
                // If the term is shared only with invalid taxonomies, return the one valid term.
                foreach ($terms as $t) {
                    if (!taxonomy_exists($t->taxonomy)) {
                        continue;
                    }

                    // Only hit if we've already identified a term in a valid taxonomy.
                    if ($_term) {
                        return new WP_Error(
                            'ambiguous_term_id',
                            __('Term ID is shared between multiple taxonomies'),
                            $term_id
                        );
                    }

                    $_term = $t;
                }
            }

            if (!$_term) {
                return false;
            }

            // Don't return terms from invalid taxonomies.
            if (!taxonomy_exists($_term->taxonomy)) {
                return new WP_Error(
                    'invalid_taxonomy',
                    __('Invalid taxonomy.')
                );
            }

            $_term = sanitize_term(
                $_term,
                $_term->taxonomy,
                'raw'
            );

            // Don't cache terms that are shared between taxonomies.
            if (1 === count($terms)) {
                wp_cache_add($term_id, $_term, 'terms');
            }
        }

        $term_obj = new Term($_term);
        $term_obj->filter($term_obj->filter);

        return $term_obj;
    }

    /**
     * Constructor.
     *
     * @since 4.4.0
     * @access public
     *
     * @param Term|object $term Term object.
     */
    public function __construct($term)
    {
        foreach (get_object_vars($term) as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Sanitizes term fields, according to the filter type provided.
     *
     * @since 4.4.0
     * @access public
     *
     * @param string $filter Filter context. Accepts 'edit', 'db', 'display', 'attribute', 'js', 'raw'.
     */
    public function filter($filter)
    {
        sanitize_term($this, $this->taxonomy, $filter);
    }

    /**
     * Converts an object to array.
     *
     * @since 4.4.0
     * @access public
     *
     * @return array Object as array.
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Getter.
     *
     * @since 4.4.0
     * @access public
     *
     * @param string $key Property to get.
     * @return mixed Property value.
     */
    public function __get($key)
    {
        if ('data' === $key) {
            $data = new stdClass();
            $columns = array(
                'term_id', 'name', 'slug',
                'term_group', 'term_taxonomy_id',
                'taxonomy', 'description', 'parent',
                'count'
            );
            foreach ($columns as $column) {
                $data->{$column} = isset($this->{$column}) ? $this->{$column} : null;
            }

            return sanitize_term($data, $data->taxonomy, 'raw');
        }

        if (property_exists($this, $key)) {
            return $this->{$key};
        }
        return get_term_meta($this->term_id, $key, true);
    }

    /**
     * Get term link
     *
     * @return string
     */
    public function link()
    {
        return get_term_link($this->term_id, $this->taxonomy);
    }

    /**
     * Get tax_query for get_posts() from terms
     *
     * @param  array  $terms
     * @param  string $relation
     * @return array
     */
    public static function termsToQuery(array $terms = array(), $relation = 'OR')
    {
        $query  = array();
        $by_tax = Arr::pluck($terms, 'term_id', 'taxonomy');

        foreach ($by_tax as $tax => $terms) {
            $query[] = array(
                'taxonomy' => $tax,
                'field'    => 'term_id',
                'terms'    => $terms,
            );
        }
        $query['relation'] = $relation;
        return $query;
    }

    /**
     * Sanitize post / posts
     *
     * @param  mixed $data
     * @return mixed
     */
    public static function sanitize($data)
    {
        if ($data instanceof Term) {
            return $data;
        }
        if ($data instanceof WP_Term) {
            return new Term($data);
        }

        if (is_array($data)) {
            foreach ($data as &$el) {
                $el = self::sanitize($el);
            }
        }
        return $data;
    }

    /**
     * Is term
     *
     * @param  mixed  $obj
     * @return boolean
     */
    public static function is($obj)
    {
        return ($obj instanceof WP_Term) || ($obj instanceof self);
    }
}
