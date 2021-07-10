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

<main class="bd-main">
<!-- START CONTAINER -->
	<div class="bd-side-background"></div>
<div class="bd-main-container container">
	<div class="bd-duo">
		<div class="bd-lead">
  <!-- Start Row -->
  <div class="row">
  <section class="section">
      <div class="tabs">
         <ul class="tabs-menu">
          <li class="is-active" data-target="first-tab"><a>Popular</a></li>
          <li data-target="second-tab"><a>Months Pop</a></li>
          <li data-target="third-tab"><a>New</a></li>
          <li data-target="forth-tab"><a>Updated</a></li>
          <li data-target="fifth-tab"><a>Random</a></li>
        </ul>
      </div>
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
		
        
        <!-- Pop Pastes -->
   <div class="tab-content" id="first-tab">
        		<div class="panel panel-default">
		  <h1 class="title is-4"><?php echo $lang['popular']; ?><h1>
                <div class="columns is-multiline">
					<?php          
							$res = getpopular($con,10);
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
                            $p_tagsys = Trim($row['tagsys']);
                            $tags = htmlentities($p_tagsys, ENT_QUOTES, 'UTF-8');
                            $tagui = sandwitch($tags);
					?>
					<?php
					if ($mod_rewrite == '1') {
						echo '
                    <div class="column is-half">	                   
                        <div class="card">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content" style="overflow: hidden">
                                        <p class="title is-5"><a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a></p>
                                        <p class="subtitle is-6"><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a><br><time datetime="' . $p_date . '">' . $p_date . '</time></p>';
                                        if(strlen($p_tagsys) > 0) { 
                                            echo $tagui; 
                                        }else{ echo  ' <span class="tag is-warning">No tags</span>';}
                    echo                '</div>
                                </div>
                            </div>
                        </div><br>
                    </div>	'; 
                    
                    } else{
						echo '<a href="'  . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>'; 
                    }
					?>
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo $lang['emptypastebin']; } ?>
				</div>
		</div>
        </div>
     
        
        
        <!-- mPop Pastes -->
        <div class="tab-content" id="second-tab">
        		<div class="panel panel-default">
		  <h1 class="title is-4"><?php echo $lang['monthpopular']; ?><h1>
                <div class="columns is-multiline">
					<?php          
							$res = monthpop($con,10);
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
                            $p_tagsys = Trim($row['tagsys']);
                            $tags = htmlentities($p_tagsys, ENT_QUOTES, 'UTF-8');
                            $tagui = sandwitch($tags);
					?>
					<?php
					if ($mod_rewrite == '1') {
						echo '
                    <div class="column is-half">	                   
                        <div class="card">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content" style="overflow: hidden">
                                        <p class="title is-5"><a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a></p>
                                        <p class="subtitle is-6"><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a><br><time datetime="' . $p_date . '">' . $p_date . '</time></p>';
                                        if(strlen($p_tagsys) > 0) { 
                                            echo $tagui; 
                                        }else{ echo  ' <span class="tag is-warning">No tags</span>';}
                    echo                '</div>
                                </div>
                            </div>
                        </div><br>
                    </div>	'; 
                    
                    } else{
						echo '<a href="'  . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>'; 
                    }
					?>
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo $lang['emptypastebin']; } ?>
				</div>
            </div>
        </div>
     
     
        <!-- New Pastes -->
        <div class="tab-content" id="third-tab">
        		<div class="panel panel-default">
		  <h1 class="title is-4"><?php echo $lang['recentpastes']; ?><h1>
                <div class="columns is-multiline">
					<?php          
							$res = getRecent($con,10);
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
                            $p_tagsys = Trim($row['tagsys']);
                            $tags = htmlentities($p_tagsys, ENT_QUOTES, 'UTF-8');
                            $tagui = sandwitch($tags);
					?>
					<?php
					if ($mod_rewrite == '1') {
						echo '
                    <div class="column is-half">	                   
                        <div class="card">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content" style="overflow: hidden">
                                        <p class="title is-5"><a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a></p>
                                        <p class="subtitle is-6"><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a><br><time datetime="' . $p_date . '">' . $p_date . '</time></p>';
                                        if(strlen($p_tagsys) > 0) { 
                                            echo $tagui; 
                                        }else{ echo  ' <span class="tag is-warning">No tags</span>';}
                    echo                '</div>
                                </div>
                            </div>
                        </div><br>
                    </div>	'; 
                    
                    } else{
						echo '<a href="'  . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>'; 
                    }
					?>
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo $lang['emptypastebin']; } ?>
				</div>
            </div>
        </div>     
     
     
         <!-- Updated Pastes -->
        <div class="tab-content" id="forth-tab">
        		<div class="panel panel-default">
		  <h1 class="title is-4"><?php echo $lang['updatedgreen']; ?><h1>
                <div class="columns is-multiline">
					<?php          
							$res = recentupdate($con,10);
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
                            $p_tagsys = Trim($row['tagsys']);
                            $tags = htmlentities($p_tagsys, ENT_QUOTES, 'UTF-8');
                            $tagui = sandwitch($tags);
					?>
					<?php
					if ($mod_rewrite == '1') {
						echo '
                    <div class="column is-half">	                   
                        <div class="card">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content" style="overflow: hidden">
                                        <p class="title is-5"><a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a></p>
                                        <p class="subtitle is-6"><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a><br><time datetime="' . $p_date . '">' . $p_date . '</time></p>';
                                        if(strlen($p_tagsys) > 0) { 
                                            echo $tagui; 
                                        }else{ echo  ' <span class="tag is-warning">No tags</span>';}
                    echo                '</div>
                                </div>
                            </div>
                        </div><br>
                    </div>	'; 
                    
                    } else{
						echo '<a href="'  . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>'; 
                    }
					?>
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo $lang['emptypastebin']; } ?>
				</div>
            </div>
        </div>     
         
             <!-- Updated Pastes -->
        <div class="tab-content" id="fifth-tab">
        		<div class="panel panel-default">
		  <h1 class="title is-4"><?php echo $lang['random']; ?><h1>
                <div class="columns is-multiline">
					<?php          
							$res = getrandom($con,10);
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
                            $p_tagsys = Trim($row['tagsys']);
                            $tags = htmlentities($p_tagsys, ENT_QUOTES, 'UTF-8');
                            $tagui = sandwitch($tags);
					?>
					<?php
					if ($mod_rewrite == '1') {
						echo '
                    <div class="column is-half">	                   
                        <div class="card">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content" style="overflow: hidden">
                                        <p class="title is-5"><a href="' . $p_id . '" title="' . $titlehov . '">' . $title . ' </a></p>
                                        <p class="subtitle is-6"><a href="https://Ponepaste.org/user/' . $p_member . '">' . $p_member . '</a><br><time datetime="' . $p_date . '">' . $p_date . '</time></p>';
                                        if(strlen($p_tagsys) > 0) { 
                                            echo $tagui; 
                                        }else{ echo  ' <span class="tag is-warning">No tags</span>';}
                    echo                '</div>
                                </div>
                            </div>
                        </div><br>
                    </div>	'; 
                    
                    } else{
						echo '<a href="'  . $p_id . '" title="' . $titlehov . '">' . ucfirst($title) . '</a>'; 
                    }
					?>
					<?php }
					// Display a message if the pastebin is empty
					$query  = "SELECT count(*) as count FROM pastes";
					$result = mysqli_query( $con, $query );
					while ($row = mysqli_fetch_array($result)) {
						$totalpastes = $row['count'];
					}
					
					if ($totalpastes == '0') { echo $lang['emptypastebin']; } ?>
				</div>
            </div>
        </div>   
     

     </div>   
    </div>     
</main>


    <!-- End Panel -->
<?php } if ($privatesite == "on") { // Remove sidebar if site is private
	} else if (isset($site_ads)) {
		echo $site_ads['ads_2'];
	}	
?>
</div>
</div>
</div>
<script>
const tabSystem = {
    init(){
        document.querySelectorAll('.tabs-menu').forEach(tabMenu => {
            Array.from(tabMenu.children).forEach((child, ind) => {
                child.addEventListener('click', () => {
                    tabSystem.toggle(child.dataset.target);
                });
                if(child.className.includes('is-active')){
                    tabSystem.toggle(child.dataset.target);
                }
            });
        });
    },
    toggle(targetId){
        document.querySelectorAll('.tab-content').forEach(contentElement=>{
            contentElement.style.display = contentElement.id === targetId ? 'block' : 'none';
            document.querySelector(`[data-target="${contentElement.id}"]`).classList[contentElement.id === targetId ? 'add' : 'remove']('is-active');
        })
    },
};
// use it
tabSystem.init()
</script>