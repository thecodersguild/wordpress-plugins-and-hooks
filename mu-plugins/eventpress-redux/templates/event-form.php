<div id="epr-event-after-title" class="postbox">
	<h3 class="hndle"><span><?php _e( 'Event Details', 'eventpress-redux' ); ?></span></h3>

	<div class="inside">
		<div id="event-fields fields">
			<label for="epr-event-start-date"><?php _e( 'Start:', 'eventpress-redux' ); ?></label>
			<br/>

			<input type="datetime-local" name="epr_event[start]" id="epr-event-start-date" value="<?php echo esc_attr( $start ); ?>">
			<br/>

			<label for="epr-event-end-date"><?php _e( 'End:', 'eventpress-redux' ); ?></label>
			<br/>

			<input type="datetime-local" name="epr_event[end]" id="epr-event-end-date" value="<?php echo esc_attr( $end ); ?>"
			       class="date-picker">
			<br/>

			<label for="epr-event-venue"><?php _e( 'Venue:', 'eventpress-redux' ); ?></label>
			<br/>
			<select type="text" name="epr_event[venue_id]" id="epr-event-venue">
				<?php wp_kses_post( $options ); ?>
			</select>
		</div>
		<br/>
		<?php wp_nonce_field( 'save_event', 'epr_event_nonce' ); ?>
	</div>
</div>
