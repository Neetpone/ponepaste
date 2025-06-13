<script>
    (function () {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        let deadline = "May 28, 2021 20:00:00 GMT+0",
            countDown = new Date(deadline).getTime(),
            x = setInterval(function () {

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

    <div class="container">
        <div class="bd-duo">
            <div class="bd-lead">
                <!-- Start Row -->
                <div class="row">
                    <!-- Start Panel -->
                    <?php if ($site_is_private) { // Site permissions ?>
                        <div class="col-md-12">
                            <div class="panel panel-default" style="padding-bottom: 100px;">
                                <div class="error-pages">
                                    <i class="fa fa-lock fa-5x" aria-hidden="true"></i>
                                    <h1>This pastebin is private.</h1>
                                </div>
                            </div>
                        </div>

                    <?php } else { ?>

                    <?php }
                    if (!$site_is_private) { ?>
                    <div class="notification is-warning">
                        <p>No event right now!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- End Panel -->
<?php } ?>
