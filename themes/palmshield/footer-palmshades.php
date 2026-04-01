</div> <!-- end main content -->
<footer class="palmshades-footer">

  <section class="row container m-auto">
    <div class="row row-col-4 py-4 my-4 border-top">
      <div class="col-md-4">
        <?php dynamic_sidebar('palmshades_footer_area_one'); ?>
      </div>
      <div class="col">
        <?php dynamic_sidebar('footer_area_two'); ?>
      </div>
      <div class="col">
        <?php dynamic_sidebar('footer_area_three'); ?>
      </div>
      <div class="col">
        <?php dynamic_sidebar('footer_area_four'); ?>
      </div>
  </section>

  <div class="row container m-auto">
        <div class="text-center copyright">
          Copyright ©<?php echo date('Y'); ?> The American Fence Company and PalmSHIELD. All Rights Reserved.
          Details and information are subject to change without notice.
        </div>
      </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>