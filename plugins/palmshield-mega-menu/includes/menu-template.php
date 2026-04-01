<?php
/**
 * Menu Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$menu_data = psmm_get_menu_data();
$logo_url = get_option('psmm_logo_url', home_url('/'));
$logo_image = get_option('psmm_logo_image', '');
?>

<nav class="psmm-nav-container" role="navigation" aria-label="Main Navigation">
    <div class="psmm-nav-bar">
        <!-- Logo -->
        <a href="<?php echo esc_url($logo_url); ?>" class="psmm-logo">
            <?php if ($logo_image) : ?>
                <img src="<?php echo esc_url($logo_image); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <?php else : ?>
                <span class="psmm-logo-text">PalmSHIELD</span>
            <?php endif; ?>
        </a>

        <!-- Navigation Items -->
        <div class="psmm-nav-items">
            <?php foreach ($menu_data as $menu_key => $menu) : ?>
                <div class="psmm-nav-item<?php echo $menu['has_dropdown'] ? ' has-dropdown' : ''; ?>" 
                     data-menu="<?php echo esc_attr($menu_key); ?>">
                    <span class="psmm-nav-label"><?php echo esc_html($menu['label']); ?></span>
                    <?php if ($menu['has_dropdown']) : ?>
                        <span class="psmm-arrow">▼</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="psmm-mobile-toggle" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- SCREENS Mega Menu -->
    <div class="psmm-mega-menu" id="psmm-menu-screens">
        <div class="psmm-mega-menu-inner psmm-mega-screens">
            <?php if (isset($menu_data['screens']['columns'])) : ?>
                <?php foreach ($menu_data['screens']['columns'] as $column) : ?>
                    <div class="psmm-mega-column">
                        <h3><?php echo esc_html($column['title']); ?></h3>
                        <ul>
                            <?php foreach ($column['items'] as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($column['quick_links'])) : ?>
                            <h3 class="psmm-quick-links-title">Quick Links</h3>
                            <ul>
                                <?php foreach ($column['quick_links'] as $link) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($link['url']); ?>" class="<?php echo !empty($link['arrow']) ? 'psmm-link-arrow' : ''; ?>">
                                            <?php echo esc_html($link['label']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if (!empty($column['view_all'])) : ?>
                            <a href="<?php echo esc_url($column['view_all']['url']); ?>" class="psmm-link-arrow">
                                <?php echo esc_html($column['view_all']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($menu_data['screens']['featured'])) : ?>
                <div class="psmm-mega-column">
                    <div class="psmm-mega-featured">
                        <h3><?php echo esc_html($menu_data['screens']['featured']['title']); ?></h3>
                        <p><?php echo esc_html($menu_data['screens']['featured']['text']); ?></p>
                        <a href="<?php echo esc_url($menu_data['screens']['featured']['cta_url']); ?>" class="psmm-cta-btn">
                            <?php echo esc_html($menu_data['screens']['featured']['cta_label']); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ENCLOSURES Mega Menu -->
    <div class="psmm-mega-menu" id="psmm-menu-enclosures">
        <div class="psmm-mega-menu-inner psmm-mega-enclosures">
            <?php if (isset($menu_data['enclosures']['columns'])) : ?>
                <?php foreach ($menu_data['enclosures']['columns'] as $column) : ?>
                    <div class="psmm-mega-column">
                        <h3><?php echo esc_html($column['title']); ?></h3>
                        <ul>
                            <?php foreach ($column['items'] as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($column['view_all'])) : ?>
                            <a href="<?php echo esc_url($column['view_all']['url']); ?>" class="psmm-link-arrow">
                                <?php echo esc_html($column['view_all']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($menu_data['enclosures']['featured'])) : ?>
                <div class="psmm-mega-column">
                    <div class="psmm-mega-featured">
                        <h3><?php echo esc_html($menu_data['enclosures']['featured']['title']); ?></h3>
                        <p><?php echo esc_html($menu_data['enclosures']['featured']['text']); ?></p>
                        <a href="<?php echo esc_url($menu_data['enclosures']['featured']['cta_url']); ?>" class="psmm-cta-btn">
                            <?php echo esc_html($menu_data['enclosures']['featured']['cta_label']); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SITE AMENITIES Mega Menu -->
    <div class="psmm-mega-menu" id="psmm-menu-amenities">
        <div class="psmm-mega-menu-inner psmm-mega-amenities">
            <?php if (isset($menu_data['amenities']['columns'])) : ?>
                <?php foreach ($menu_data['amenities']['columns'] as $column) : ?>
                    <div class="psmm-mega-column">
                        <h3><?php echo esc_html($column['title']); ?></h3>
                        <ul>
                            <?php foreach ($column['items'] as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($column['view_all'])) : ?>
                            <a href="<?php echo esc_url($column['view_all']['url']); ?>" class="psmm-link-arrow">
                                <?php echo esc_html($column['view_all']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($menu_data['amenities']['featured'])) : ?>
                <div class="psmm-mega-column">
                    <div class="psmm-mega-featured">
                        <h3><?php echo esc_html($menu_data['amenities']['featured']['title']); ?></h3>
                        <p><?php echo esc_html($menu_data['amenities']['featured']['text']); ?></p>
                        <a href="<?php echo esc_url($menu_data['amenities']['featured']['cta_url']); ?>" class="psmm-cta-btn">
                            <?php echo esc_html($menu_data['amenities']['featured']['cta_label']); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- GATES Mega Menu -->
    <div class="psmm-mega-menu" id="psmm-menu-gates">
        <div class="psmm-mega-menu-inner psmm-mega-gates">
            <?php if (isset($menu_data['gates']['columns'])) : ?>
                <?php foreach ($menu_data['gates']['columns'] as $column) : ?>
                    <div class="psmm-mega-column">
                        <h3><?php echo esc_html($column['title']); ?></h3>
                        <ul>
                            <?php foreach ($column['items'] as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (!empty($column['view_all'])) : ?>
                            <a href="<?php echo esc_url($column['view_all']['url']); ?>" class="psmm-link-arrow">
                                <?php echo esc_html($column['view_all']['label']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($menu_data['gates']['hardware'])) : ?>
                <div class="psmm-mega-column">
                    <h3 class="psmm-hardware-title"><?php echo esc_html($menu_data['gates']['hardware']['title']); ?></h3>
                    <ul class="psmm-hardware-list">
                        <?php foreach ($menu_data['gates']['hardware']['items'] as $item) : ?>
                            <li><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (!empty($menu_data['gates']['hardware']['featured'])) : ?>
                        <div class="psmm-mega-featured psmm-compact">
                            <h3><?php echo esc_html($menu_data['gates']['hardware']['featured']['title']); ?></h3>
                            <p><?php echo esc_html($menu_data['gates']['hardware']['featured']['text']); ?></p>
                            <a href="<?php echo esc_url($menu_data['gates']['hardware']['featured']['cta_url']); ?>" class="psmm-cta-btn">
                                <?php echo esc_html($menu_data['gates']['hardware']['featured']['cta_label']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RESOURCES Mega Menu -->
    <div class="psmm-mega-menu" id="psmm-menu-resources">
        <div class="psmm-mega-menu-inner psmm-mega-resources">
            <?php if (isset($menu_data['resources']['columns'])) : ?>
                <?php foreach ($menu_data['resources']['columns'] as $column) : ?>
                    <div class="psmm-mega-column">
                        <h3><?php echo esc_html($column['title']); ?></h3>
                        <ul>
                            <?php foreach ($column['items'] as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                                    <?php if (!empty($item['description'])) : ?>
                                        <p class="psmm-description"><?php echo esc_html($item['description']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($menu_data['resources']['featured'])) : ?>
                <div class="psmm-mega-column">
                    <div class="psmm-mega-featured">
                        <h3><?php echo esc_html($menu_data['resources']['featured']['title']); ?></h3>
                        <p><?php echo esc_html($menu_data['resources']['featured']['text']); ?></p>
                        <a href="<?php echo esc_url($menu_data['resources']['featured']['cta_url']); ?>" class="psmm-cta-btn">
                            <?php echo esc_html($menu_data['resources']['featured']['cta_label']); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</nav>
