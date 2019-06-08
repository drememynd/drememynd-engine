<div class="row menu sm-12 sm-nowrap">
    <?php foreach ($pages as $menuPage => $menuName) : ?>
        <a class="cell sm-grow align-left align-center <?php echo ($view->page == $menuPage ? 'is-active' : ''); ?>" href="/<?php echo $menuPage; ?>">
            <span><?php echo $menuName; ?></span>
        </a>
    <?php endforeach; ?>
</div>