<?php include_once('./tmp/header.php'); ?>

        <div id="wrap">
            <div id="header">
                <h1>Mindmeto + Twitter = Awesome reminders!</h1>
                <div id="introduction">
                    <p>Use Twitter to easily add and receive reminders with Mindmeto. This is how it works:</p>
                </div>
            </div>
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
            <div id="options">
            <p><a href="#nowhere" id="commandsLink" title="reveal all commands and formats">Learn more about our Twitter commands and formats &#x2193;</a></p>
                <div id="commands">
                    <div>
                    <h2>Use any of the following time formats:</h2>
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
                    <div>
                    <h2>Configure mindmeto with these commands:</h2>
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
                <p><a href="list.php" title="Log in with yu Twitter account to view and edit you reminders">View and edit your scheduled reminders &#x2192;</a></p>
            </div>
        </div>

<?php include_once('./tmp/footer.php'); ?>