<?php
// Creating the widget 
class wprs_cce_widget extends WP_Widget {
 
    function __construct() {
        parent::__construct(
        
        // Base ID of your widget
        'wprs_cce_widget', 
        
        // Widget name will appear in UI
        esc_html__('Cryptocurrency Exchange Widget', 'cryptocurrency-exchange'), 
        
        // Widget description
        array( 'description' => esc_html__( 'Widget to display realtime cryptocurrency exchange prices', 'cryptocurrency-exchange' ), ) 
        );
    }
     
    // Creating widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $shortcode_alias = $instance[ 'wprs_cce_shortcode_alias' ];
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];
        // This is where you run the code and display the output
        echo Cryptocurrency_Exchange_Public::cryptocurenccy_exchange_shortcode(['id' => $shortcode_alias]);

        echo $args['after_widget'];
    }
                    
    // Widget Backend 
    public function form( $instance ) {
        $title = '';
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }

        $shorcode_ID = '';
        if ( isset( $instance[ 'wprs_cce_shortcode_alias' ] ) ) {
            $shorcode_ID = $instance[ 'wprs_cce_shortcode_alias' ];
        }

        $args = array(
            'post_type'=> 'wprs_cce_shortcode',
            'posts_per_page' => 50,
            'order_by'    => 'post_title',
            'order'    => 'ASC'
            ); 
        
        $the_posts = get_posts( $args );

        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?=esc_html__( 'Title:', 'cryptocurrency-exchange'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'wprs_cce_shortcode_alias' ); ?>"><?=esc_html__( 'Cryptocurrency Shortcode:', 'cryptocurrency-exchange'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'wprs_cce_shortcode_alias' ); ?>" name="<?php echo $this->get_field_name( 'wprs_cce_shortcode_alias' ); ?>">
                <option value="0"> -- <?=esc_html__('Select cryptocurrency shortcode', 'cryptocurrency-exchange');?> -- </option>
        <?php 
        if( count( $the_posts) ) : 
            foreach( $the_posts as $shortcode) {
        ?>
            <option value="<?=$shortcode->ID;?>" 
                <?=$shortcode->ID == $shorcode_ID ? ' selected': '';?>>
                <?=$shortcode->post_title;?>
            </option>
        <?php
            }
        endif;
    
        ?>
            </select>
        </p>
    <?php
    }
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['wprs_cce_shortcode_alias'] = ( ! empty( $new_instance['wprs_cce_shortcode_alias'] ) ) ? strip_tags( $new_instance['wprs_cce_shortcode_alias'] ) : '';
        return $instance;
    }
} 
    