<?php
/**
 * Mega Menu Template
 * 
 * @package PalmSHIELD_Mega_Menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Helper function to check if a URL is valid (not empty and not just anchor)
function psmm_is_valid_url($url) {
    $url = trim($url);
    if (empty($url)) {
        return false;
    }
    // Reject anchor-only URLs
    if ($url === '#' || $url === '#!' || preg_match('/^#[\w-]*$/', $url)) {
        return false;
    }
    return true;
}

// Variables available: $settings, $logo_url, $logo_link
?>

<nav class="psmm-nav-container" role="navigation" aria-label="<?php esc_attr_e('Main Navigation', 'palmshield-mega-menu'); ?>">
    <div class="psmm-nav-bar">
        <!-- Logo -->
        <div class="psmm-logo">
            <a href="<?php echo esc_url($logo_link); ?>">
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            </a>
        </div>

        <!-- Mobile Toggle -->
        <button class="psmm-mobile-toggle" aria-label="<?php esc_attr_e('Toggle Menu', 'palmshield-mega-menu'); ?>" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Nav Items -->
        <ul class="psmm-nav-items">
            <li class="psmm-nav-item" data-menu="screens" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <?php esc_html_e('Screens', 'palmshield-mega-menu'); ?>
                <span class="psmm-arrow">▼</span>
            </li>
            <li class="psmm-nav-item" data-menu="enclosures" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <?php esc_html_e('Enclosures', 'palmshield-mega-menu'); ?>
                <span class="psmm-arrow">▼</span>
            </li>
            <li class="psmm-nav-item" data-menu="amenities" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <?php esc_html_e('Site Amenities', 'palmshield-mega-menu'); ?>
                <span class="psmm-arrow">▼</span>
            </li>
            <li class="psmm-nav-item" data-menu="gates" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <?php esc_html_e('Gates', 'palmshield-mega-menu'); ?>
                <span class="psmm-arrow">▼</span>
            </li>
            <li class="psmm-nav-item" data-menu="resources" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <?php esc_html_e('Resources', 'palmshield-mega-menu'); ?>
                <span class="psmm-arrow">▼</span>
            </li>
        </ul>

        <!-- CTA Menu Items -->
        <?php if (!empty($settings['cta_menu'])) : ?>
        <div class="psmm-cta-menu-items">
            <?php if (!empty($settings['cta_menu']['careers']) && !empty($settings['cta_menu']['careers']['title']) && psmm_is_valid_url($settings['cta_menu']['careers']['url'])) : ?>
                <a href="<?php echo esc_url($settings['cta_menu']['careers']['url']); ?>" class="psmm-cta-button">
                    <?php echo esc_html($settings['cta_menu']['careers']['title']); ?>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($settings['cta_menu']['dealer_network']) && !empty($settings['cta_menu']['dealer_network']['title']) && psmm_is_valid_url($settings['cta_menu']['dealer_network']['url'])) : ?>
                <a href="<?php echo esc_url($settings['cta_menu']['dealer_network']['url']); ?>" class="psmm-cta-button">
                    <?php echo esc_html($settings['cta_menu']['dealer_network']['title']); ?>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($settings['cta_menu']['search']) && !empty($settings['cta_menu']['search']['title'])) : ?>
                <?php 
                $search_url = !empty($settings['cta_menu']['search']['url']) && psmm_is_valid_url($settings['cta_menu']['search']['url']) 
                    ? esc_url($settings['cta_menu']['search']['url']) 
                    : home_url('/?s=');
                ?>
                <div class="psmm-search-container">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="psmm-search-form">
                        <input type="search" 
                               name="s" 
                               class="psmm-search-input" 
                               placeholder="<?php esc_attr_e('Search...', 'palmshield-mega-menu'); ?>" 
                               value="<?php echo get_search_query(); ?>"
                               aria-label="<?php esc_attr_e('Search', 'palmshield-mega-menu'); ?>">
                        <button type="submit" class="psmm-search-submit" aria-label="<?php esc_attr_e('Submit Search', 'palmshield-mega-menu'); ?>">
                            <span class="psmm-search-icon">🔍</span>
                            <span class="psmm-search-text"><?php echo esc_html($settings['cta_menu']['search']['title']); ?></span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- SCREENS Mega Menu -->
        <div class="psmm-mega-menu" id="psmm-menu-screens" role="menu" aria-label="<?php esc_attr_e('Screens Menu', 'palmshield-mega-menu'); ?>">
            <div class="psmm-mega-menu-inner psmm-mega-screens">
                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('By Application', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['screens']['by_application'])) : ?>
                            <?php foreach ($settings['screens']['by_application'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('By Performance', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['screens']['by_performance'])) : ?>
                            <?php foreach ($settings['screens']['by_performance'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('By Style', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['screens']['by_style'])) : ?>
                            <?php foreach ($settings['screens']['by_style'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <h3 style="margin-top: 22px;"><?php esc_html_e('Quick Links', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['screens']['quick_links']['view_all']) && psmm_is_valid_url($settings['screens']['quick_links']['view_all']['url'])) : ?>
                            <li><a href="<?php echo esc_url($settings['screens']['quick_links']['view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['screens']['quick_links']['view_all']['title']); ?> →</a></li>
                        <?php endif; ?>
                        <?php if (!empty($settings['screens']['quick_links']['color_options']) && psmm_is_valid_url($settings['screens']['quick_links']['color_options']['url'])) : ?>
                            <li><a href="<?php echo esc_url($settings['screens']['quick_links']['color_options']['url']); ?>"><?php echo esc_html($settings['screens']['quick_links']['color_options']['title']); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <?php if (!empty($settings['screens']['featured']) && psmm_is_valid_url($settings['screens']['featured']['button_url'])) : ?>
                        <div class="psmm-mega-featured">
                            <h3><?php echo esc_html($settings['screens']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($settings['screens']['featured']['description']); ?></p>
                            <a href="<?php echo esc_url($settings['screens']['featured']['button_url']); ?>" class="psmm-cta-btn"><?php echo esc_html($settings['screens']['featured']['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ENCLOSURES Mega Menu -->
        <div class="psmm-mega-menu" id="psmm-menu-enclosures" role="menu" aria-label="<?php esc_attr_e('Enclosures Menu', 'palmshield-mega-menu'); ?>">
            <div class="psmm-mega-menu-inner psmm-mega-enclosures">
                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Enclosure Types', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['enclosures']['enclosure_types'])) : ?>
                            <?php foreach ($settings['enclosures']['enclosure_types'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['enclosures']['enclosure_types_view_all']) && psmm_is_valid_url($settings['enclosures']['enclosure_types_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['enclosures']['enclosure_types_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['enclosures']['enclosure_types_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <?php if (!empty($settings['enclosures']['featured']) && psmm_is_valid_url($settings['enclosures']['featured']['button_url'])) : ?>
                        <div class="psmm-mega-featured">
                            <h3><?php echo esc_html($settings['enclosures']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($settings['enclosures']['featured']['description']); ?></p>
                            <a href="<?php echo esc_url($settings['enclosures']['featured']['button_url']); ?>" class="psmm-cta-btn"><?php echo esc_html($settings['enclosures']['featured']['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SITE AMENITIES Mega Menu -->
        <div class="psmm-mega-menu" id="psmm-menu-amenities" role="menu" aria-label="<?php esc_attr_e('Site Amenities Menu', 'palmshield-mega-menu'); ?>">
            <div class="psmm-mega-menu-inner psmm-mega-amenities">
                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Railings', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['amenities']['railings'])) : ?>
                            <?php foreach ($settings['amenities']['railings'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['amenities']['railings_view_all']) && psmm_is_valid_url($settings['amenities']['railings_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['amenities']['railings_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['amenities']['railings_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Bollards', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['amenities']['bollards'])) : ?>
                            <?php foreach ($settings['amenities']['bollards'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['amenities']['bollards_view_all']) && psmm_is_valid_url($settings['amenities']['bollards_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['amenities']['bollards_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['amenities']['bollards_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Shades', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['amenities']['shades'])) : ?>
                            <?php foreach ($settings['amenities']['shades'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['amenities']['shades_view_all']) && psmm_is_valid_url($settings['amenities']['shades_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['amenities']['shades_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['amenities']['shades_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <?php if (!empty($settings['amenities']['featured']) && psmm_is_valid_url($settings['amenities']['featured']['button_url'])) : ?>
                        <div class="psmm-mega-featured">
                            <h3><?php echo esc_html($settings['amenities']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($settings['amenities']['featured']['description']); ?></p>
                            <a href="<?php echo esc_url($settings['amenities']['featured']['button_url']); ?>" class="psmm-cta-btn"><?php echo esc_html($settings['amenities']['featured']['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- GATES Mega Menu -->
        <div class="psmm-mega-menu" id="psmm-menu-gates" role="menu" aria-label="<?php esc_attr_e('Gates Menu', 'palmshield-mega-menu'); ?>">
            <div class="psmm-mega-menu-inner psmm-mega-gates">
                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Access Control+', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['gates']['access_control'])) : ?>
                            <?php foreach ($settings['gates']['access_control'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['gates']['access_control_view_all']) && psmm_is_valid_url($settings['gates']['access_control_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['gates']['access_control_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['gates']['access_control_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Architectural Gates', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['gates']['architectural'])) : ?>
                            <?php foreach ($settings['gates']['architectural'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['gates']['architectural_view_all']) && psmm_is_valid_url($settings['gates']['architectural_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['gates']['architectural_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['gates']['architectural_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Vulcan Pedestrian Gates', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['gates']['pedestrian'])) : ?>
                            <?php foreach ($settings['gates']['pedestrian'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($settings['gates']['pedestrian_view_all']) && psmm_is_valid_url($settings['gates']['pedestrian_view_all']['url'])) : ?>
                        <a href="<?php echo esc_url($settings['gates']['pedestrian_view_all']['url']); ?>" class="psmm-link-arrow"><?php echo esc_html($settings['gates']['pedestrian_view_all']['title']); ?> →</a>
                    <?php endif; ?>
                </div>

                <div class="psmm-mega-column">
                    <h3 class="psmm-navy-heading"><?php esc_html_e('Hardware', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['gates']['hardware'])) : ?>
                            <?php foreach ($settings['gates']['hardware'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if (!empty($settings['gates']['featured']) && psmm_is_valid_url($settings['gates']['featured']['button_url'])) : ?>
                        <div class="psmm-mega-featured psmm-featured-small">
                            <h3><?php echo esc_html($settings['gates']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($settings['gates']['featured']['description']); ?></p>
                            <a href="<?php echo esc_url($settings['gates']['featured']['button_url']); ?>" class="psmm-cta-btn"><?php echo esc_html($settings['gates']['featured']['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RESOURCES Mega Menu -->
        <div class="psmm-mega-menu" id="psmm-menu-resources" role="menu" aria-label="<?php esc_attr_e('Resources Menu', 'palmshield-mega-menu'); ?>">
            <div class="psmm-mega-menu-inner psmm-mega-resources">
                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Technical Resources', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['resources']['technical'])) : ?>
                            <?php foreach ($settings['resources']['technical'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Sales Resources', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['resources']['sales'])) : ?>
                            <?php foreach ($settings['resources']['sales'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <h3><?php esc_html_e('Support', 'palmshield-mega-menu'); ?></h3>
                    <ul>
                        <?php if (!empty($settings['resources']['support'])) : ?>
                            <?php foreach ($settings['resources']['support'] as $item) : ?>
                                <?php if (psmm_is_valid_url($item['url'])) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                                        <?php if (!empty($item['description'])) : ?>
                                            <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="psmm-mega-column">
                    <?php if (!empty($settings['resources']['featured']) && psmm_is_valid_url($settings['resources']['featured']['button_url'])) : ?>
                        <div class="psmm-mega-featured">
                            <h3><?php echo esc_html($settings['resources']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($settings['resources']['featured']['description']); ?></p>
                            <a href="<?php echo esc_url($settings['resources']['featured']['button_url']); ?>" class="psmm-cta-btn"><?php echo esc_html($settings['resources']['featured']['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</nav>
