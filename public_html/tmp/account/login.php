<?php include_once('./tmp/header.php'); ?>

<div id="wrap">
    <div id="header">
        <h1>Sign in with your Twitter account...</h1>
    </div>
    <div id="oauth">
        <p>
            When you click the button below you will be sent to Twitter.com and asked to grant mindmeto.com access to your account.
        </p>
        <p>
            Once you do you will be re-directed to your list of reminders.<p>
        <p>
            <a href="<?=$oAuthRequestLink?>">Sign in on Twitter.com</a> or
            <a href="index.html" title="Go back to the home page">cancel</a>.
        </p>    
    </div>
</div>

<?php include_once('./tmp/footer.php'); ?>