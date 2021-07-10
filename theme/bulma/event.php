<?php
/*
 * Paste <https://github.com/jordansamuel/PASTE> - Clean theme
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License in GPL.txt for more details.
 */
?>
<script>(function () {
  const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

  let deadline = "May 28, 2021 20:00:00 GMT+0",
      countDown = new Date(deadline).getTime(),
      x = setInterval(function() {    

        let now = new Date().getTime(),
            distance = countDown - now;

        document.getElementById("days").innerText = Math.floor(distance / (day)),
          document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
          document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
          document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

        //do something later when date is reached
        if (distance < 0) {
          let headline = document.getElementById("headline"),
              countdown = document.getElementById("countdown"),
              content = document.getElementById("content");

          headline.innerText = "New date coming soon";
          countdown.style.display = "none";
          content.style.display = "block";

          clearInterval(x);
        }
        //seconds
      }, 0)
  }());
  </script> 
<main class="bd-main">
<!-- START CONTAINER -->
	<div class="bd-side-background"></div>
<div class="bd-main-container container">
	<div class="bd-duo">
		<div class="bd-lead">
  <!-- Start Row -->
  <div class="row">
    <!-- Start Panel -->
<?php if ($privatesite == "on") { // Site permissions ?>
	<div class="col-md-12">
		<div class="panel panel-default" style="padding-bottom: 100px;">
			<div class="error-pages">
				<i class="fa fa-lock fa-5x" aria-hidden="true"></i>
				<h1><?php echo $lang['siteprivate']; ?></h1>
			</div>
		</div>
	</div>
	
<?php } else { ?>
	
   <?php } 
	if ( isset($privatesite) && $privatesite == "on") { // Remove 'recent pastes' if site is private
	} else { ?>
    <div class="notification is-warning">
                  <strong id="headline">Entries Deadline</strong>
                        <div id="countdown">
                        <p>
                            <span id="days"></span> Days,
                            <span id="hours"></span> Hours,
                            <span id="minutes"></span> Minutes,
                            <span id="seconds"></span> Seconds Remaining 
                        </p>
                        </div>  
                    <div class="message">
                            <div id="content">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
    			<h1 class="title is-4">Welcome to Ponepaste /pj50kb/ Pastejam<h1>
                  <h1 class="title is-5">No context 50kb Challenge<h1>
                   <b> What is the PasteJam 50kb challange?</b> 
                    <p> The PasteJam 50kb challenge is a competition that last for two weeks that any one can join.</p>
                    <b> What do I win? </b>
                    <p style="color:green;"> >A fucking badge, plus the best pony game the host can find.</p>
                    <img src="/img/prize.png">
                    <p>Note: This is a steam game, winner will be sent a cdkey</p>
                    <br>
                    <b> What do I have to do? </b>
                    <p> A prompt is given and you have to write a 50kb greentext or prose. </p>
                    <b> Does it have to be exactly 50kb </b>
                    <p> As close as possible. This is more of a guide due to magin of error.</p>
                    <b> What will happen if I submit a paste thats under 50kb?</b>
                    <p> Again, As close as possible.</p>
                    <b> Can I ask for feedback for my green/prose?</b>
                    <p> From other anons on /mlp/ is fine.</p>
                    <b> Can I write anything? </b>
                    <p> As long it follows the prompt, mlp related and follows Ponepaste rules.</p>
                    <b> How is the prompt chosen?</b>
                    <p> Hand picked homonyms.</p>
                    <b> How do I submit? </b>
                    <p> Make a <b>UNLISTED</b> paste with /pj50kb/ in the title and tag.</p>
                    <b> How is the winner chosen?</b>
                    <p> By a vote after the closing date </p>
                    <b> When is the closing date? </b>
                    <p> 28th of May </p>
                    <b> When does the voting start? </b>
                    <p> 28th of May, to gives (you) time to read.</p>
                    <b> Where do I vote? </b>
                    <p> Here, on this page.</p>
                    <b> How long will the vote last? </b>
                    <p> Two weeks. Vote ends on 11th of June, 8pm UTC </p>
                    <b> What will I be voting on?</b>
                    <p> How well the story is written, how unique the idea is and how it fits the prompt. </p>
                    <b>Can the entry be a sequel/ side arc of one of their existing greens?</b>
                    <p>It must be a stand alone story. </p>
                    <br>
                    <br>   
                <div class="notification is-info">
                  <strong>Prompt:</strong>
                  <figure>
    <figcaption>Listen to the prompt:</figcaption>
    <audio
        controls
        src="prompt.mp3">
            Your browser is shit and does not support the
            <code>audio</code> element.
    </audio>
</figure>

                </div>
		<!-- Submitted Pastes -->
         <div class="col-md-9 col-lg-10">
        		<div class="panel panel-default">
		  <h1 class="title is-4">Submited Entries<h1>
			<div class="panel-body">
				<div class="list-widget pagination-content">
					<?php          
							$res = getevent($con,100);
							while($row = mysqli_fetch_array($res)) {
							$title =  Trim($row['title']);
                            $p_member =  Trim($row['member']);
							$titlehov = ($row['title']);
							$p_id =  Trim($row['id']);
							$p_date = Trim($row['date']);
							$p_time = Trim($row['now_time']);
							$nowtime = time();
							$oldtime = $p_time;
							$p_time = conTime($nowtime-$oldtime);
							$title = truncate($title, 24, 60);
                            $todea = strtotime("now");
					?>
					<?php
						echo '<header class="bd-category-header my-1">
									<a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a>
									<a class="icon is-pulled-right has-tooltip-arrow has-tooltip-left-mobile has-tooltip-bottom-desktop has-tooltip-left-until-widescreen" data-tooltip="' . $p_time . '">
										<i class="far fa-clock has-text-grey" aria-hidden="true"></i>
									</a>
									<p class="subtitle is-7">' . 'by ' . '
										<i><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a></i>
									</p>' .
								'</header>'; 
					?>
  
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo "None submitted"; } ?>
					</p>
				</div>
			</div>
                                                <div class="notification is-warning">
                         <strong id="headline">Note:</strong>
                        <div id="mess">
                        <p>
                        No one has been nominated for the Wooden Spoon Award.  
                        </p>
                        </div>  
                        </div>
                               <iframe width="620" height="444" src="https://strawpoll.com/embed/kzvcup4hp" style="width: 100%; height: 444px;" frameborder="0" allowfullscreen></iframe>
                    </div>
		</div>
        <hr>
       <!-- <iframe width="620" height="744" src="https://strawpoll.com/embed/kz179c835" style="width: 100%; height: 744px;" frameborder="0" allowfullscreen></iframe> -->
    	</div>
     </div>   
    </div>     
</main>


    <!-- End Panel -->
<?php } if ($privatesite == "on") { // Remove sidebar if site is private
	} elseif (isset($site_ads)) {
		echo $site_ads['ads_2'];
	}	
?>
