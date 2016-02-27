<?php
/**
 * Filename: index-pdf.php
 * Project: WordPress PDF Templates
 * Copyright: (c) 2014-2016 Antti Kuosmanen
 * License: GPLv3
 *
 * Copy this file to your theme directory to start customising the PDF template.
*/
?>
<!DOCTYPE html>

<html>
<head>
  <title><?php wp_title(); ?></title>
  <?php wp_head(); ?>

  <style>.post-header h1 {margin-bottom: 0;}</style>

</head>

<body>
  <div class="container">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post-header">
      <h1><?php the_title(); ?></h1>
      <small><?php the_permalink(); ?></small>
    </div>

    <div class="post-content">
      <?php the_content(); ?>
    </div>

  <?php endwhile; endif; ?>
  </div>
  <?php wp_footer(); ?>
</body>
</html>
