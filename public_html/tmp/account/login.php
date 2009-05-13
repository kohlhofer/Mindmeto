<?php include_once('./tmp/header.php'); ?>

	<div id="about">
		<div id="about-inner" class="center">
			<img src="http://mindmeto.com/public/img/logos/standard.png" alt="MindMeTo" />
		</div>
	</div>
	<div id="content-container">
		<div id="content" class="center clearfix">
			
		    <div id="login">
		        <p>
		            Follow the link below to grant MindMeTo.com access on Twitter.com. Once you do so you will be re-directed to your list of reminders.</p>
		            <a href="<?=$oAuthRequestLink?>">Sign in on Twitter.com</a> or
		            <a href="http://mindmeto.com" title="Go back to the home page">cancel</a>.
		        </p>    
			</div>
			
	    </div>
	</div>

<?php include_once('./tmp/footer.php'); ?>