<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
  <aside id="primary-sidebar" class="primary-sidebar col-lg-2 widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar' ); ?>
  </aside>
<?php endif; ?>