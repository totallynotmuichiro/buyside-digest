<style>


	.collapsible {
  background-color:#EFEFEF !important;
  color: white;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;white-space: break-spaces;
}

.active, .collapsible:hover {
  background-color: #555;
}

.content {
  padding: 18px;
  display: none;
  overflow: hidden;
  background-color: #fff;border:1px solid #e3e3e3;
}
	.bsdDate{font-family: "Nunito", Sans-serif;
    font-size: 15px;
    font-weight: 600;
    fill: var(--e-global-color-0dd1a0a );
    color: var(--e-global-color-0dd1a0a );
    background-color: transparent;
    background-image: linear-gradient(140deg, var(--e-global-color-accent ) 0%, #623601 100%);
    box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
    padding: 12px 20px 12px 20px;
}
	.bsdTitle{color: var(--e-global-color-secondary );
    font-family: "Nunito", Sans-serif;
    font-size: 24px;
    font-weight: 700;margin-left:15px;}
	.excerpt{color:#000000;display: inline-block;margin-top:15px;}
</style>



<?php 
$post_id = get_the_ID();
$title = get_the_title();
$theContent = get_the_content();
$date = get_the_date();
$theExcerpt = get_the_excerpt();

echo "<button type='button' class='collapsible'><span class='bsdDate'>$date</span><span class='bsdTitle'> $title</span> <br/><span class='excerpt'>$theExcerpt</span></button>";
echo "<div class='content'> $theContent </div>";


?>
