<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('TomSSwitch')){
    class TomSSwitch{
        function unit( $video_unit ){
            switch ( $video_unit ) {
                case "0":
                    $unit = 'px';
                    break;
                case "1":
                    $unit = 'em';
                    break;
                case "2":
                    $unit = 'rem';
                    break;
                case "3":
                    $unit = '%';
                    break;
                default:
                    $unit = 'px';
                    break;
            }
            return $unit;
        }

        function max_value( $video_unit ){
            switch ( $video_unit ) {
                case "px":
                    $max_value = 100;
                    break;
                case "em":
                    $max_value = 25;
                    break;
                case "rem":
                    $max_value = 25;
                    break;
                case "%":
                    $max_value = 35;
                    break;
                default:
                    $max_value = 100;
                    break;
            }
            return $max_value;
        }
    }
}