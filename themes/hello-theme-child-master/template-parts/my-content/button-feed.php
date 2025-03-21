<?php
	$fundIds = md_get_users_subscribed_funds();
	$tickerIds = md_get_users_subscribed_tickers();
	if ( (empty($fundIds)) && (empty($tickerIds)) ){

		echo "<p>You haven't subscribed to any Funds or Tickers yet, go and save some to view them here!</p>";

	} else {

		echo "<div class='recommended'>";
		echo "<div class='funds-div'>";
		echo "<div class='table-responsive'>";

			// Display Funds

				if (!empty($fundIds)) {

					$content_feed = new WP_Query(array(
						'post_type' => 'any',
						'posts_per_page' => -1,
						'post__in' => $fundIds,
						'orderby' => 'modified', // Order by last update date
    					'order' => 'DESC', // Latest update first
					));
					
					
					if ($content_feed->have_posts()) :

						
						?>
						<table>
						<tr>
							<th style="min-width:470px">Fund name</th>
							<th style="min-width:220px">Last update</th>
							<th style="min-width:220px" colspan="2">Letters</th>
							<th style="min-width:150px">Action</th>
						</tr>
						<?php

						while ($content_feed->have_posts()) : $content_feed->the_post();

							global $post;

		                    $fund_id = $post->ID;
							

							// $fund_published_date = get_the_date('Y-m-d', $fund_id);

							$fund_published_date = get_the_modified_date('Y-m-d', $fund_id);

		            		$fund_published_date_formatted = date('m/d/Y', strtotime($fund_published_date));
							?>
								<tr id='md-recommended-fund-id-<?=$fund_id?>'>
								<?php
								// $currentDate = new DateTime();
								// $providedDate = DateTime::createFromFormat('m/d/Y', $fund_published_date_formatted);
								// $interval = $currentDate->diff($providedDate);
								// $daysDifference = $interval->days;
					
						
								?>
									<td><a href="<?=get_permalink();?>"><?= get_the_title(); ?></a></td>
									<td><center><?=$fund_published_date_formatted;?></center></td>
									<td colspan="2">
									<center>
										<?php
										global $wpdb;

										$letters_array = array();
	
										$jet_table_name = $wpdb->prefix.'jet_rel_default';          
											$posts_table_name = $wpdb->prefix.'posts';   
											// $query = "
											// 	SELECT jet_rel.child_object_id, posts.*
											// 	FROM ".$jet_table_name." AS jet_rel
											// 	INNER JOIN ".$posts_table_name." AS posts ON jet_rel.child_object_id = posts.ID
											// 	WHERE jet_rel.parent_object_id = '".$fund_id."'
											// 	AND posts.post_status = 'publish'
											// 	AND posts.post_date >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) + 365 DAY
											// 	AND posts.post_date < CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 1 DAY
											// 	GROUP BY jet_rel.child_object_id
											// 	ORDER BY posts.post_title DESC
											// ";

											$query = "
												SELECT jet_rel.child_object_id, posts.*
												FROM ".$jet_table_name." AS jet_rel
												INNER JOIN ".$posts_table_name." AS posts ON jet_rel.child_object_id = posts.ID
												WHERE jet_rel.parent_object_id = '".$fund_id."'
												AND posts.post_status = 'publish'
												GROUP BY jet_rel.child_object_id
												ORDER BY posts.post_title DESC
											";

											// echo $query;
											// die;
											
											$letters = $wpdb->get_results($query);

											// echo"<pre>";
											// print_r($letters);
											// die;
	
										//    $letters = $wpdb->get_results("SELECT * FROM ".$jet_table_name." AS jet_rel INNER JOIN ".$posts_table_name." AS posts ON jet_rel.child_object_id = posts.ID WHERE jet_rel.parent_object_id = '".$fund_id."' AND posts.post_status = 'publish' AND posts.post_date >= curdate() - INTERVAL DAYOFWEEK(curdate())+365 DAY AND posts.post_date < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY");

										$letters = $wpdb->get_results($query);
										$i = 1;
	
										   echo "<ul class='dot-list'>";
											
	
											if ($letters){
	
												  foreach( $letters as $letter ) {

													$terms = wp_get_post_terms($letter->ID, 'quarter');
													$term_name = '';
													foreach($terms as $term){
														$term_name = $term->name;
													}
													if($i > 2)
	
														break;
	
													$letter_id = $letter->child_object_id;

													// echo get_the_title().'/';
													// echo $letter->post_title;
													
													$letter_title = str_replace(get_the_title(), '', $letter->post_title);
	
													$letter_url = get_the_permalink($letter_id);
													// $letter_published_date = get_the_date('Y-m-d', $letter_id);
	
													$letter_published_date = get_the_modified_date('Y-m-d', $letter_id);

													$letter_published_date_formatted = date('m/d/Y', strtotime($letter_published_date));
													// echo "<li><a href='".$letter_url."'>".$letter_title." <span>".$letter_published_date_formatted."</span></a></li>";
													echo "<li><a href='".$letter_url."'>".$term_name." Letter </a></li>";
	
													 $i++;
												  }
											}
	
										  echo "</ul>";
										?>
										</center>
									</td>
									<td><?php echo "<button id='md_fund_subscribe_btn_id_".$fund_id."' class='md-fund-subscribe-btn md-users-favourites-fund-unsubscribe-btn md-fund-subscribe-btn--subscribed' data-fund-id='".$fund_id."'>Unfollow</button>";?></td>
								</tr>
					

							<?php

						endwhile;
						echo "</table>";
					endif;
					wp_reset_query();

				}
				echo "</div><!-- /.table-responsive -->";
		echo "</div><!-- /.fund-div -->";
		echo "<div class='tickers-div'>";
		echo "<div class='table-responsive'>";

			// Display Tickers

			if (!empty($tickerIds)) {
				// Create an array to store ticker names for sorting
				$tickerNames = array();
			
				foreach ($tickerIds as $tickerId) {
					$ticker_title = get_term($tickerId)->name;
					$tickerNames[$tickerId] = $ticker_title;
				}
				
			
				// Sort the array by ticker names
				asort($tickerNames);
			
				?>
			
				<table>
					<tr>
						<th style="min-width:117px">Ticker name</th>
						<th style="min-width:150px">Last Update</th>
						<th style="min-width:150px">Letters</th>
						<th style="min-width:390px">Fund name</th>
						<th style="min-width:131px">Action</th>
					</tr>
			
					<?php
					foreach ($tickerNames as $tickerId => $ticker_title) {
						// Continue with the rest of the existing code
						$ticker_slug = get_term($tickerId)->slug;
						$ticker_url = get_site_url() . "/tickers/" . $ticker_slug;
						// md_get_letters_ids_for_tickers expects to receive an array so convert $tickerId to array

						$modify_tickertitle = str_replace('|', ',', $ticker_title);
						?>
			
						<tr id='md-recommended-ticker-id-<?= $tickerId ?>'>
							<td>
							
							<a style="word-break: break-word;" href="<?= $ticker_url ?>"><?= $modify_tickertitle ?></a></td>
							
							<td class="border" colspan="3" style="padding:0;">
								<table class="combine-table">
									<?php
									$ticker_array = array($tickerId);
									$letters = md_get_letters_ids_for_tickers($ticker_array);
									
									$ii = 1;
									$fund_names = array(); // Initialize an array to store fund names

									if ($letters) {
										foreach ($letters as $letter) {
											$post_type = get_post_type($letter);
											if($post_type == 'elevator_pitch'){
												continue;
											}
											if ($ii > 2) {
												break;
											}

											$letter_id = $letter;
											$terms = wp_get_post_terms($letter_id, 'quarter');
											// echo "<pre>";
											// print_r($terms);
											// die;
											$term_name = '';
											foreach($terms as $term){
												$term_name = $term->name;
											}
											$meta = get_post_meta($letter_id, 'fund_text_meta_key', true);
											$letter_post  = get_post($letter_id);
											$letter_title = str_replace($meta, '', get_the_title($letter_id));
											$letter_url  = get_the_permalink($letter_id);
											$letter_published_date = get_the_modified_date('Y-m-d', $letter_id);
											$letter_published_date_formatted = date('m/d/Y', strtotime($letter_published_date));
											$related_funds_array = md_get_related_fund_ids($letter_id);
											$fund_name = get_the_title($related_funds_array[0]);

											// if ((get_post_status($letter_id) == 'publish') && (strtotime($letter_published_date) > strtotime('-365 days'))) { 
																 				
												// $term_names = "<a href='" . $letter_url . "'> " . $term_name . " Letter <span>" . $letter_published_date_formatted . "</span></a>";

												$term_names = "<a href='" . $letter_url . "'> " . $term_name . " Letter </a>";
												
												?>
												<tr>
													<td><?= $letter_published_date_formatted ?></td> 
													<td>
													<?php echo "<ul class='dot-list'><li>";
														echo $term_names. "</li></ul>"; ?>

													</td> 
													<td><?php echo $fund_name; ?></td> 
												</tr>
												<?php $ii++;	
												 
											// }
										}
									} 
									?>
									
								</table>
							</td> 
							<td>
								<?php
								echo "<button id='md_ticker_subscribe_btn_id_" . $tickerId . "' class='md-ticker-subscribe-btn md-users-favourites-ticker-unsubscribe-btn md-ticker-subscribe-btn--subscribed' data-fund-id='" . $tickerId . "'>Unfollow</button>";
								?>
							</td>
			
						</tr>
			
					<?php
			
					}
					echo "</table>";
				}
			
				echo "</div><!-- /.table-responsive -->";
		echo "</div><!-- /.ticker-div -->";
		echo "</div><!-- /.recommended -->";

	}

