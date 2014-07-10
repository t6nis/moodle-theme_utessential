<div class="row-fluid" id="middle-blocks">
    <div class="span4">
        <!-- Advert #1 -->
        <div class="service">
            <!-- Icon & title. Font Awesome icon used. -->
            <h5><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing1icon ?>"></i> <?php echo get_string('marketing1title', 'theme_utessential'); ?></span></h5>
            <?php if ($hasmarketing1image) { ?>
            	<div class="marketing-image1"></div>
            <?php } ?>
            
            <?php echo get_string('marketing1content', 'theme_utessential'); ?>
            <p class="marketing-button"><a href="<?php echo get_string('marketing1url', 'theme_utessential'); ?>" id="button" target="_blank"><?php echo get_string('marketing1button', 'theme_utessential'); ?></a></p>
        </div>
    </div>
    
    <div class="span4">
        <!-- Advert #2 -->
        <div class="service">
            <!-- Icon & title. Font Awesome icon used. -->
            <h5><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing2icon ?>"></i> <?php echo get_string('marketing2title', 'theme_utessential'); ?></span></h5>
            <?php if ($hasmarketing2image) { ?>
            	<div class="marketing-image2"></div>
            <?php } ?>
            
            <?php echo get_string('marketing2content', 'theme_utessential'); ?>
            <p class="marketing-button"><a href="<?php echo get_string('marketing2url', 'theme_utessential'); ?>" id="button" target="_blank"><?php echo get_string('marketing3button', 'theme_utessential'); ?></a></p>
        </div>
    </div>
    
    <div class="span4">
        <!-- Advert #3 -->
        <div class="service">
            <!-- Icon & title. Font Awesome icon used. -->
            <h5><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing3icon ?>"></i> <?php echo get_string('marketing3title', 'theme_utessential'); ?></span></h5>
            <?php if ($hasmarketing3image) { ?>
            	<div class="marketing-image3"></div>
            <?php } ?>
            
            <?php echo get_string('marketing3content', 'theme_utessential'); ?>
            <p class="marketing-button"><a href="<?php echo get_string('marketing3url', 'theme_utessential'); ?>" id="button" target="_blank"><?php echo get_string('marketing3button', 'theme_utessential'); ?></a></p>
        </div>
    </div>
</div>