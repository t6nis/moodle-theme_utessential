<?php 
    if ($hasslideshow && !strpos($checkuseragent, 'MSIE 7')) { // Hide slideshow for IE7
?>
    <div id="da-slider" class="da-slider <?php echo $hideonphone ?>" style="background-position: 8650% 0%;">

    <?php if ($hasslide1) { ?>
        <div class="da-slide">
            <h2><?php echo get_string('slide1title', 'theme_utessential'); ?></h2>
            <?php if ($hasslide1caption) { ?>
                <p><?php echo get_string('slide1caption', 'theme_utessential'); ?></p>
            <?php } ?>
            <?php if ($hasslide1url) { ?>
                <a href="<?php echo get_string('slide1url', 'theme_utessential'); ?>" class="da-link" target="_blank"><?php echo get_string('readmore','theme_utessential')?></a>
            <?php } ?>
            <?php if ($hasslide1image) { ?>
            <div class="da-img"><img src="<?php echo $slide1image ?>" alt="<?php echo $slide1 ?>"></div>
            <?php } ?>
        </div>
    <?php } ?>
    

    <?php if ($hasslide2) { ?>
        <div class="da-slide">
            <h2><?php echo get_string('slide2title', 'theme_utessential'); ?></h2>
            <?php if ($hasslide2caption) { ?>
                <p><?php echo get_string('slide2caption', 'theme_utessential'); ?></p>
            <?php } ?>
            <?php if ($hasslide2url) { ?>
                <a href="<?php echo get_string('slide2url', 'theme_utessential'); ?>" class="da-link" target="_blank"><?php echo get_string('readmore','theme_utessential')?></a>
            <?php } ?>
            <?php if ($hasslide2image) { ?>
            <div class="da-img"><img src="<?php echo $slide2image ?>" alt="<?php echo $slide2 ?>"></div>
            <?php } ?>
        </div>
    <?php } ?>
    

    <?php if ($hasslide3) { ?>
        <div class="da-slide">
            <h2><?php echo get_string('slide3title', 'theme_utessential'); ?></h2>
            <?php if ($hasslide3caption) { ?>
                <p><?php echo get_string('slide3caption', 'theme_utessential'); ?></p>
            <?php } ?>
            <?php if ($hasslide3url) { ?>
                <a href="<?php echo get_string('slide3url', 'theme_utessential'); ?>" class="da-link" target="_blank"><?php echo get_string('readmore','theme_utessential')?></a>
            <?php } ?>
            <?php if ($hasslide3image) { ?>
            <div class="da-img"><img src="<?php echo $slide3image ?>" alt="<?php echo $slide3 ?>"></div>
            <?php } ?>
        </div>
    <?php } ?>
    

    <?php if ($hasslide4) { ?>
        <div class="da-slide">
            <h2><?php echo get_string('slide4title', 'theme_utessential'); ?></h2>
            <?php if ($hasslide4caption) { ?>
                <p><?php echo get_string('slide4caption', 'theme_utessential'); ?></p>
            <?php } ?>
            <?php if ($hasslide4url) { ?>
                <a href="<?php echo get_string('slide4url', 'theme_utessential'); ?>" class="da-link" target="_blank"><?php echo get_string('readmore','theme_utessential')?></a>
            <?php } ?>
            <?php if ($hasslide4image) { ?>
            <div class="da-img"><img src="<?php echo $slide4image ?>" alt="<?php echo $slide4 ?>"></div>
            <?php } ?>
        </div>
    <?php } ?>
    
    <?php if ($hasslide5) { ?>
        <div class="da-slide">
            <h2><?php echo get_string('slide5title', 'theme_utessential'); ?></h2>
            <?php if ($hasslide5caption) { ?>
                <p><?php echo get_string('slide5caption', 'theme_utessential'); ?></p>
            <?php } ?>
            <?php if ($hasslide5url) { ?>
                <a href="<?php echo get_string('slide5url', 'theme_utessential'); ?>" class="da-link" target="_blank"><?php echo get_string('readmore','theme_utessential')?></a>
            <?php } ?>
            <?php if ($hasslide5image) { ?>
            <div class="da-img"><img src="<?php echo $slide5image ?>" alt="<?php echo $slide5 ?>"></div>
            <?php } ?>
        </div>
    <?php } ?>

        <nav class="da-arrows">
            <span class="da-arrows-prev"></span>
            <span class="da-arrows-next"></span>
        </nav>
        
    </div>
<?php } ?>