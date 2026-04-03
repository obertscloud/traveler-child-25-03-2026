<div class="accordion-item">
<!-- Begin People Viewing Notice -->            
<?php
$viewers_list = [1, 5, 3, 10, 2, 8, 20, 4, 11, 19, 6, 14, 25, 7, 16, 9];
$booked_list = [1, 2, 3, 4, 5];
$people_viewing = $viewers_list[array_rand($viewers_list)];
$slots_booked = $booked_list[array_rand($booked_list)];
$show_booked = (rand(0, 1) === 1);
$days_advance = get_post_meta(get_the_ID(), '_days_advance', true);
?>
<div class="people-viewing-notice" style="display:block !important;opacity:1 !important;background:#f5f5f5;padding:12px;border:1px solid #2e7d32;margin-top:8px;font-size:14px;color:#333333;z-index:9999;font-weight:bold;border-radius:8px;">
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.42 11.968V6.567h1.5v6.022l-3.028 3.028-1.06-1.06zm9.357 2.925-5.417 5.416-2.89-2.89 1.06-1.06 1.83 1.829 4.356-4.356z"></path><path d="M20.41 12a8.24 8.24 0 0 0-8.128-8.239v-1.5c5.327.06 9.627 4.397 9.627 9.739q0 .135-.003.268h-1.501q.004-.135.004-.268m-8.273 8.239a8.24 8.24 0 0 1-5.46-14.38l-.978-1.137A9.72 9.72 0 0 0 2.43 12c0 5.367 4.343 9.72 9.706 9.739z"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.42 5.733H2.09v-1.5h4.83v4.83h-1.5z"></path></svg> <span style="color:#2e7d32;"><?php echo esc_html($people_viewing); ?></span> travelers are viewing this tour right now &bull; spaces are filling quickly</strong><br>
<?php if ($show_booked) { ?>
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.23 6.645v10.71h17.54V6.644zm-1-1.5a.5.5 0 0 0-.5.5v12.71a.5.5 0 0 0 .5.5h19.54a.5.5 0 0 0 .5-.5V5.644a.5.5 0 0 0-.5-.5z"></path><path d="M2.605 8.554h18.933V12H2.605z"></path></svg> <span style="color:#2e7d32;"><?php echo esc_html($slots_booked); ?></span> just booked &bull; Book now!</strong><br>
<?php } ?>
<?php if (!empty($days_advance)) { ?>
<strong><svg viewBox="0 0 24 24" width="16px" height="16px" style="fill:#2e7d32;vertical-align:middle;margin-right:4px;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.007 2.75a.75.75 0 0 1 .748.752l-.002.748h2.512V3.5a.75.75 0 0 1 1.5 0v.75h2.472l-.002-.748a.75.75 0 1 1 1.5-.004l.002.752h4.018v17H3.245v-17h4.008l.002-.752a.75.75 0 0 1 .752-.748m-.758 3H4.745v14h14.51v-14h-2.513l.006 1.747-1.5.005-.006-1.752h-2.477V7.5h-1.5V5.75H8.749l-.004 1.752-1.5-.004zm-.004 6h5.5v5.5h-5.5zm1.5 1.5v2.5h2.5v-2.5z"></path></svg> Book ahead &bull; This is booked <span style="color:#2e7d32;"><?php echo esc_html($days_advance); ?></span> days in advance on average</strong>
<?php } ?>
</div><br>
<!-- End People Viewing Notice -->

	<h2 class="st-heading-section" id="headingDescription">
		<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription" aria-expanded="true" aria-controls="collapseDescription">
			<?php echo esc_html__( 'Description', 'traveler' ) ?>
		</button>
	</h2>
	<div id="collapseDescription" class="accordion-collapse collapse show" aria-labelledby="headingDescription" data-bs-parent="#headingDescription">
		<div class="accordion-body d-flex">
			<div class="st-description" data-toggle-section="st-description">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</div>