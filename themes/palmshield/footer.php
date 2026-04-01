</div> <!-- end main content -->
<footer class="default-footer">

  <section class="row container-fluid m-auto">
    <div class="row row-col-5 py-2 my-4 border-top" style="margin: 0 auto;">
      <div class="col-lg  col-md-6 col-sm-6">
        <?php dynamic_sidebar('footer_area_one'); ?>
      </div>
      <div class="col-lg col-md-6 col-sm-6">
        <?php dynamic_sidebar('footer_area_two'); ?>
      </div>
      <div class="col-lg col-md-6 col-sm-6">
        <?php dynamic_sidebar('footer_area_three'); ?>
      </div>
      <div class="col-lg col-md-6 col-sm-6">
        <?php dynamic_sidebar('footer_area_four'); ?>
      </div>
      <div class="col-lg col-md-6 col-sm-6 ">
        <?php dynamic_sidebar('footer_area_five'); ?>
      </div>
  </section>

  <div class="row container m-auto">
        <div class="text-center copyright">
          Copyright ©<?php echo date('Y'); ?> PalmSHIELD and America's Gate Company. All Rights Reserved.
          Details and information are subject to change without notice.
        </div>
      </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>