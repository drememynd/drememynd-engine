<div class="row menu sm-12 sm-wrap">
    <?php foreach ($pages as $menuPage => $menuName) : ?>
        <div class="cell sm-grow <?php echo ($view->page == $menuPage ? 'is-active' : ''); ?>" >
            <a href="/<?php echo $menuPage; ?>">
                <span><?php echo $menuName; ?></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>