<?php include_once('./tmp/header.php'); ?>

			<div id="about">
				<div id="about-inner" class="center">
					<img src="public/img/logos/logged-in.png" alt="MindMeTo" />
				</div>
			</div>			
			<div id="content-container">
				<div id="content" class="center clearfix">

					<div class="left-column">

						<div id="reminders">
						
							<?php if( $queryResult !== NULL ) echo $queryResult; ?>
						
					        <?php if( isset( $existingReminders ) && count( $existingReminders ) > 0 ):

								echo '<ul>';
									while( $reminder = $existingReminders->getResultsArray() ) {

										echo '<li class="clearfix" id="reminder-'.$reminder['reminder_id'].'">'.
											 '<div class="reminder-data">'.
											 	'<a href="ajax.php?a=remove&id='.$reminder['reminder_id'].'" onclick="return removeReminder(\''.$reminder['reminder_id'].'\')" class="remove"></a> '.$reminder['reminder_text'].
											 '</div>'.
											 '<div class="reminder-meta">';
										
										$timestamp = convertDefaultTime( $session->userId, strtotime($reminder['reminder_timestamp']));
										if( $timestamp !== false ) {
											echo date( 'jS M \a\t g:iA', $timestamp);
										} else {
											echo date('jS M \a\t g:iA', strtotime($reminder['reminder_timestamp']));
										}
									
										echo '</div></li>';

									}
								echo '</ul>';

							else: ?>

								You currently have no reminders set!

							<?php endif; ?>
						</div>
						
					</div>
					<div class="right-column">

						<div id="reminder-web-input">

							<div class="speech">Add a new reminder</div>
						
							<div id="reminder-web-form">
								<form action="#" method="post">
									<input type="text" name="command" class="large-input" />

									<input type="submit" value="Go!" />
								</form>
							</div>
						
							User timezone: GMT<?=substr( $session->userDetails['user_timezone'], 0, stripos($session->userDetails['user_timezone'], ":"))?><br />
							Default time: <?=$session->userDetails['user_default_time']?><br />
							<?php echo ( $session->userDetails['user_allow_reminders'] ) ? "Reminders are ON" : "Reminders are OFF"; ?><br />
							<?php echo ( $session->userDetails['user_allow_confirmations'] ) ? "Confirmations are ON" : "Confirmations are OFF"; ?>

						</div>

					</div>
					
			</div>
		</div>

<?php include_once('./tmp/footer.php'); ?>