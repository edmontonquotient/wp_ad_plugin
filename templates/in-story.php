
  <?php 
  $args = array(
      'post_type'=>'advertising',
      'posts_per_page'=> 1,
      'orderby' =>'rand'
  );

  $eq_ads = get_posts( $args ); 

  $all_desktop_ads = array(); //master array of all desktop ad info

  /*
  *   Retrieve all the data for ads, and push them into a more easily manipulatable array
  */

  foreach($eq_ads as $key=>$eq_ad):

      $desktop_ad = array(); //Top level descriptors for ad
      $desktop_ad_details = array(); // Details for all available art

      $slugify = get_the_title($eq_ad->ID);
      $slugify = sanitize_title_with_dashes($slugify);

      $desktop_ad['ad_id'] = $eq_ad->ID;
      $desktop_ad['ad_title'] = get_the_title($eq_ad->ID);
      $desktop_ad['ad_slug'] = $slugify;

       if( have_rows('desktop_ads', $eq_ad->ID) ):

          $i = 0;
          while ( have_rows('desktop_ads', $eq_ad->ID) ) : the_row();
              
              // Push repeater art-specific details into a sub-array
              $desktop_ad_details[$i]['ad_art'] = get_sub_field('desktop_ad_art', $eq_ad->ID);
              $desktop_ad_details[$i]['ad_url'] = get_sub_field('desktop_ad_url', $eq_ad->ID);
              $desktop_ad_details[$i]['ad_alttag'] = sanitize_title_with_dashes(get_the_title($eq_ad->ID));
              
              // Add sub-array to main ad array
              $desktop_ad['art_combo'] = $desktop_ad_details;
          $i++;

          endwhile;
      endif;

    

      $all_desktop_ads[$key] = $desktop_ad;

  endforeach;


shuffle($all_desktop_ads);


// Loop through the array of new, correctly formatted ads 
foreach($all_desktop_ads as $formatted_desktop_ad):

    // Count how many types of art there are  
    $number_of_art_options = count($formatted_desktop_ad['art_combo']) - 1;

    // Randomly select a piece of art  
    $x = rand(0, $number_of_art_options);?>


    <?php 


    $inline_ad = '<a '. $ad_alignment .' class="inline-callout" href="' . $formatted_desktop_ad['art_combo'][$x]['ad_url'] . '">';
    $inline_ad .= '<img onload="ga(\'send\', \'event\', \'Advertising\', \'Impression\', ' . $formatted_desktop_ad['ad_slug'] . 'desktop-art-inline' . $x . ');" src="'. $formatted_desktop_ad['art_combo'][$x]['ad_art'] . '"/>';
    $inline_ad .= '</a>';
    return $inline_ad;?>

<?php endforeach;?>



