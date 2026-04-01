<?php
/**
 * Template Name: Hardware Template
 * Template Post Type: hardware
 */

get_header(); ?>

<div class="container-fluid col-xl-11 col-lg-12 col-md-12 col-sm-12 col-12 main-hardware-container">
    <div class="row">
        <div class="col">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                        
                        <!-- CAD Drawings Section -->
                        <div class="cad-drawings-section">
                            <h2>Hardware Resource Files</h2>
                            <?php
                            // Get all file fields
                            $spec_doc = get_field('specifications_document');
                            $pdf_drawing = get_field('pdf_drawing');
                            $cad_file = get_field('cad_file_dwg');
                            $bim_file = get_field('bim_file');

                            // Check if any files exist
                            if ($spec_doc || $pdf_drawing || $cad_file || $bim_file): ?>
                                <div class="drawing-buttons">
                                    <?php
                                    // Display buttons for each file if they exist
                                    if ($pdf_drawing): ?>
                                        <a href="<?php echo esc_url($pdf_drawing); ?>" class="btn btn-primary hardware-btn" download>
                                            Download PDF Drawing
                                        </a>
                                    <?php endif;

                                    if ($cad_file): ?>
                                        <a href="<?php echo esc_url($cad_file); ?>" class="btn btn-primary hardware-btn" download>
                                            Download CAD File
                                        </a>
                                    <?php endif;

                                    if ($bim_file): ?>
                                        <a href="<?php echo esc_url($bim_file); ?>" class="btn btn-primary hardware-btn" download>
                                            Download BIM File
                                        </a>
                                    <?php endif; ?>
									<?php if ($spec_doc): ?>
                                        <a href="<?php echo esc_url($spec_doc); ?>" class="btn btn-primary hardware-btn" download>
                                            Download Specifications
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p>No hardware resource files found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
<style>
    .main-hardware-container {
        padding-bottom: 1.5rem;
    }
.hardware-btn {
    margin-right: 10px;
    border-radius: 5px;
    border: 2px solid #1E335F;
    background-color: #e02826;
    padding: 5px 10px;
    color: #fff;
    transition: all 0.3s ease;
}
.hardware-btn:hover {
    background-color: #1E335F;
    border: 2px solid #50b8e9;
}
</style>
