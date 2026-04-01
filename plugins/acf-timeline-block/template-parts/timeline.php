<?php
$items = get_field('timeline_items');
if( $items ):
?>
<div class="timeline-block container">
    <ul class="timeline">
        <?php $i = 0; foreach( $items as $item ): ?>
            <li class="timeline-item <?php echo ($i % 2 == 0) ? 'timeline-item-right' : 'timeline-item-left'; ?>">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-content-inner">
                    <?php if ($item['year']): ?>
                        <h3 class="timeline-year"><?php echo esc_html($item['year']); ?></h3>
                    <?php endif; ?>
                    <?php if ($item['title']): ?>
                        <h4 class="timeline-title"><?php echo esc_html($item['title']); ?></h4>
                    <?php endif; ?>
                    <?php if ($item['description']): ?>
                        <p class="timeline-description"><?php echo wp_kses_post($item['description']); ?></p>
                    <?php endif; ?>
                    </div>
                    <?php if ($item['timeline_image']): ?>
                        <div class="timeline-image">
                            <img src="<?php echo esc_url($item['timeline_image']); ?>" alt="Timeline Image" />
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php $i++; endforeach; ?>
    </ul>
</div>
<?php endif; ?>
