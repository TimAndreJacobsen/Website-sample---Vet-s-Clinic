<?php
    get_header();
    page_banner(array(
      'title' => 'All Events',
      'subtitle' => 'See upcoming or previous events'
    )); ?>

<br><br>
<div class="container container--narrow page section">
  <?php
  while (have_posts()) {
      the_post(); 
      get_template_part('template-parts/content', get_post_type());
  }
  echo paginate_links(); ?>

  <hr class="section-break">
  <p>Want to browse our <a href="<?php echo esc_url(site_url('/past-events')); ?>">past events?</p>
</div>

<?php
  get_footer();
?>