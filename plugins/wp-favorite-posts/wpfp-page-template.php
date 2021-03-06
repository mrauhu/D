<?php
    $user_id = get_current_user_id();

    $wpfp_before = "";
    echo "<div class='wpfp-span public-page-statistic-box'>";
    if (!empty($user)) {
        if (wpfp_is_user_favlist_public($user)) {
            $wpfp_before = "$user's Favorite Posts.";
        } else {
            $wpfp_before = "$user's list is not public.";
        }
    }

    if ($wpfp_before):
        echo '<div class="wpfp-page-before">'.$wpfp_before.'</div>';
    endif;

    if ($favorite_post_ids) {
        $post_per_page = wpfp_get_option("post_per_page");
        $page = intval(get_query_var('paged'));

        $qry = array(
            'post__in' => $favorite_post_ids,
            'posts_per_page'=> $post_per_page,
            'orderby' => 'post__in',
            'paged' => $page,
        );
        // custom post type support can easily be added with a line of code like below.
        // $qry['post_type'] = array('post','page');
        query_posts($qry);
        // Подготовка данных для проверки пройден ли курс полностью, для того чтобы убрать его из страницы "Мои массивы"
        // для того чтобы убрать его из страницы "Мои массивы"
        echo "<ul>";
        while ( have_posts() ) : the_post();
            $author_id = get_the_author_meta('ID');
            if($author_id === $user_id) {
                $add_string = '<small class="is_author"> автор </small>';
            }

            $passing_date = $GLOBALS['dPost']->get_passing_info_by_post($author_id, get_the_ID());
            $passing_string = "<span class='passing_date'>" . $passing_date['date_string'] . "</span>";
            $on_knowledge = $passing_date['undone_title']
                ?  '<span class="on-knowldedge"> На этапе: ' . $passing_date['undone_title'] . '</span>'
                : '';

            $li  = "<li>";
                $li .= "<a href='". get_permalink(). get_first_unchecked_lesson(get_the_ID()) ."'";
                $li .= "title='". get_the_title() ."' >";
                    $li .= get_the_title();
                    //Is author page
                    $li .= $add_string;
                $li .= "</a>";
                //Showing start-end date
                $li .= $passing_string;
                //Progress bar
                $li .= diductio_add_progress(get_the_ID(), $user_id, false);
                //Show on what knowledge user is now
                $li .= $on_knowledge;
            $li .="</li>";

            echo $li;
//            echo "<li><a href='".get_permalink(). get_first_unchecked_lesson(get_the_ID()) ."' title='". get_the_title() ."'>" . get_the_title() . $add_string ."</a> ";
//                wpfp_remove_favorite_link(get_the_ID());
//            echo "</li>";
        endwhile;
        echo "</ul>";

        echo '<div class="navigation">';
            if(function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
            <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
            <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
            <?php }
        echo '</div>';

        wp_reset_query();
    } else {
        $wpfp_options = wpfp_get_options();
        echo "<ul><li>";
        echo $wpfp_options['favorites_empty'];
        echo "</li></ul>";
    }

    echo '<p>'.wpfp_clear_list_link().'</p>';
    echo "</div>";
    wpfp_cookie_warning();
