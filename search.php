<?php
//Keep the same search term from last search, on first run, use placeholder
if(isset($_POST['q'])){$searchterm = $_POST['q'];}
  else {$searchterm = "SEARCHTERM";}
//Keep the same depth preference from last search
if(isset($_POST['deep'])){
  $checkornot="checked";
  $depth = "file";
}
  else{
    $checkornot="";
    $depth = "folder";
  }
$likelydrive = "Some Drive";
?>

<!DOCTYPE html>
<html lang="en">
<head>
            <meta charset="utf-8" />
        <title>search_archive_405 | Brand Calibre</title>
        <meta name="generator" content="GravCMS" />
<meta name="description" content="Design, Refine, Approach and Market. Strategic sales &amp; marketing consultancy to transform your brand communication." />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="icon" type="image/png" href="/user/themes/twenty/images/favicon.png" />

                                                                        <link href="/user/plugins/markdown-notices/assets/notices.css" type="text/css" rel="stylesheet" />
<link href="/user/plugins/form/assets/form-styles.css" type="text/css" rel="stylesheet" />
<link href="/user/plugins/login/css/login.css" type="text/css" rel="stylesheet" />
<link href="/user/themes/twenty/assets/css/main.css" type="text/css" rel="stylesheet" />

<style>
ul.sleek{
  font-size: 12px;
  color: black;
  text-align: left;
}
li.hoverlight{
  cursor: pointer;
}
li.hoverlight:hover{
  color: rgb(200, 100, 150);
}
</style>
            </head>
    <body class="">

                    <header id="header" class="">
  <nav id="nav">
    <ul>
                                        <li class="">
            <a href="/">
              Home
            </a>
          </li>
                                                  <li class="">
            <a href="/services">
              Services
            </a>
          </li>
                                                                  <li><a href="contact" class="button special">Contact</a></li>
    </ul>
  </nav>
</header>

                    <article id="main">
    <header class="special container" <?php if($searchterm == "SEARCHTERM"){echo "style=\"display: none;\"";}else{echo "style=\"visibility: hidden;\"";}?>>
        <span class="icon fa-thumbs-up"></span>
        <h2><?php echo "Content is mostly on " . $likelydrive; ?></h2>
    </header>

    <section class="wrapper style4 special container 75%">
        <div class="content">





<h1>Search Archive Drives</h1>

  <form name="search" id="search" method="post">
      <input type="text" name="q" value=<?php if($searchterm == "SEARCHTERM"){echo "\"\" placeholder=\"Filename\"";}else{echo "\"" . $searchterm . "\"";}?>><br>
    <label>Deep search (files too - takes longer)</label>
      <input type="checkbox" name="deep" value="deep"<?php echo $checkornot; ?>><br><br>
      <input type="submit" name="submit" value="submit" class="button special">
  </form>
<br><br><br>
<?php
//mySQL connection credentials
  $servername = "10.169.0.152";
  $username = "brandcal_archive";
  $password = "Ut@#,5yP(?GM3P";
  $dbname = "brandcal_archive";

// Start connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$drivecount = array(0);

$showtables = "SHOW tables";
$result = $conn->query($showtables);
$doublenumbertables = $result->num_rows;
$numbertables = $doublenumbertables / 2;

//If we're going file deep, check these tables, full of file names.
if($depth=="file"){
    //ALL FILES
    $tables = array();
    for ($i = 1; $i <= $numbertables; $i++){
      $stringy = (string) $i;
      $tablenamepretty = "BC_Archive_" . str_pad($stringy, 2, '0', STR_PAD_LEFT);
      array_push($tables, $tablenamepretty);
    }
    $county = 1; //set to control table id through the foreach loop.
    foreach($tables as $table){
      $sql = "SELECT * FROM `" . $table . "` WHERE `Filename` LIKE '%" . $searchterm . "%'";
      $result = $conn->query($sql);
      $resultcount=0; //count results up from zero per table
      if ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
      $closethis = 1;
      echo "<h3 style=\"padding-top: 85px;\" id=\"table" . $county . "\">Files in " . $table . "</h3><ul class='sleek'>";
      }
      else {$closethis = 0;}
      while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
          echo '<li>';
          foreach($row as $field) {
              echo $field;
          }
          echo '</li>';
          $resultcount += 1;//add these findings to the number of found items in this table.
      }
      $drivecount[] = $resultcount;
      if ($closethis == 1){echo "</ul><hr><br />";}
      $county = $county + 1; //increment to control table id through the foreach loop so we can pabel each table differently.
    }
}
//otherwise check these tables, which only have directories in them
else{
  //FOLDERS ONLY
  $tables = array();
  for ($i = 1; $i <= $numbertables; $i++){
    $stringy = (string) $i;
    $tablenamepretty = "FAST_BC_Archive_" . str_pad($stringy, 2, '0', STR_PAD_LEFT);
    array_push($tables, $tablenamepretty);
  }
  $county = 1; //set to control table id through the foreach loop.
  foreach($tables as $table){
    $sql = "SELECT * FROM `" . $table . "` WHERE `Filename` LIKE '%" . $searchterm . "%'";
    $result = $conn->query($sql);
    $resultcount=0; //count results up from zero per table
    $closethis = 0;
    $once = 1;
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
      if ($once == 1){
        echo "<h3 style=\"padding-top: 85px;\" id=\"table" . $county . "\">Folders in " . $table . "</h3> <ul class='sleek'>";
        $once = 0;
      }
      echo '<li>';
      foreach($row as $field) {
        echo $field;
      }
      echo '</li>';
      $resultcount += 1; //add these findings to the number of found items in this table.
      $closethis = 1;
    }
    $drivecount[] = $resultcount;
    if ($closethis == 1){echo "</ul><hr><br />";}
    $county = $county + 1; //increment to control table id through the foreach loop so we can pabel each table differently.
  }
}

$conn->close();
?>





</div>

    </section>

</article>

                    <footer id="footer">

    <ul class="icons">
                     <li><a href="https://www.facebook.com/brandcalibreltd/" target="_BLANK" class="icon circle fa-facebook"><span class="label">facebook</span></a></li>
                     <li><a href="http://www.twitter.com/brandcalibre" target="_BLANK" class="icon circle fa-twitter"><span class="label">twitter</span></a></li>
            </ul>

    <ul class="copyright">
        <li>&copy; 2017 Brand Calibre Limited</li><li> <a href=""></a></li>
    </ul>

</footer>
                                                                                                                                    <script src="/system/assets/jquery/jquery-2.x.min.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/jquery.dropotron.min.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/jquery.scrolly.min.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/jquery.scrollgress.min.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/skel.min.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/util.js" type="text/javascript" ></script>
<script src="/user/themes/twenty/assets/js/main.js" type="text/javascript" ></script>

<?php
$maxs = array_keys($drivecount, max($drivecount));
$likelydrive = "v4_BC_Archive_0" . $maxs[0];
?>

<header class="special container" style="position: absolute; top: 140px; width: 100%;<?php if($searchterm == "SEARCHTERM"){echo " display: none;";}?>">
        <span class="icon fa-thumbs-up"></span>
        <h2 id="topsearch">Content is mostly on <?php echo $likelydrive;?></h2>
        <ul style="position: fixed; left: 50px; text-align: left;">
        <li class="hoverlight" OnClick='document.getElementById("topsearch").scrollIntoView();'>Search Again ^</li>
<?php
  $count = 0;
foreach($drivecount as $drive){
  if ($drive == 0){;}
else{echo "<li class=\"hoverlight\" OnClick='document.getElementById(\"table" . $count . "\").scrollIntoView();'>BC_Archive_0" . $count . " --> " . $drive . "matches</li>";}
  $count = $count + 1;
}
?>
        </ul>
</header>
    </body>
</html>
