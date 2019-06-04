<div class="column menu">
    <?php foreach ($pages as $menuPage => $menuName) : ?>
        <div class="cell shrink <?php echo ($view->page == $menuPage ? 'is-active' : ''); ?>" >
            <a href="/<?php echo $menuPage; ?>">
                <span><?php echo $menuName; ?></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>