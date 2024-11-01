<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('TomSProductVideoHTML') ){
    class TomSProductVideoHTML{
        function TomS_Display_WVP(){
            $video_url                  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) ) ? wc_get_product()->get_meta( 'toms_wvp_video_url' ) : '';
            $video_size                 = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_size' ) ) ? wc_get_product()->get_meta( 'toms_wvp_video_size' ) : 100;
            $video_padding_top_bottom   = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_padding_top_bottom' ) ) ? wc_get_product()->get_meta( 'toms_wvp_padding_top_bottom' ) : 0;
            $video_padding_left_right   = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_padding_left_right' ) ) ? wc_get_product()->get_meta( 'toms_wvp_padding_left_right' ) : 0;
            $video_padding_top_bottom_unit   = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_padding_top_bottom_unit' ) ) ? wc_get_product()->get_meta( 'toms_wvp_padding_top_bottom_unit' ) : 0;
            $video_padding_left_right_unit   = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_padding_left_right_unit' ) ) ? wc_get_product()->get_meta( 'toms_wvp_padding_left_right_unit' ) : 0;
            $video_poster_image_url     = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_poster_image_url' ) ) ? wc_get_product()->get_meta( 'toms_wvp_video_poster_image_url' ) : '';

            $switch = new TomSSwitch();
            
            $top_bottom_unit = $switch->unit($video_padding_top_bottom_unit);
            $left_right_unit = $switch->unit($video_padding_left_right_unit);

            $top_bottom_max_value = $switch->max_value($top_bottom_unit);
            $left_right_max_value = $switch->max_value($left_right_unit);

            if( $video_size < 20 ){
                $video_size = 20;
            }

            if( $video_padding_left_right > $left_right_max_value ){
                $video_padding_left_right = $left_right_max_value;
            }

            if( $video_url ) {

                $video_html = '';
                ob_start(); ?>
                <style>
                    #tomswvp .toms-woo-video-player video{
                        max-width: <?php echo esc_attr( $video_size ); ?>%;
                        height: auto;
                        padding-top: <?php echo esc_attr( $video_padding_top_bottom . $top_bottom_unit ); ?>;
                        padding-bottom: <?php echo esc_attr( $video_padding_top_bottom . $top_bottom_unit ); ?>;
                        padding-left:<?php echo esc_attr( $video_padding_left_right . $left_right_unit ); ?>;
                        padding-right: <?php echo esc_attr( $video_padding_left_right . $left_right_unit ); ?>;
                    }
                    @media screen and (max-width: 540px){
                        #tomswvp .toms-woo-video-player video{
                            max-width: 100%;
                            padding-left: 0px;
                            padding-right: 0px; 
                        }
                    }
                </style>
                <div class="toms-wvp" id="tomswvp">
                    <div class="toms-woo-video-player" style="text-align: center" oncontextmenu="return false">
                        <video 
                            controls
                            disablepictureinpicture="true"
                            controlslist="nodownload"
                            preload="metadata"
                            <?php 
                                if( $video_poster_image_url ){
                                    $poster_url = $video_poster_image_url;
                                    echo 'poster="';
                                    echo esc_url( $poster_url );
                                    echo '"';
                                }    
                            ?>
                        >
                            <source src="<?php echo esc_url( $video_url ); ?>">     
                            <p><?php _e( 'Your browser doesn\'t support HTML5 video.', 'toms-product-video' ); ?></p>
                        </video>
                    </div>
                </div>
                <?php 
                
                $video_html .= ob_get_clean();
                return $video_html;
            }
        }
    }
}