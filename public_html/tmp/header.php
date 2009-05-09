<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>MindMeTo</title>
		<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>public/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>public/css/style.css" />
		<style type="text/css">@import url('http://s3.amazonaws.com/getsatisfaction.com/feedback/feedback.css');</style>
		<script src="public/js/jquery-1.3.2.js" type="text/javascript"></script>
		<script src="public/js/mindmeto.js" type="text/javascript"></script>
		<?php if( isset($headerCode) ) echo $headerCode; ?>
	</head>
	<body>

		<div id="masthead">
			<div id="masthead-inner" class="center clearfix">
				<?php if( $session->loggedIn ): ?>
				<div class="left">
					<ul class="clearfix">
						<li>
							<a href="index.php">Home</a>
						</li>
						<li>
							<a href="index.php#commands">Commands</a>
						</li>
					</ul>
				</div>
				<?php endif; ?>
				<div class="right">
					<ul class="clearfix">
						<li style="text-align: right">
							
							<?php if( !$session->loggedIn ): ?>
							
							<a href="list.php">Login</a><br />
							<span class="note">Login is powered by Twitter</span>
							
							<?php else: ?>
								
							<a href="list.php">Your account</a><br />
							<span class="note">Hello <?=$session->userDetails['user_twitter_data']->screen_name?> (<a href="logout.php">logout</a>)</span>
								
							<?php endif; ?>
						</li>
						<li style="margin: 0">
							
							<?php if( !$session->loggedIn ): ?>
							<img src="public/img/avatars/avatar.png" alt="MindMeTo" class="avatar" />
							<?php else: ?>
							<img src="<?=$session->userDetails['user_twitter_data']->profile_image_url?>" alt="<?=$session->userDetails['user_twitter_data']->screen_name?>" class="avatar" />
							<?php endif; ?>
							
						</li>
					</ul>
				</div>
			</div>
		</div>