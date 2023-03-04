/**
 * Plugin Name: Calendario eventi
 * Description: Plugin che visualizza gli eventi in un calendario
 * Version: 1.0
 * Author: Ventura, Antonacci
 */

function load_calendar_scripts() {
  wp_enqueue_script( 'moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', array( 'jquery' ), '2.29.1', true );
  wp_enqueue_script( 'fullcalendar', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js', array( 'jquery', 'moment' ), '3.10.2', true );
  wp_enqueue_style( 'fullcalendar-style', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css', array(), '3.10.2' );
}

add_action( 'wp_enqueue_scripts', 'load_calendar_scripts' );

function display_calendar() {
  $args = array(
    'post_type' => 'eventi',
    'posts_per_page' => -1,
    'meta_key' => 'data_inizio',
    'orderby' => 'meta_value',
    'order' => 'ASC'
  );
  
  $events = new WP_Query( $args );

  if ( $events->have_posts() ) {
    $event_data = array();
    while ( $events->have_posts() ) {
      $events->the_post();
      $id = get_the_ID();
      $title = get_the_title();
      $start = get_post_meta( $id, 'data_inizio', true );
      $end = get_post_meta( $id, 'data_fine', true );
      $event_data[] = array(
        'title' => $title,
        'start' => $start,
        'end' => $end,
        'url' => get_permalink( $id )
      );
    }
    wp_reset_postdata();
  }

  ?>
  <div id="calendar"></div>

  <script>
    jQuery(document).ready(function($) {
      $('#calendar').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,basicWeek,basicDay'
        },
        defaultDate: '<?php echo date('Y-m-d'); ?>',
        navLinks: true,
        editable: false,
        eventLimit: true,
        events: <?php echo json_encode($event_data); ?>
      });
    });
  </script>
  <?php
}

add_shortcode( 'display_calendar', 'display_calendar' );
