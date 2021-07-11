<?php
			$get_view_count = $conn->query("SELECT * FROM page_view");
            while ($row = $get_view_count->fetch()) {
				$total_page = Trim($row['tpage']);
				$total_un   = Trim($row['tvisit']);
			}
           
            $paste_counter = $conn->query("SELECT COUNT(id) from pastes");
            while ($row = $paste_counter->fetch()) {
            $get_paste_count = $row['COUNT(id)'];
            }
?>

<footer class="footer has-background-white" style="border-top: 1px solid #ebeaeb">
 

  <div class="container">
    <div class="columns">

  <div class="column">
    <hr>
        <div class="columns is-mobile is-centered">
        <h5 class="title is-5">Support PonePaste</h5>
        </div>
        <a href='https://liberapay.com/Ponepaste/donate' target='_blank'><img src='../img/lib.png'/></a>
        <a href='https://ko-fi.com/V7V02K3I2' target='_blank'><img src='../img/kofi.png'/></a>
  </div>
  <div class="column">
    <hr>
        <div class="columns is-mobile is-centered">
            <h5 class="title is-5">Links</h5>
        </div>
         <div class="columns">
        <div class="column">
        <ul>
        <li><a href="https://ponepaste.org/page/rules" target="_blank">Site Rules</a></li>
        <li><a href="https://ponepaste.org/page/privacy" target="_blank">Privacy Policy</a></li>
        <li><a href="mailto:admin@ponepaste.org">Contact</a></li>
        </ul>
        </div>
        <div class="column">
        <ul>
        <li><a href="https://ponepaste.org/page/tags" target="_blank">Tag Guide</a></li>
        <li><a href="https://ponepaste.org/page/transparency " target="_blank">Transparency</a></li>    
        <li><a href="https://liberapay.com/Ponepaste" target="_blank">Donate </a></li>
        </ul>
        </div>
       </div>

  </div>
  <div class="column">
    <hr>
        <div class="columns is-mobile is-centered">
        <h5 class="title is-5">Stats</h5>
        </div>
         <div class="columns">
        <div class="column">
        <ul>
         <li> <?php
            $endtime = microtime();
            $time = explode(' ', $endtime);
            $time = $time[1] + $time[0];
            $finish = $time;
            $total_time = round(($finish - $start), 4);
            echo 'Page load: '.$total_time.'s';
            ?>
         </li>
         <li>
         <?php echo 'Page Hits Today: '. $total_page .''; ?>
         </li>
         <li>
          <?php echo 'Unique Visitors Today: '. $total_un .''; ?>
         </li>
         <li>
         <?php echo 'Total Pastes: ' . $get_paste_count . ''; ?>
         </li>
        </ul>
        </div>
       </div>

  </div>
    </div>
  </div>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
      $notification = $delete.parentNode;

      $delete.addEventListener('click', () => {
        $notification.parentNode.removeChild($notification);
      });
    });
  });
</script>

<!-- Tabs function for pop up login-->
<script>
  function openTab(evt, tabName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("content-tab");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab");
    for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" is-active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " is-active";
  }
</script>

<!--Notification -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
      var $notification = $delete.parentNode;

      $delete.addEventListener('click', () => {
        $notification.parentNode.removeChild($notification);
      });
    });
  });
</script>

<!--Data Tables --> 
<script type="text/javascript" src="<?php echo '//' . $baseurl . '/theme/' . $default_theme; ?>/js/modal-fx.min.js"></script>

<!-- Hamburger menu -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
    if ($navbarBurgers.length > 0) {
      $navbarBurgers.forEach(el => {
        el.addEventListener('click', () => {
          const target = el.dataset.target;
          const $target = document.getElementById(target);
          el.classList.toggle('is-active');
          $target.classList.toggle('is-active');
        });
      });
    }
  });
</script>


<!-- Additional Scripts -->
<?php echo $additional_scripts; ?>

</body>

</html>