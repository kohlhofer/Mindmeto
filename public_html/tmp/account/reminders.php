<?php include_once('./tmp/header.php'); ?>

<div id="about">
	<div id="about-inner" class="center">
		<img src="http://mindmeto.com/public/img/logos/logged-in.png" alt="MindMeTo" />
	</div>
</div>			
<div id="content-container">
	<div id="content" class="center clearfix">

		<div class="left-column">

			<div id="reminders">
				<?php 
				if( $queryResult !== NULL ) {
					echo '<div id="reminder-web-result">';
					echo $queryResult;
					echo '</div>';
				}
				if( isset( $existingReminders ) && $existingReminders->numRows() > 0 ) {

					echo '<ul>';
					while( $reminder = $existingReminders->getResultsArray() ) {

						echo '<li class="clearfix" id="reminder-'.$reminder['reminder_id'].'">'.
							'<a href="ajax.php?a=remove&id='.$reminder['reminder_id'].'" onclick="return removeReminder(\''.$reminder['reminder_id'].'\')" class="remove"></a>'.
							'<span class="reminder-data">'.$reminder['reminder_text'].'</span>'.
							'<span class="reminder-meta">';

							$timestamp = convertDefaultTime( $session->userId, strtotime($reminder['reminder_timestamp']));
							if( $timestamp !== false ) {
								echo date( 'jS M \a\t g:iA', $timestamp);
							} else {
								echo date('jS M \a\t g:iA', strtotime($reminder['reminder_timestamp']));
							}

						echo '</span></li>';

					}
					echo '</ul>';

				} else {

					echo '<div id="no-reminders">You currently have no reminders set!</div>';

				} ?>

			</div>

		</div>
		<div id="reminder-web-input" class="right-column">

			<div class="speech">Add a new reminder</div>

			<div id="reminder-web-form" class="clearfix">
				<form action="list" method="post">
					<textarea name="command" class="large-input" maxlength="200"></textarea>
					<input type="submit" class="button" value="Go!" />
				</form>
			</div>

			<div id="reminder-settings">
				<b>Any reminders you set in the web interface will be kept private</b><br /><br />
				Your timezone is currently set to GMT<b><?=substr( $session->userDetails['user_timezone'], 0, stripos($session->userDetails['user_timezone'], ":"))?></b><br />
				Reminders without a time set will arrive at <b><?=$session->userDetails['user_default_time']?><?php if( $session->userDetails['user_default_time'] > 12 ) { echo 'PM'; } else { echo 'AM'; }?></b><br />
				<?php echo ( $session->userDetails['user_allow_reminders'] ) ? "Direct Message reminder messages are <b>ON</b>" : "Direct Message reminder messages are <b>OFF</b>"; ?><br />
				<?php echo ( $session->userDetails['user_allow_confirmations'] ) ? "Direct Message confirmation messages are <b>ON</b>" : "Direct Message reminder messages are <b>OFF</b>"; ?>
			</div>

		</div>

	</div>
</div>
<div id="commands-container">
	<div id="commands" class="center clearfix">

		<div class="left-column">

			<div class="left" style="width: 256px; margin: 0 30px 0 0">
				<h2>Easy and extremely flexible</h2>
				<ul>
					<li>@mindmeto ... in two hours</li>
					<li>@mindmeto ... on Tuesday</li>
					<li>@mindmeto ... next Friday</li>
					<li>@mindmeto ... in two weeks</li>
					<li>@mindmeto ... in 2 months</li>
					<li>@mindmeto ... on October 24th</li>
					<li>@mindmeto ...</li>
				</ul>
			</div>
			<div class="left" style="width: 256px">
				<h2>Make MindMeTo work your way</h2>
				<ul>
					<li>d mindmeto timezone GMT+3 <span class="note">Sets your time zone to GMT+3. You can find out your timezone relative to GMT <a href="http://wwp.greenwichmeantime.com/">here</a>.</span></li>
					<li>d mindmeto default time 8 <span class="note">Reminders without a specific time will be sent at 8am (use 24 hour values)</span></li>
					<li>d mindmeto list reminders <span class="note">Will send you a link to your reminders</span></li>
					<li>d mindmeto cancel #123 <span class="note">#123 is the id of a reminder as stated in the confirmation</span></li>
					<li>d mindmeto confirmations ON <span class="note">New reminders will be confirmed with a direct message</span></li>
					<li>d mindmeto confirmations OFF</li>
					<li>d mindmeto reminders OFF <span class="note">Will suspend all reminders until you turn this ON again</span></li>
					<li>d mindmeto reminders ON</li>
				</ul>
			</div>

		</div>

	</div>
</div>

<?php include_once('./tmp/footer.php'); ?>