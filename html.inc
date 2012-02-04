<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Interviewstreet Tweet Density</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <style type="text/css">
      img {border-width: 0}
      * {font-family:'Lucida Grande', sans-serif;}
    </style>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      <?php if (isset($hours)) { ?> 
        google.setOnLoadCallback(drawChart);
      <?php } ?>
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Hour');
        data.addColumn('number', 'Tweets');
        data.addRows([
        <?php foreach ($hours as $hour => $tweets) { ?>
          <?php echo "['$hour', $tweets],"?>
        <?php } ?>
        ]);
        var options = {
          width: 640, height: 240,
          title: '<?php echo $twitter_user; ?>\'s Tweet Density',
          hAxis: {title: 'Hour'}
        };
  
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div>
      <h2>Welcome to wukan's Tweet Density.</h2>

      <p>This site is a basic showcase of tweetdensity challenge on <a href="http://interviewstreet.com">interviewstreet.com</a>. If you are having issues try <a href='./clearsessions.php'>clearing your session</a>.</p>
      
      <?php if (isset($menu)) { ?>
        <?php echo $menu; ?>
      <?php } ?>
    </div>

    <div <?php if(!isset($verified)) { echo 'style="display:none;"'; } ?>> 
      <form action="index.php" method="get">
        <label for="twitter_user">Input Twitter User:</label>
        <input type="text" id="twitter_user" name="handle" />
        <input type="submit" />
        <span style="font-size:12px; font-style:italic; color:gray">(for example, interviewstreet)</span>
      </form>
    </div>

    <div <?php if(!isset($content)) { echo 'style="display:none;"';}?> >
      <?php if (isset($status_text)) { ?>
        <?php echo '<h3>'.$status_text.'</h3>'; ?>
      <?php } ?>
      <p>
        <pre>
          <?php print_r($content); ?>
        </pre>
      </p>
    </div>

    <div id="chart_div" <?php if(!isset($hours)) { echo 'style="display:none;"'; }?> ></div>

    <hr />
    <p>
      The site is based on project twitteroauth from @<a href='http://twitter.com/abraham'>abraham</a>.
      <a href='http://github.com/abraham/twitteroauth'>Source Code</a> &amp;
      <a href='http://wiki.github.com/abraham/twitteroauth/documentation'>Documentation</a>
    </p>
  </body>
</html>
