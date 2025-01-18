<?php
get_header();

if ( have_posts() ) :
?>
<div class="container-1140">
    <?php
    $term = get_queried_object();
    $term_image_id = get_term_meta( $term->term_id, 'image', true );
    $term_image     = wp_get_attachment_image_src( $term_image_id, 'full' );
    $taxonomy_description = term_description( $term->term_id, 'topics' );
    if ($term->term_id == '10330'){
        $class_name = 'usefule_site_table';
    }

    function get_media_type($media_type_child='', $article__pdf__video_link=''){
        $media_type_url_child = '';
        $post_link = '';
        switch ($media_type_child) {
            case "PDF":
                $media_type_url_child = wp_get_attachment_url(17614);
                $post_link = $article__pdf__video_link;
                break;
            case "link":
                $media_type_url_child = wp_get_attachment_url(17615);
                $post_link = $article__pdf__video_link;
                break;
            case "Video":
                $media_type_url_child = wp_get_attachment_url(17616);
                $post_link = $article__pdf__video_link;
                break;
            case "Article":
                $media_type_url_child = wp_get_attachment_url(17618);
                $post_link = get_permalink(get_the_ID());
                break;
            default:
                $media_type_url_child = wp_get_attachment_url(17614);
                $post_link = $article__pdf__video_link;
                break;
        }

        $data = ['media_type'=>$media_type_url_child,'post_link'=>$post_link];
        return $data;
    }
    // echo "<pre>";
    // print_r($term);
    // die;

    // Get child categories
    $child_categories = get_terms( array(
        'taxonomy' => $term->taxonomy,
        'parent'   => $term->term_id,
    ) );
    ?>
    <div class="go-back-insights">
        <a href="<?=get_permalink('13889')?>"><i aria-hidden="true" class="fas fa-arrow-left"> </i> Go Back</a>
    </div>
    <h1 class="cat-title"><?= $term->name ?></h1>

    <div class="cat-fetured-content">
        <div class="cat-fetured-img">
            <img src="<?= $term_image[0] ?>" alt="" title="">
        </div>
        <div class="cat-fetured-text">
            <?php echo $taxonomy_description; ?>
        </div>
    </div>


            <?php

            $featured_posts = new WP_Query(array(
                'post_type'      => 'insights',
                'posts_per_page' => 4,
                'tax_query'      => array(
                    'relation' => 'AND', // Add this line for an additional condition
                    array(
                        'taxonomy' => 'featured-insights',
                        'field'    => 'slug',
                        'terms'    => 'featured',
                    ),
                    array(
                        'taxonomy' => 'topics', // Adjust the additional taxonomy name
                        'field'    => 'slug',
                        'terms'    => $term->slug,
                    ),
                ),
            ));
            
            if ($featured_posts->have_posts()) {
                ?>
                <div class="top-picks">
                    <h2>Editors Top Picks</h2>
                    <div class="top-picks-list">

                <?php
                while ($featured_posts->have_posts()) : $featured_posts->the_post();
                    $featured_author_name = get_post_meta(get_the_ID(), 'author_name', true);
                    $featured_publish_date = get_post_meta(get_the_ID(), 'publish_date', true);

                    $article__pdf__video_link = get_post_meta(get_the_ID(), 'article__pdf__video_link', true);
                    $media_type_child = get_post_meta(get_the_ID(), 'type', true);
                    $link = get_media_type($media_type_child,$article__pdf__video_link);
                    ?>

                    <a href="<?=$link['post_link']?>" class="top-picks-item" target="_blank">
                        <div class="top-picks-img">
                            <?php the_post_thumbnail(); // Display post thumbnail ?>
                        </div>
                        <h3><?php the_title(); ?></h3>
                        <?php
                            if($featured_author_name){
                                echo '<p>by '.$featured_author_name.'</p>';
                            }
                        ?>
                        <p><?= $featured_publish_date ?></p>
                     </a>
            
                <?php
                endwhile;
            
                // Reset post data
                wp_reset_postdata();
                echo "</div>";
                echo "</div>";
            } 

            ?>

    <?php
    // echo '<div class="custom-search">';
    //     echo do_shortcode( '[insight_search_form]');
    // echo '</div>';
    ?>
    <div class="search-sec">
        <div class="custom-search" style="max-width: 300px;">
            <form role="search" method="post" id="searchform_by_insight_name">
                <input type="text" value="" name="insight_search" id="insight_search" required=""
                       placeholder="Search Insights...">
                <input type="submit" id="searchsubmit_insight" value="Search">
            </form>
        </div>
        <div class="custom_clear">
			<a class="">
				<i aria-hidden="true" class="far fa-window-close"></i>
                <span class="elementor-button-text">Clear</span>
		   </a>
		</div>
    </div>
</div>

<div class="result_loader">
    <?php 
    $result_loader = wp_get_attachment_url(16783);
    echo '<img src="'.$result_loader.'" alt="">';
    ?>
</div>
<div class="search_category_insight">
</div>

<div class="child_category_insight">
<?php
    if (!empty($child_categories)) {
        foreach ($child_categories as $child_category) {
            $term_image_id_child = get_term_meta($child_category->term_id, 'image', true);
            $term_image_child = wp_get_attachment_image_src($term_image_id_child, 'full');
            ?>
            <div class="sub-cat-sec">
                <div class="container-1140">
                    <h2><?= $child_category->name ?></h2>
                    <table>
                        <?php
                        $args = array(
                            'post_type' => 'insights', // Adjust post type if needed
                            'posts_per_page' => '-1',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => $child_category->taxonomy,
                                    'field'    => 'id',
                                    'terms'    => $child_category->term_id,
                                ),
                            ),
                        );

                        $child_category_posts = new WP_Query($args);

                        if ($child_category_posts->have_posts()) {
                            while ($child_category_posts->have_posts()) {
                                $child_category_posts->the_post(); 
                                $article__pdf__video_link = get_post_meta(get_the_ID(), 'article__pdf__video_link', true);
                                $author_name = get_post_meta(get_the_ID(), 'author_name', true);
                                $publish_date = get_post_meta(get_the_ID(), 'publish_date', true);
                                $media_type_child = get_post_meta(get_the_ID(), 'type', true);
                                $link = get_media_type($media_type_child,$article__pdf__video_link); 
                                global $wpdb;
                                $insight_tags = wp_get_post_terms(get_the_ID(), 'insight_tags'); 
                                $investor_link = ''; 
                                foreach ($insight_tags as $insight) {
                                    $post_type = 'investors';
                                    $post_name = $insight->name;  
                                    $query = $wpdb->prepare(
                                        "SELECT ID
                                        FROM {$wpdb->prefix}posts
                                        WHERE post_type = %s
                                        AND post_title = %s
                                        LIMIT 1",
                                        $post_type,
                                        $post_name
                                    ); 
                                    $investor_id = $wpdb->get_var($query); 
                                    if ($investor_id) {
                                        $investor_link = get_permalink($investor_id);
                                        // Use $investor_link as needed
                                    }
                                }
                                ?>

                                <tr class="<?=$class_name?>">
                                    <td class="title"><a href="<?=$link['post_link']?>" target="_blank"><?= the_title() ?></a>
                                <?php
                                // echo "<pre>";
                                // print_r($investor_posts);
                                ?>
                                </td>
                                    <?php
                                    if ($term->term_id != '10329') {
                                        if($author_name){
                                            echo '<td class="auther"><span class="by_author">by</span> '.$author_name.'</td>';
                                        }
                                        else{
                                            echo '<td class="auther">-</td>';
                                        }
                                    } 

                                    if ($term->term_id != '10329' && $term->term_id != '10330' && $term->term_id != '10331') {?>
                                    <td class="date"><?=$publish_date?$publish_date:'-'?></td>
                                    <?php
                                    } 
                                    ?>
                                    <td class="media"><a href="<?=$link['post_link']?>" target="_blank"><img src="<?=$link['media_type'] ?>"
                                                         alt="" title="<?= the_title() ?>"></a></td>
                                </tr>
                                <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </table>
                </div>
            </div>

            <?php
        }
    }
    else{
            ?>
            <div class="sub-cat-sec">
                <div class="container-1140">
                    <h2><?= $term->name ?></h2>
                    <table>
                        <?php
                        $args = array(
                            'post_type' => 'insights', // Adjust post type if needed
                            'posts_per_page' => '-1',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => $term->taxonomy,
                                    'field'    => 'id',
                                    'terms'    => $term->term_id,
                                ),
                            ),
                        );

                        $parent_category_posts = new WP_Query($args);

                        if ($parent_category_posts->have_posts()) {
                            while ($parent_category_posts->have_posts()) {
                                $parent_category_posts->the_post();

                                $article__pdf__video_link = get_post_meta(get_the_ID(), 'article__pdf__video_link', true);
                                $author_name = get_post_meta(get_the_ID(), 'author_name', true);
                                $publish_date = get_post_meta(get_the_ID(), 'publish_date', true);
                                $media_type_child = get_post_meta(get_the_ID(), 'type', true);
                                $link = get_media_type($media_type_child,$article__pdf__video_link);

                                global $wpdb;
                                $insight_tags = wp_get_post_terms(get_the_ID(), 'insight_tags');

                                $investor_link = '';

                                foreach ($insight_tags as $insight) {
                                    $post_type = 'investors';
                                    $post_name = $insight->name;
                                
                                    $query = $wpdb->prepare(
                                        "SELECT ID
                                        FROM {$wpdb->prefix}posts
                                        WHERE post_type = %s
                                        AND post_title = %s
                                        LIMIT 1",
                                        $post_type,
                                        $post_name
                                    );
                                
                                    $investor_id = $wpdb->get_var($query);
                                
                                    if ($investor_id) {
                                        $investor_link = get_permalink($investor_id);
                                        // Use $investor_link as needed
                                    }
                                }
                                
                                ?>

                                <tr class="<?=$class_name?>">
                                    <td class="title"><a href="<?= $link['post_link']?>" target="_blank"><?= the_title() ?></a></td>
                                    <?php
                                    if ($term->term_id != '10329') {
                                        if($author_name){
                                            echo '<td class="auther"><span class="by_author">by</span> '.$author_name.'</td>';
                                        }
                                        else{
                                            echo '<td class="auther">-</td>';
                                        }
                                    }
                                    

                                    if ($term->term_id != '10329' && $term->term_id != '10330') {?>
                                        <td class="date"><?=$publish_date?$publish_date:'-'?></td>
                                    <?php
                                    }
                                    ?>
                                    <td class="media"><a href="<?=$link['post_link']?>" target="_blank"><img src="<?=$link['media_type'] ?>"
                                                         alt="" title="<?= the_title() ?>"></a></td>
                                </tr>
                                <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </table>
                </div>
            </div>

            <?php
    }
echo "</div>";
endif;

echo do_shortcode( "[hfe_template id='18022']");
get_footer();
?>