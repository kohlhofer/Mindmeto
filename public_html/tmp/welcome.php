<?php include_once('./tmp/header.php'); ?>

	<div id="about">
		<img src="public/img/logos/standard.png" alt="MindMeTo" />
	</div>
	<div id="two-column" class="clearfix">
		<div id="left-column">
	
			<div id="how-to">
				<ol>
	                <li>
	                    Follow <a href="http://twitter.com/mindmeto" title="Open the mindmeto profile on Twitter to follow it...">mindmeto</a> on Twitter.
	                    <span class="note">We will quickly follow you back.</span>
	                </li>
	                <li>
	                    Add a reminder by tweeting: <strong> @mindmeto buy some milk tomorrow</strong>
	                    <span class="note">This will add "Buy some milk" to tomorrow's schedule. (You can also add your reminder privately by sending a direct message.)</span>
	                </li>
	                <li>
	                    At the scheduled time we will send you a private direkt message to "buy some milk".
	                </li>
	        	</ol>
	    	</div>
    	
	    	<a name="commands"></a>
			<div id="commands" class="clearfix">
	             <div class="left" style="width: 256px; margin: 0 30px 0 0">
	                    <h2>MindMeTo is super easy to use and extremely flexible</h2>
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
	              		<h2>MindMeTo is fully configurable and works the way you want it to</h2>
	                    <ul>
	                    	<li>dm mindmeto timezone GMT+3 <span class="note">Sets your time zone to GMT+3</span></li>
	                    	<li>dm mindmeto default time 8 <span class="note">Reminders without a specific time will be sent at 8am (use 24 hour values)</span></li>
	                    	<li>dm mindmeto list reminders <span class="note">Will send you a link to your reminders</span></li>
	                     	<li>dm mindmeto cancel #123 <span class="note">#123 is the id of a reminder as stated in the confirmation</span></li>
	                     	<li>dm mindmeto confirmations ON <span class="note">New reminders will be confirmed with a direct message</span></li>
	                     	<li>dm mindmeto confirmations OFF</li>
	                     	<li>dm mindmeto reminders OFF <span class="note">Will suspend all reminders until you turn this ON again</span></li>
	                     	<li>dm mindmeto reminders ON</li>
	                    </ul>
	              </div>
			</div>
    	
		</div>
		<div id="right-column">
		
			<ul id="ticker"></ul>
		
		</div>
	</div>

<?php include_once('./tmp/footer.php'); ?>