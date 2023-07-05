<?php
/*
Plugin Name: WP GraphQL Meta Field Ordering
Description: Adds functionality to order by a meta field as specified by the user.
Version:     1.0.0
*/

defined('ABSPATH') or die('Nope, not accessing this');

class WPGraphQLOrderByMetaField
{
    public function __construct()
    {
        add_filter('graphql_PostObjectsConnectionOrderbyEnum_values', [$this, 'register_meta_key_option']);
        add_filter('graphql_TermObjectsConnectionOrderbyEnum_values', [$this, 'register_meta_key_option']);

        add_filter('graphql_PostObjectsConnectionOrderbyInput_fields', [$this, 'register_meta_key_field']);
        add_filter('graphql_TermObjectsConnectionOrderbyInput_fields', [$this, 'register_meta_key_field']);

        add_filter('graphql_post_object_connection_query_args', [$this, 'graphql_orderby_meta'], 10, 3);
        add_filter('graphql_term_object_connection_query_args', [$this, 'graphql_orderby_meta'], 10, 3);
    }

    public function register_meta_key_option($values)
    {
        $values['META_KEY'] = ['value' => 'META_KEY', 'description' => __('Order posts by the meta value "order"', 'wp-graphql') , ];
        return $values;
    }

    public function register_meta_key_field($fields)
    {
        $fields['metaKeyField'] = ['type' => 'String', 'description' => __('Array of names to return term(s) for. Default empty.', 'wp-graphql') , ];
        return $fields;
    }

    public function graphql_orderby_meta($query_args, $source, $input)
    {
        if (isset($input['where']['orderby']) && is_array($input['where']['orderby']))
        {
            foreach ($input['where']['orderby'] as $orderby)
            {
                if (!isset($orderby['field']) || 'META_KEY' !== $orderby['field'] || !isset($orderby['metaKeyField']))
                {
                    continue;
                }

                $query_args['meta_key'] = $orderby['metaKeyField'];

                // ////////////////////////////////////////////////////////
                // Explanation for the below order values:
                // ////////////////////////////////////////////////////////
                //
                // Order is attached to the "META_KEY" value as defined on line 25 of this file.
                // This needs to be corrected to the value that has been already been processed by WP-Graphql
                // otherwise pagination will not work correctly as the value for the direction of pagination
                // is calculated here: wp-graphql/src/Data/Connection/PostObjectConnectionResolver.php | Lines 323-333
                //
                // See docs on order and orderby here for more info on how this is handled in wp queries:
                // https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
                $query_args['order'] = isset($query_args['orderby']['META_KEY']) ? $query_args['orderby']['META_KEY'] : 'DESC';

                // We run this last which overwrites the processed value for 'orderby' with
                // the required value for meta ordering as per the above Wordpress docs.
                $query_args['orderby'] = 'meta_value';
            }
        }
        return $query_args;
    }
}

new WPGraphQLOrderByMetaField();