<?php
$post_id = get_the_ID();
$post_translated = TravelHelper::post_translated($post_id);
$thumbnail_id = get_post_thumbnail_id($post_translated);
$duration = get_post_meta( get_the_ID(), 'duration', true );
$info_price = STActivity::inst()->get_info_price();
$address = get_post_meta($post_translated, 'address', true);

$review_rate = floatval(STReview::get_avg_rate());
$count_review = get_comment_count($post_translated)['approved'];
$class_image = 'image-feature st-hover-grow';
$url = st_get_link_with_search(get_permalink($post_translated), array('start','date','adult_number','child_number'), $_GET);
?>
<div class="services-item grid item-elementor" itemscope itemtype="https://schema.org/Event">
    <div class="item service-border st-border-radius">
        <div class="featured-image">
            <div class="st-tag-feature-sale">
                <?php
                    $is_featured = get_post_meta($post_translated, 'is_featured', true);
                    if ($is_featured == 'on') { ?>
                        <div class="featured">
                            <?php
                                if(!empty(st()->get_option('st_text_featured', ''))){
                                    echo wp_kses_post(st()->get_option('st_text_featured', ''));
                                } else {?>
                                    <?php echo esc_html__('Featured', 'traveler') ?>
                                <?php }
                            ?>
                        </div>
                <?php } ?>
                <?php if(!empty( $info_price['discount'] ) and $info_price['discount']>0 and $info_price['price_new'] >0) { ?>
                    <?php echo STFeatured::get_sale($info_price['discount']); ?>
                <?php } ?>

            </div>
            <?php if (is_user_logged_in()) { ?>
                <?php $data = STUser_f::get_icon_wishlist(); ?>
                <div class="service-add-wishlist login <?php echo ($data['status']) ? 'added' : ''; ?>"
                    data-id="<?php echo get_the_ID(); ?>" data-type="<?php echo get_post_type(get_the_ID()); ?>"
                    title="<?php echo ($data['status']) ? __('Remove from wishlist', 'traveler') : __('Add to wishlist', 'traveler'); ?>">
                    <?php echo TravelHelper::getNewIconV2('wishlist');?>
                    <div class="lds-dual-ring"></div>
                </div>
            <?php } else { ?>
                <a href="#" class="login" data-bs-toggle="modal" data-bs-target="#st-login-form">
                    <div class="service-add-wishlist" title="<?php echo __('Add to wishlist', 'traveler'); ?>">
                        <?php echo TravelHelper::getNewIconV2('wishlist');?>
                        <div class="lds-dual-ring"></div>
                    </div>
                </a>
            <?php } ?>
            <a href="<?php echo esc_url($url); ?>">
                <img itemprop="image" src="<?php echo wp_get_attachment_image_url($thumbnail_id, array(900, 600)); ?>"
                     alt="<?php echo TravelHelper::get_alt_image(); ?>" class="<?php echo esc_attr($class_image); ?>"/>
            </a>
            <?php do_action('st_list_compare_button', get_the_ID(), get_post_type(get_the_ID())); ?>
            <?php echo st_get_avatar_in_list_service(get_the_ID(),70)?>
        </div>
        <div class="content-item">
            <?php if ($address) { ?>
                <div class="sub-title st-address d-flex align-items-center" itemprop="location" itemscope itemtype="https://schema.org/Place">
                    <span itemprop="name"> <i class="stt-icon-location1"></i> <?php echo esc_html($address); ?></span>
                </div>
            <?php } ?>
            <div class="event-date d-none" itemprop="startDate" content="<?php echo date("Y-m-d H:i:s");?>"><?php echo date("Y-m-d H:i:s");?></div>
            <h3 class="title" itemprop="name">
                <a href="<?php echo esc_url($url); ?>"
                   class="c-main"><?php echo get_the_title($post_translated) ?></a>
            </h3>
<!-- Begin People Viewing Notice -->            
<?php
$viewers_list = [1, 5, 3, 10, 2, 8, 20, 4, 11, 19, 6, 14, 25, 7, 16, 9];
$booked_list = [1, 2, 3, 4, 5];
$people_viewing = $viewers_list[array_rand($viewers_list)];
$slots_booked = $booked_list[array_rand($booked_list)];
$show_booked = (rand(0, 1) === 1);
$days_advance = get_post_meta(get_the_ID(), '_days_advance', true);
?>
<div class="people-viewing-notice" style="display:block !important;opacity:1 !important;background:#f5f5f5;padding:12px;border:1px solid #2e7d32;margin-top:8px;font-size:14px;color:#333333;z-index:9999;font-weight:bold;border-radius:8px;">
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.42 11.968V6.567h1.5v6.022l-3.028 3.028-1.06-1.06zm9.357 2.925-5.417 5.416-2.89-2.89 1.06-1.06 1.83 1.829 4.356-4.356z"></path><path d="M20.41 12a8.24 8.24 0 0 0-8.128-8.239v-1.5c5.327.06 9.627 4.397 9.627 9.739q0 .135-.003.268h-1.501q.004-.135.004-.268m-8.273 8.239a8.24 8.24 0 0 1-5.46-14.38l-.978-1.137A9.72 9.72 0 0 0 2.43 12c0 5.367 4.343 9.72 9.706 9.739z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.42 5.733H2.09v-1.5h4.83v4.83h-1.5z"></path></svg> <span style="color:#2e7d32;"><?php echo esc_html($people_viewing); ?></span> travelers are viewing this tour right now &bull; spaces are filling quickly</strong><br>
<?php if ($show_booked) { ?>
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.23 6.645v10.71h17.54V6.644zm-1-1.5a.5.5 0 0 0-.5.5v12.71a.5.5 0 0 0 .5.5h19.54a.5.5 0 0 0 .5-.5V5.644a.5.5 0 0 0-.5-.5z"></path><path d="M2.605 8.554h18.933V12H2.605z"></path></svg> <span style="color:#2e7d32;"><?php echo esc_html($slots_booked); ?></span> just booked &bull; Book now!</strong><br>
<?php } ?>
<?php if (!empty($days_advance)) { ?>
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.007 2.75a.75.75 0 0 1 .748.752l-.002.748h2.512V3.5a.75.75 0 0 1 1.5 0v.75h2.472l-.002-.748a.75.75 0 1 1 1.5-.004l.002.752h4.018v17H3.245v-17h4.008l.002-.752a.75.75 0 0 1 .752-.748m-.758 3H4.745v14h14.51v-14h-2.513l.006 1.747-1.5.005-.006-1.752h-2.477V7.5h-1.5V5.75H8.749l-.004 1.752-1.5-.004zm-.004 6h5.5v5.5h-5.5zm1.5 1.5v2.5h2.5v-2.5z"></path></svg> Book ahead &bull; This is booked <span style="color:#2e7d32;"><?php echo esc_html($days_advance); ?></span> days in advance on average</strong>
<?php } ?>
</div><br>
<!-- End People Viewing Notice -->
 
            <?php
            // --- REVIEW STARS LOGIC (5 stars, half, empty) ---
            $fullStars = floor($review_rate);
            $halfStar = ($review_rate - $fullStars) >= 0.5 ? 1 : 0;
            $emptyStars = 5 - $fullStars - $halfStar;
            ?>
            <div class="st-review">
                <?php for ($i = 0; $i < $fullStars; $i++): ?>
                    <i class="stt-icon-star1" style="color:gold;"></i>
                <?php endfor; ?>
                <?php if ($halfStar): ?>
                    <i class="stt-icon-star1" style="color:gold; opacity:0.5;"></i>
                <?php endif; ?>
                <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                    <i class="stt-icon-star1" style="color:#E0E0E0;"></i>
                <?php endfor; ?>
                <span class="rating" style="margin-left:4px;"><?php echo esc_html($review_rate); ?></span>
                <span class="count">
                    (<?php echo esc_html($count_review); ?>
                    <?php echo ($count_review == 1) ? esc_html__('Review', 'traveler') : esc_html__('Reviews', 'traveler'); ?>)
                </span>
            </div>
            <div class="event-date d-none" itemprop="startDate" content="<?php echo date("Y-m-d H:i:s");?>"><?php echo date("Y-m-d H:i:s");?></div>
            <div class="section-footer">

                <div class="price-wrapper price-wrapper-tour d-flex align-items-end justify-content-between">
                    <span class="price-tour">
                        <span class="price d-flex justify-content-around flex-column"><?php echo STActivity::get_price_html(get_the_ID(),false, '',  'sale-top', false); ?></span>
                    </span>
                    <?php
                        if(!empty($duration)){ ?>
                            <span class="unit"><?php echo TravelHelper::getNewIcon('time-clock-circle-1', '#5E6D77', '16px', '16px'); ?><?php echo esc_html($duration); ?></span>
                        <?php }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>