<?php 
  $args = array(
      'post_type'=>'advertising',
      'posts_per_page'=> 1,
      'orderby' => 'rand'
  );

  $eq_ads = get_posts( $args ); 

  $all_mobile_ads = array(); //master array of all mobile ad info

  /*
  *   Retrieve all the data for ads, and push them into a more easily manipulatable array
  */

  foreach($eq_ads as $key=>$eq_ad):

      $mobile_ad = array(); //Top level descriptors for ad
      $mobile_ad_details = array(); // Details for all available art

      $slugify = get_the_title($eq_ad->ID);
      $slugify = sanitize_title_with_dashes($slugify);

      $mobile_ad['ad_id'] = $eq_ad->ID;
      $mobile_ad['ad_title'] = get_the_title($eq_ad->ID);
      $mobile_ad['ad_slug'] = $slugify;

       if( have_rows('mobile_ads', $eq_ad->ID) ):

          $i = 0;
          while ( have_rows('mobile_ads', $eq_ad->ID) ) : the_row();
              
              // Push repeater art-specific details into a sub-array
              $mobile_ad_details[$i]['ad_art'] = get_sub_field('mobile_ad_image', $eq_ad->ID);
              $mobile_ad_details[$i]['ad_url'] = get_sub_field('mobile_ad_url', $eq_ad->ID);
              $mobile_ad_details[$i]['ad_alttag'] = sanitize_title_with_dashes(get_the_title($eq_ad->ID));
              
              // Add sub-array to main ad array
              $mobile_ad['art_combo'] = $mobile_ad_details;
          $i++;

          endwhile;
      endif;

      $all_mobile_ads[$key] = $mobile_ad;

  endforeach

// Loop through the array of new, correctly formatted ads 
foreach($all_mobile_ads as $formatted_mobile_ad):

    // Count how many types of art there are  
    $number_of_art_options = count($formatted_mobile_ad['art_combo']) - 1;

    // Randomly select a piece of art  
    $x = rand(0, $number_of_art_options);?>


    <?php 
    $mobile_ad = '<a class="mobile-callout" href="' . $formatted_mobile_ad['art_combo'][$x]['ad_url'] . '">';
    $mobile_ad .= '<img onload="ga(\'send\', \'event\', \'Advertising\', \'Impression\', ' . $formatted_mobile_ad['ad_slug'] . 'mobile-art-' . $x . ');" src="'. $formatted_mobile_ad['art_combo'][$x]['ad_art'] . '"/>';
    $mobile_ad .= '</a>';

    return $mobile_ad;

endforeach;?>



