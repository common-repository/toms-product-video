<?php
/**
 * Plugin Name:       TomS Product Video
 * Description:       The simplest video player for woocommerce single product.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Tom Sneddon
 * Author URI:        https://TomS-Caprice.org
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       toms-product-video
 * Domain Path:		  /languages
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( plugin_dir_path( __FILE__) . 'library/toms-allowed-elements.php' );
require_once( plugin_dir_path( __FILE__) . 'library/toms-switch.php' );
require_once( plugin_dir_path( __FILE__) . 'library/toms-product-video-html.php' );
require_once( plugin_dir_path( __FILE__) . 'library/toms-html-in-product-image.php' );

if( !class_exists('TomSProductVideo') ){
    class TomSProductVideo{
        function __construct(){
            $allowed_elements   = new TomSAllowedElements();
            $this->allowed_html = $allowed_elements->AllowedElements();
            $this->allowed_protocols = $allowed_elements->AllowedProtocols();

            $product_video = new TomSProductVideoHTML();
            $this->product_video = $product_video;

            add_action( 'init', array($this, 'TomSProductVideoINIT') );

            add_filter( 'woocommerce_product_data_tabs', array($this, 'TomS_WVP_Tab'), 10, 1 );
            add_action( 'woocommerce_product_data_panels', array($this, 'TomS_Edit_WVP') );
            add_action( 'woocommerce_admin_process_product_object', array($this, 'TomS_Save_WVP') );
            
            //放置到产品主图下方
            add_action( 'woocommerce_product_thumbnails', array($this, 'show_in_image_thumbnails'), 99 );

            //放置到购物车按钮下方
            add_action( 'woocommerce_product_meta_start', array($this, 'show_on_after_add_cart') );

            //放置到产品Tab选项卡上方
            add_action( 'woocommerce_after_single_product_summary', array($this, 'show_on_before_product_tabs') );

            //移除 产品描述 H2标题
            add_filter('woocommerce_product_description_heading', array($this, 'remove_description_heading'), 10, 1 );
            //添加 自定义内容到 Tab 下面 注意：使用js将内容挪到描述内容之上，Tab之下。
            add_action( 'woocommerce_product_after_tabs', array($this, 'show_on_description_field'), 10 );
            add_action( 'woocommerce_product_after_tabs', array($this, 'tomswvp_js'), 15 );
            //检查产品描述内容是否为空
            add_filter( 'woocommerce_product_tabs', array($this, 'tomswvp_default_product_tabs'), 10, 1 );

            add_filter( 'load_textdomain_mofile', array($this, 'TomSProductVideoLocale'), 10, 2);
        }

        function TomSProductVideoINIT(){
            load_plugin_textdomain( 'toms-product-video', false, false );
        }

        function TomSProductVideoLocale($mofile, $domain){
            if( 'toms-product-video' === $domain ){
                $mofile = plugin_dir_path( __FILE__ ) . 'languages/toms-product-video-' . get_locale() . '.mo';
            }
            return $mofile;
        }

        function show_on_description_field(){
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "0" ){
                echo wp_kses( $this->product_video->TomS_Display_WVP(), $this->allowed_html, $this->allowed_protocols);
            }
        }

        function show_in_image_thumbnails(){
            $product_video = new TomSHtmlInImage();
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "1" ){
                echo wp_kses( $product_video->TomS_Display_WVP(), $this->allowed_html, $this->allowed_protocols);
            }
        }

        function show_on_after_add_cart(){
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "2" ){
                echo wp_kses( $this->product_video->TomS_Display_WVP(), $this->allowed_html, $this->allowed_protocols);
            }
        }

        function show_on_before_product_tabs(){
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "3" ){
                echo wp_kses( $this->product_video->TomS_Display_WVP(), $this->allowed_html, $this->allowed_protocols);
            }
        }

        function TomS_WVP_Tab( $default_tabs ){
            $default_tabs['toms_wvp_tab'] = array(
                'label'   =>  __( 'Product Video', 'toms-product-video' ),
                'target'  =>  'toms_wvp_tab_data',
                'priority' => 15,
                'class'   => array("toms-wvp-tab")
            );
            return $default_tabs;
        }

        function TomS_Edit_WVP(){
            //引入编辑器CSS
            wp_enqueue_style( 'toms-wvp-editor', plugin_dir_url( __FILE__ ) . 'assets/css/toms-wvp-editor.css' );
            ?>
            <div id="toms_wvp_tab_data" class="panel woocommerce_options_panel">
                <div class="toms-wvp-editor">
                    <div class="toms-wvp-heading">
                        <div class="toms-wvp-heading-content">
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/img/toms-product-video.png';?>" />
                            <h2><?php _e( 'Product Video', 'toms-product-video' ); ?></h2>
                        </div>
                        <a class="toms-wvp-review" href="https://wordpress.org/support/plugin/toms-product-video/reviews/#new-post" >
                            <div class="toms-wvp-stars">
                                <i class="toms-wvp-star"></i>
                                <i class="toms-wvp-star"></i>
                                <i class="toms-wvp-star"></i>
                                <i class="toms-wvp-star"></i>
                                <i class="toms-wvp-star"></i>
                            </div>
                            <div class="toms-wvp-review-notice"><?php _e('Give 5-Star for us', 'toms-product-video' ); ?></div>
                        </a>
                    </div>
                    <?php
                    //Video Url
                    $video_url = array(
                        'id'            => 'toms_wvp_video_url',
                        'label'         => __( 'Video Url', 'toms-product-video' ),
                        'class'         => 'short toms-wvp',
                        'desc_tip'      => true,
                        'description'   => __( 'Enter the url of your video or choose one from media library.', 'toms-product-video' ),
                    );
                    woocommerce_wp_text_input( $video_url );

                    //上传按钮
                    if ( ! did_action( 'wp_enqueue_media' ) ) {
                        wp_enqueue_media();
                    }
                    $upload_button = array(
                        'id'            => 'toms_wvp_video_upload_button',
                        'label'         => '',
                        'class'         => 'short toms-wvp-video-url-button button-primary button-large',
                        'type'          => 'button',
                        'value'         => __('Choose a video', 'toms-product-video' ),
                        'desc_tip'      => true,
                    );
                    woocommerce_wp_text_input( $upload_button );

                    //Poster Image URL
                    $video_poster_image_url = array(
                        'id'            => 'toms_wvp_video_poster_image_url',
                        'label'         => __( 'Video Poster Image', 'toms-product-video' ),
                        'class'         => 'short toms-wvp',
                        'desc_tip'      => true,
                        'description'   => __( 'Enter an image url or choose one from media library.', 'toms-product-video' ),
                    );
                    woocommerce_wp_text_input( $video_poster_image_url );

                    //Poster Image上传按钮
                    $upload_poster_image_button = array(
                        'id'            => 'toms_wvp_video_poster_image_upload_button',
                        'label'         => '',
                        'class'         => 'short toms-wvp-video-url-button button-primary button-large',
                        'type'          => 'button',
                        'value'         => __('Choose an image', 'toms-product-video' ),
                        'desc_tip'      => true,
                    );
                    woocommerce_wp_text_input( $upload_poster_image_button );

                    //Position of video
                    $position = array(
                        'id'            => 'toms_wvp_position',
                        'label'         => __( 'Video Position', 'toms-product-video' ),
                        'class'         => 'short toms-wvp-position',
                        'desc_tip'      => true,
                        'description'   => __( 'Select a position for video display.', 'toms-product-video' ),
                        'options'       => array(
                            __('In Product Description', 'toms-product-video' ),
                            __('Under Product Image', 'toms-product-video' ),
                            __('After Add to card button', 'toms-product-video' ),
                            __('Before Product Tabs', 'toms-product-video' ),
                        ),
                        'custom_attributes' => array(
                            'onchange'  => 'disabledUnsupportFunction(this)'
                        ),
                    );
                    woocommerce_wp_select( $position );

                    ?>
                    <div class="toms-wvp-padding" id="toms-wvp-padding-container">
                        <span class="toms-wvp-padding-details">
                            <div class="toms-wvp-padding-label toms-wvp-padding-first"><?php _e('Video Size', 'toms-product-video' ); ?></div>
                            <div class="toms-wvp-padding-content">
                                <div class="toms-wvp-padding-scroll">
                                <?php
                                    $toms_wvp_video_size_scroll = array(
                                        'id'            => 'toms_wvp_video_size_scroll',
                                        'label'         => '',
                                        'class'         => 'toms-wvp',
                                        'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_video_size') ) ? wc_get_product()->get_meta('toms_wvp_video_size') : 100,
                                        'type'          => 'range',
                                        'custom_attributes' => array(
                                            'min'   => 20,
                                            'max'   => 100,
                                            'oninput'   => "updatePadding('toms_wvp_video_size',this.value)",
                                            'onchange'   => "getPadding(this.name, 'toms_wvp_video_size')",
                                        )
                                    );
                                    woocommerce_wp_text_input( $toms_wvp_video_size_scroll );
                                ?>
                                </div>
                                <div class="toms-wvp-padding-content-right">
                                    <div class="toms-wvp-padding-output">
                                    <?php
                                        $toms_wvp_video_size = array(
                                            'id'            => 'toms_wvp_video_size',
                                            'label'         => '',
                                            'type'          => 'number',
                                            'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_video_size') ) ? wc_get_product()->get_meta('toms_wvp_video_size') : 100,
                                            'custom_attributes' => array(
                                                'onchange'  => ''
                                            )
                                        );
                                        woocommerce_wp_text_input( $toms_wvp_video_size );
                                    ?>
                                    </div>
                                    <div class="toms-wvp-padding-output-unit">
                                        <span class="toms-wvp-width-unit">%</span>
                                    </div>
                                </div>
                            </div>
                            <?php if( !wp_is_mobile() ): ?>
                                <div class="toms-wvp-padding-notice"><?php _e('When screen less than 540px will always', 'toms-product-video' );?> <span class="toms-wvp-padding-notice-heightlight">100%</span></div>
                            <?php endif; ?>
                        </span>
                        <span class="toms-wvp-padding-details">
                            <div class="toms-wvp-padding-label"><?php _e('Video Padding Top/Bottom', 'toms-product-video' ); ?></div>
                            <div class="toms-wvp-padding-content">
                                <div class="toms-wvp-padding-scroll">
                                <?php
                                    $video_padding_top_bottom_scroll = array(
                                        'id'            => 'toms_wvp_padding_top_bottom_scroll',
                                        'label'         => '',
                                        'class'         => 'toms-wvp',
                                        'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_padding_top_bottom') ) ? wc_get_product()->get_meta('toms_wvp_padding_top_bottom') : 0,
                                        'type'          => 'range',
                                        'custom_attributes' => array(
                                            'min'   => 0,
                                            'max'   => 100,
                                            'oninput'   => "updatePadding('toms_wvp_padding_top_bottom',this.value)",
                                            'onchange'  => "getPadding(this.name, 'toms_wvp_padding_top_bottom')",
                                        )
                                    );
                                    woocommerce_wp_text_input( $video_padding_top_bottom_scroll );
                                ?>
                                </div>
                                <div class="toms-wvp-padding-content-right">
                                    <div class="toms-wvp-padding-output">
                                    <?php
                                        $video_padding_top_bottom = array(
                                            'id'            => 'toms_wvp_padding_top_bottom',
                                            'label'         => '',
                                            'type'          => 'number',
                                            'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_padding_top_bottom') ) ? wc_get_product()->get_meta('toms_wvp_padding_top_bottom') : 0,
                                        );
                                        woocommerce_wp_text_input( $video_padding_top_bottom );
                                    ?>
                                    </div>
                                    <div class="toms-wvp-padding-output-unit">
                                        <?php
                                        $video_padding_top_bottom_unit = array(
                                            'id'            => 'toms_wvp_padding_top_bottom_unit',
                                            'label'         => '',
                                            'class'         => 'toms-wvp-padding-unit',
                                            'options'       => array(
                                                'PX',
                                                'EM',
                                                'REM',
                                                '%',
                                            )
                                        );
                                        woocommerce_wp_select( $video_padding_top_bottom_unit );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </span>
                        <span class="toms-wvp-padding-details">
                            <div class="toms-wvp-padding-label"><?php _e('Video Padding Left/Right', 'toms-product-video' ); ?></div>
                            <div class="toms-wvp-padding-content">
                                <div class="toms-wvp-padding-scroll">
                                <?php
                                    $toms_wvp_padding_left_right_scroll = array(
                                        'id'            => 'toms_wvp_padding_left_right_scroll',
                                        'label'         => '',
                                        'class'         => 'toms-wvp',
                                        'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_padding_left_right') ) ? wc_get_product()->get_meta('toms_wvp_padding_left_right') : 0,
                                        'type'          => 'range',
                                        'custom_attributes' => array(
                                            'min'   => 0,
                                            'max'   => 100,
                                            'oninput'   => "updatePadding('toms_wvp_padding_left_right',this.value)",
                                            'onchange'  => "getPadding(this.name, 'toms_wvp_padding_left_right')",
                                        )
                                    );
                                    woocommerce_wp_text_input( $toms_wvp_padding_left_right_scroll );
                                ?>
                                </div>
                                <div class="toms-wvp-padding-content-right">
                                    <div class="toms-wvp-padding-output">
                                    <?php
                                        $toms_wvp_padding_left_right = array(
                                            'id'            => 'toms_wvp_padding_left_right',
                                            'label'         => '',
                                            'type'          => 'number',
                                            'value'         => esc_textarea( wc_get_product()->get_meta('toms_wvp_padding_left_right') ) ? wc_get_product()->get_meta('toms_wvp_padding_left_right') : 0,
                                        );
                                        woocommerce_wp_text_input( $toms_wvp_padding_left_right );
                                    ?>
                                    </div>
                                    <div class="toms-wvp-padding-output-unit">
                                        <?php
                                        $toms_wvp_padding_left_right_unit = array(
                                            'id'            => 'toms_wvp_padding_left_right_unit',
                                            'label'         => '',
                                            'class'         => 'toms-wvp-padding-unit',
                                            'options'       => array(
                                                'PX',
                                                'EM',
                                                'REM',
                                                '%',
                                            ),
                                            'custom_attributes' => array(
                                                'onchange'  => 'updatePaddingMaxValue("toms_wvp_padding_left_right_scroll", "toms_wvp_padding_left_right", this)'
                                            ),
                                        );
                                        woocommerce_wp_select( $toms_wvp_padding_left_right_unit );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="toms-wvp-copyright"><?php _e( 'Powered by', 'toms-product-video' ); ?> <a href="https://toms-caprice.org" target="_blank"><?php _e('TomS Caprice', 'toms-product-video' ); ?></a></div>
                </div>
            </div>
            <?php
            //引入编辑器JS
            wp_enqueue_script( 'toms-wvp-editor', plugin_dir_url( __FILE__ ) . 'assets/js/toms-wvp-editor.js' );
        }

        function TomS_Save_WVP( $product ){
            //保存Video url
            $video_url  = isset( $_POST['toms_wvp_video_url'] ) ? sanitize_url( $_POST['toms_wvp_video_url'] ) : '';
            $product->update_meta_data( 'toms_wvp_video_url', $video_url );

            //保存Video Poster image url
            $toms_wvp_video_poster_image_url  = isset( $_POST['toms_wvp_video_poster_image_url'] ) ? sanitize_url( $_POST['toms_wvp_video_poster_image_url'] ) : '';
            $product->update_meta_data( 'toms_wvp_video_poster_image_url', $toms_wvp_video_poster_image_url );

            //保存video position
            $video_position = isset( $_POST['toms_wvp_position'] ) ? sanitize_text_field( $_POST['toms_wvp_position'] ) : '';
            $product->update_meta_data( 'toms_wvp_position', $video_position );

            //保存Video size
            $toms_wvp_video_size = isset( $_POST['toms_wvp_video_size'] ) ? sanitize_text_field( $_POST['toms_wvp_video_size'] ) : '';
            $product->update_meta_data( 'toms_wvp_video_size', $toms_wvp_video_size );

            //保存Video padding
            $toms_wvp_padding_top_bottom = isset( $_POST['toms_wvp_padding_top_bottom'] ) ? sanitize_text_field( $_POST['toms_wvp_padding_top_bottom'] ) : '';
            $product->update_meta_data( 'toms_wvp_padding_top_bottom', $toms_wvp_padding_top_bottom );

            $toms_wvp_padding_left_right = isset( $_POST['toms_wvp_padding_left_right'] ) ? sanitize_text_field( $_POST['toms_wvp_padding_left_right'] ) : '';
            $product->update_meta_data( 'toms_wvp_padding_left_right', $toms_wvp_padding_left_right );

            //保存Padding size Unit
            $toms_wvp_padding_top_bottom_unit = isset( $_POST['toms_wvp_padding_top_bottom_unit'] ) ? sanitize_text_field( $_POST['toms_wvp_padding_top_bottom_unit'] ) : '';
            $product->update_meta_data( 'toms_wvp_padding_top_bottom_unit', $toms_wvp_padding_top_bottom_unit );

            $toms_wvp_padding_left_right_unit = isset( $_POST['toms_wvp_padding_left_right_unit'] ) ? sanitize_text_field( $_POST['toms_wvp_padding_left_right_unit'] ) : '';
            $product->update_meta_data( 'toms_wvp_padding_left_right_unit', $toms_wvp_padding_left_right_unit );

            $product->save_meta_data();
        }

        function tomswvp_js(){
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "0" ){
                //通过JS将视频放到描述内容的第一个元素之上
                $html_js = '';
                ob_start(); ?>
                        var WooDescriptionTab   = document.getElementById('tab-description');
                        var TomSWVP             = document.getElementById('tomswvp');
                        WooDescriptionTab.prepend(TomSWVP);
                <?php $html_js .= ob_get_contents();
                ob_end_clean();
                wp_print_inline_script_tag( $html_js, [ 'type' => 'text/javascript' ] );
            }
        }

        //即使产品描述内容为空也显示TAB
        function tomswvp_default_product_tabs($tabs) {
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if ( empty($tabs['description']) && $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "0") {
                $tabs['description'] = array(
                    'title'    => __( 'Description', 'woocommerce' ),
                    'priority' => 10,
                    'callback' => 'woocommerce_product_description_tab',
                );
            }
            return $tabs;
        }

        function remove_description_heading($heading){
            $video_url  = esc_textarea( wc_get_product()->get_meta( 'toms_wvp_video_url' ) );
            if ( $video_url && esc_textarea( wc_get_product()->get_meta('toms_wvp_position') ) === "0") {
                return '';
            }
            return $heading;
        }
    }
    new TomSProductVideo();
}