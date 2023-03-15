<?php

namespace Visit;

class BlockManager
{
    public function __construct()
    {
        add_action('acf/init', [$this,'registerBlockTemplate']);
    }

    public function registerBlockTemplate()
    {
        $postTypes = get_post_types();
        foreach ($postTypes as $postType) {
            $postTypeObject = get_post_type_object($postType);
            $postTypeObject->template =
            array(
                array(
                    'acf/container', array(),
                    array(
                        array( 'core/columns', array(),
                            array(
                                array( 'core/column', array() ),
                                array( 'core/column', array( 'backgroundColor' => 'white'),
                                    array(
                                        array( 'acf/margin', array( 'data' => array( 'field_61bc61423f527' => 4 ) ),
                                            array(
                                                array( 'core/heading', array( 'placeholder' => 'Rubrik' ) ),
                                                array( 'core/paragraph', array( 'placeholder' => 'Innehåll' ) )
                                            )
                                        ),
                                    ),

                                ),
                            ),
                        ),
                    ),
                ),
                );
        }
    }
}
