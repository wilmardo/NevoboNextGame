<?php
/*
Plugin Name: Nevobo Next Game
Description: Laat de eerst volgende wedstrijd zien
Version: 1.0
Author: Wilmar den Ouden
Author URI: https://wilmardenouden.nl
License: MIT
*/

define('nevoboNextgame_SPVERSION','1.4.2');

// Pull parameters
function pull_shortcode( $atts ) {
  $a = shortcode_atts( array(
      'url' => 'https://api.nevobo.nl/export/team/CKM1F52/dames/1/programma.rss',
  ), $atts );

  return generateOutput($a);
}
add_shortcode( 'nevobo-nextgame', 'pull_shortcode' );

function generateOutput($a) {
  /* Set locale to Dutch */
  setlocale(LC_ALL, 'nl_NL');
  date_default_timezone_set('Europe/Amsterdam');

  require_once plugin_dir_path( __FILE__ ) . 'simplepie-' . nevoboNextgame_SPVERSION . '/autoloader.php';
  $feed = new SimplePie();
  $feed->set_feed_url($a['url']);
  $feed->enable_order_by_date(false);
  $feed->enable_cache();
  $feed->set_cache_location(plugin_dir_path( __FILE__ ) . 'cache');
  $feed->set_stupidly_fast(true);
  $feed->init();
  $maxitems = $feed->get_item_quantity( 1 );
  $item = $feed->get_item( 0 );

  //start processing rss
  if( $maxitems == 0) {
    return "<b>Feed bevat geen items</b>";
  }
  if(strpos($a['url'], 'programma.rss') !== false) {
    //remove date
    $match = substr(strstr($item->get_title(), ': '), 2);
    //get date and format properly
    $date = strtotime($item->get_date());
    $dateTime = strftime("%e %B %R", $date);

    return "<a href='" . $item->get_link() . "' target=_BLANK>" . $dateTime . " " . $match . "</a>";

  } else {
    //feed not programma or uitslagen
    return "<b>Feed URL kan niet verwerkt worden, is het wel een Nevobo Feed?</b>";
  }
}
