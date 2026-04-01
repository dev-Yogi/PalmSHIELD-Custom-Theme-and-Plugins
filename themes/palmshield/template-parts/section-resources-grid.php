<section id="resources">

<?php
$sub_text = get_query_var('sub_text');
$taxonomy = 'resource_type';

$terms = get_terms([
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
]);

// Start main container
?>
<div class="container-fluid col-xl-11 col-lg-12" >
	<?php if($sub_text) : ?>
		<div class="row">
			<div class="col-12">
				<p class="resources-subtitle"><?php echo $sub_text; ?></p>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="row">
		<?php 
		$term_count = 0;
		foreach($terms as $term){
			// Get all child terms of this term
			$child_terms = get_terms([
				'taxonomy' => $taxonomy,
				'parent' => $term->term_id,
				'hide_empty' => false
			]);
			
			// Get child term IDs
			$child_term_ids = array();
			foreach($child_terms as $child_term) {
				$child_term_ids[] = $child_term->term_id;
			}
			
			$args = array(
				'post_type'      => 'palmshield_resources',
				'posts_per_page' => -1,
				'orderby'		 => 'title',
				'order'			 => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => $term->term_id,
						'include_children' => false // Don't include child terms
					 )
				  )
			 );

			$products = new WP_Query( $args );

			if ( $products->have_posts() ) : 
				// Create a new row for every 2 terms (2 columns per row)
				if ($term_count % 2 == 0) {
					if ($term_count > 0) {
						echo '</div>'; // Close previous row
					}
					echo '<div class="row mb-5">';
				}
				?>
				
				<div class="col-lg-6 col-md-6 col-sm-12 mb-4">
					<div class="resource-category">
						<div class="offset" id="<?php echo str_replace(" ", "-", strtolower($term->name)) ; ?>"></div>
						<h3 class="resource-category-title"><?php echo $term->name ?></h3>
						
						<div class="resource-items">
							<?php 
							while ( $products->have_posts() ) : $products->the_post(); ?>
								<div id="resources-<?php the_ID(); ?>" class="resource-item">
									<div class="resource-content">
										<?php
										if(get_field('resource_document')){
											$doc_path = get_field('resource_document');
											$type_parts = explode('.', $doc_path);
											$doc_type = array_pop($type_parts);
											$file_ext = 'FILE';
											if($doc_type == 'docx'){
												$file_ext = strtoupper($doc_type);
											}
											elseif($doc_type == 'pdf' || $doc_type == 'dwg' || $doc_type == 'doc'){
												$file_ext = '  ' . strtoupper($doc_type);
											}
											?>
											<div class="resource-icon">
												<a href="<?php echo get_field('resource_document'); ?>">
													<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 14.552081 14.552081" version="1.1" id="svg4585">
													  <g inkscape:label="Layer 1" inkscape:groupmode="layer" id="layer1" transform="translate(0,-282.44795)">
														<path
														   style="fill:#30BDFF;fill-opacity:1;stroke:#30BDFF;stroke-width:0.15;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:.55"
														   d="m 7.0039195,296.58724 c -0.5488034,-0.0316 -0.9353683,-0.0863 -1.393936,-0.19725 -1.4313074,-0.34617 -2.7261117,-1.13322 -3.6747918,-2.23373 -0.8116055,-0.9415 -1.34117615,-2.04898 -1.55911391,-3.26055 -0.0370311,-0.20585 -0.0690245,-0.45882 -0.0899939,-0.71152 -0.0131286,-0.15819 -0.0132177,-0.78638 -1.3746e-4,-0.94625 0.0558934,-0.68319 0.18252567,-1.26483 0.40883505,-1.87784 0.39037622,-1.05743 1.05345402,-2.01756 1.91457182,-2.77229 1.1097261,-0.97262 2.4808974,-1.56688 3.969049,-1.72017 0.2749321,-0.0283 0.3777568,-0.033 0.7226457,-0.0331 0.3460815,-8e-5 0.4429922,0.004 0.7263137,0.0334 1.4962973,0.15338 2.8850183,0.76123 4.0020643,1.75173 0.117876,0.10452 0.357136,0.33906 0.475951,0.46657 0.999644,1.07273 1.63459,2.46223 1.78653,3.90961 0.02985,0.28439 0.03384,0.36889 0.03377,0.71519 -7e-5,0.3464 -0.0045,0.4402 -0.03366,0.71519 -0.09304,0.87698 -0.36111,1.73784 -0.78198,2.51121 -0.815573,1.49865 -2.14707,2.63783 -3.7732788,3.22829 -0.6117676,0.22214 -1.2133824,0.35037 -1.9148276,0.40816 -0.1261601,0.0104 -0.7053208,0.0198 -0.8180202,0.0133 z m 0.7859529,-0.0725 c 1.1648679,-0.0863 2.2409336,-0.43031 3.2197786,-1.02925 0.80266,-0.49114 1.518576,-1.16761 2.056098,-1.94279 0.699733,-1.00912 1.098431,-2.14665 1.191282,-3.39889 0.01258,-0.16971 0.01263,-0.69761 8.2e-5,-0.86556 -0.06301,-0.84305 -0.253506,-1.598 -0.590929,-2.34195 -0.72771,-1.60445 -2.077195,-2.88193 -3.7423358,-3.54267 -0.6921814,-0.27466 -1.3917181,-0.43283 -2.1532639,-0.48687 -0.2110999,-0.0149 -0.691169,-0.017 -0.8877173,-0.004 -0.9888034,0.0664 -1.8722493,0.30091 -2.7255116,0.7235 -1.9577294,0.96959 -3.32703481,2.76854 -3.71577361,4.88165 -0.0407666,0.22161 -0.0724425,0.46772 -0.0929919,0.72253 -0.0172841,0.21431 -0.0172778,0.74451 1.4e-5,0.96093 0.0271862,0.34027 0.071101,0.64197 0.13769768,0.94597 0.37632363,1.71777 1.41027703,3.22276 2.89369773,4.21197 0.3970474,0.26477 0.8471652,0.50017 1.3086163,0.68437 0.7333216,0.29274 1.5482681,0.46286 2.3703274,0.4948 0.1336585,0.005 0.5907033,-0.004 0.7309294,-0.0139 z m -4.191549,-1.62001 c -0.00522,-0.008 -0.0072,-0.16261 -0.00846,-0.66125 l -0.00108,-0.43256 -0.3853793,-0.38577 -0.3853793,-0.38578 0.0049,-0.12059 c 0.00267,-0.0663 7.637e-4,-0.73738 -0.00426,-1.49123 -0.00845,-1.26919 -0.00823,-1.37154 0.00293,-1.38271 0.010806,-0.0108 0.049848,-0.0123 0.3759705,-0.0142 l 0.3639122,-0.002 -0.00477,-1.06153 c -0.00261,-0.58384 -0.00636,-1.149 -0.0083,-1.25591 -0.00197,-0.10691 -0.00566,-0.59544 -0.00817,-1.08563 -0.00255,-0.49018 -0.00689,-1.21307 -0.00967,-1.60643 -0.00395,-0.56017 -0.00312,-0.71851 0.00401,-0.73053 0.00809,-0.0138 0.018251,-0.0158 0.098803,-0.0202 0.049377,-0.003 1.3602351,-0.005 2.9130145,-0.005 h 2.8232318 l 0.01204,0.0184 c 0.00662,0.0101 0.2039596,0.20402 0.4385261,0.43092 0.2345651,0.2269 0.5354541,0.51819 0.6686411,0.6473 0.133187,0.12912 0.392349,0.37982 0.575916,0.55711 0.183567,0.1773 0.384104,0.37132 0.445639,0.43116 l 0.111882,0.10881 v 0.0933 c 0,0.0514 0.0033,0.56535 0.0072,1.14227 0.0039,0.57692 0.01209,1.86262 0.01804,2.8571 0.0059,0.99449 0.01258,2.00786 0.01473,2.25194 0.0022,0.24408 0.006,0.81615 0.0087,1.27125 0.0046,0.80796 0.0045,0.82755 -0.0091,0.83093 -0.02491,0.006 -8.0596616,0.007 -8.0634945,6.4e-4 z m 8.0066816,-0.8577 c -0.0052,-0.83909 -0.01118,-1.7955 -0.03377,-5.3617 -0.0075,-1.18488 -0.01604,-2.15821 -0.01897,-2.16295 -0.004,-0.006 -0.05556,-0.008 -0.204277,-0.004 -0.109421,0.003 -0.570357,0.006 -1.024304,0.008 -0.4539446,0.002 -0.8619517,0.006 -0.9066804,0.008 l -0.081324,0.004 -0.0049,-0.49699 c -0.00267,-0.27335 -0.0049,-0.63426 -0.0049,-0.80204 -5.1e-6,-0.16776 -0.00216,-0.44124 -0.00484,-0.60771 l -0.00484,-0.30267 -2.8603425,3.4e-4 c -2.5983823,1.7e-4 -2.860749,0.001 -2.8647843,0.0118 -0.00242,0.006 -6.364e-4,0.62273 0.00401,1.36969 0.00465,0.74696 0.010106,1.66344 0.012129,2.03663 0.00204,0.37318 0.00528,0.80229 0.0073,0.95359 0.00197,0.15128 0.00414,0.49623 0.00471,0.76653 6.059e-4,0.27031 0.00261,0.50715 0.00439,0.52631 l 0.00331,0.0349 h 3.5678187 3.5678165 l 0.019,0.0226 0.019,0.0226 3.37e-4,0.46706 c 1.69e-4,0.25689 0.0017,0.48687 0.0032,0.51107 0.0024,0.0378 0.01517,1.82946 0.01372,1.92775 -3.37e-4,0.0194 -0.0037,0.0384 -0.0076,0.0424 -0.0046,0.004 -1.2836276,0.007 -3.5897606,0.007 l -3.5826432,-3.3e-4 0.00484,0.32834 c 0.00267,0.18058 0.00554,0.53464 0.00638,0.7868 8.273e-4,0.25215 0.00267,0.51374 0.00414,0.58132 l 0.00261,0.12286 h 3.9770693 3.9770695 l -0.005,-0.80138 z M 3.5862888,293.5639 c -0.00261,-0.0757 -0.00484,-0.22913 -0.00484,-0.34109 l -2.04e-5,-0.20356 H 3.2384795 2.8955278 l 0.3410878,0.3411 c 0.1875989,0.18759 0.3440998,0.34109 0.3477806,0.34109 0.00395,0 0.00471,-0.0561 0.00191,-0.13754 z m 7.1637262,-1.36987 c -0.0015,-0.41958 -0.0048,-1.06655 -0.0073,-1.43772 l -0.0045,-0.67485 -3.935326,-0.002 -3.9353258,-0.002 0.00477,0.5593 c 0.00261,0.30761 0.00653,0.92899 0.00867,1.38085 0.00216,0.45186 0.00573,0.84874 0.00801,0.88197 l 0.00414,0.0604 3.9298816,-0.002 3.9298822,-0.002 z m 0.5222,-5.74455 c 0.140377,-0.003 0.254274,-0.007 0.253109,-0.0106 -0.0021,-0.006 -0.09548,-0.0965 -0.981716,-0.95339 -0.235957,-0.22815 -0.5907802,-0.57134 -0.7884989,-0.76265 l -0.3594894,-0.34784 -0.00235,0.18315 c -0.00134,0.10075 -7.07e-5,0.57064 0.00274,1.04423 l 0.00509,0.86107 0.8079453,-0.004 c 0.44437,-0.003 0.922798,-0.007 1.063173,-0.009 z"
														   id="path4546"
														   inkscape:connector-curvature="0" />
														<text xml:space="preserve" style="font-style:normal;font-weight:normal;font-size:2.40819693px;line-height:2.25;font-family:sans-serif;letter-spacing:0px;word-spacing:0px;fill:#9a9a9a;fill-opacity:1;stroke:none;stroke-width:0.26458332" x="3.9452205" y="292.29498" id="text4566" transform="scale(1.0000816,0.99991843)"><tspan
															sodipodi:role="line"
															id="tspan4564"
															x="3.2552205"
															y="292.52498"
															style="stroke-width:0.26458332"><?php echo $file_ext; ?></tspan></text>
													  </g>
													</svg>
												</a>
											</div>
											<?php
										}
										?>
										<div class="resource-details">
											<a href="<?php echo get_field('resource_document'); ?>" class="resource-title">
												<h4><?php the_title(); ?></h4>
											</a>
											<?php if(has_excerpt()){ ?>
												<p class="resource-excerpt"><?php echo get_the_excerpt(); ?></p>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php 
							endwhile; ?>
						</div>
					</div>
				</div>
				
				<?php 
				$term_count++;
			endif; 
		}
		
		// Close the last row if we have an odd number of terms
		if ($term_count > 0 && $term_count % 2 == 1) {
			echo '</div>'; // Close the last row
		}
		?>
	</div>
</div>

<?php
wp_reset_postdata(); ?>

<style>
.resources-subtitle {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
    font-size: 1.1rem;
}

.resource-category {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.resource-category:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.resource-category-title {
    color: #333;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #30BDFF;
    font-weight: 600;
}

.resource-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.resource-item {
    background: white;
    border-radius: 6px;
    padding: 1rem;
    border-left: 4px solid #30BDFF;
    transition: all 0.2s ease;
}

.resource-item:hover {
    background: #f0f8ff;
    transform: translateX(4px);
}

.resource-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.resource-icon {
    flex-shrink: 0;
}

.resource-icon a {
    display: block;
    transition: transform 0.2s ease;
}

.resource-icon a:hover {
    transform: scale(1.1);
}

.resource-details {
    flex: 1;
}

.resource-title h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    line-height: 1.3;
}

.resource-title:hover h4 {
    color: #30BDFF;
}

.resource-excerpt {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .resource-category {
        margin-bottom: 1.5rem;
    }
    
    .resource-content {
        gap: 0.75rem;
    }
    
    .resource-icon svg {
        width: 35px;
        height: 35px;
    }
    
    .resource-title h4 {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .resource-category {
        padding: 1rem;
    }
    
    .resource-item {
        padding: 0.75rem;
    }
    
    .resource-icon svg {
        width: 30px;
        height: 30px;
    }
}
</style>