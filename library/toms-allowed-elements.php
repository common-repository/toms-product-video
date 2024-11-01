<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'TomSAllowedElements' ) ){
    class TomSAllowedElements{
        function __construct(){

        }
        function AllowedElements(){
            $allowed_elements = array(
                'div'       => [
                    'class'         => [],
                    'id'            => [],
                    'style'         => [],
                    'oncontextmenu' => [],
                    'data-thumb'    => [],
                    'data-thumb-alt'    => []
                ],
                'video'     => [
                    'id'            => [],
                    'style'         => [],
                    'controls'      => [],
                    'disablepictureinpicture'   => [],
                    'controlslist'  => [],
                    'preload'       => [],
                    'poster'        => []
                ],
                'source'    => [
                    'src'           => []
                ],
                'p'         => [],
                'style'     => []
            );

            return $allowed_elements;
        }

        function AllowedProtocols(){
            return $protocols = array( 'data', 'http', 'https' );
        }
    }
}