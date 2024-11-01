<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('TomSHtmlInImage') ){
    class TomSHtmlInImage{
        function TomS_Display_WVP(){
            $video_url                  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) ) ? wc_get_product()->get_meta( 'toms_wvp_video_url' ) : '';
            $video_poster_image_url     = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_poster_image_url' ) ) ? wc_get_product()->get_meta( 'toms_wvp_video_poster_image_url' ) : '';
            // $data_thumb_url             = $video_poster_image_url;

            // if( empty( $data_thumb_url ) ){
                $data_thumb_url         = dirname( plugin_dir_url( __FILE__ ) ). '/assets/img/toms-wvp-preview.png';
            //}

            if( $video_url ) {
                $video_html = '';
                ob_start(); ?>
                <div    data-thumb="<?php echo esc_url( $data_thumb_url ); ?>"
                        data-thumb-alt="Product Video"
                        data-thumb-title="Product Video"
                        class="woocommerce-product-gallery__image toms-woo-video-player-in-image"
                        id="toms-wvp-html"
                        style="text-align: center;"
                        oncontextmenu="return false">
                        
                        <video
                            id="toms-wvp-html5-video"
                            style="max-width: 100%; height:auto"
                            controls=""
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
                <?php
                $video_html .= ob_get_clean();
                return $video_html;
            }
        }
    }
}