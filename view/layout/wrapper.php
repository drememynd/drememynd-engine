<?php /* ENGINE */ ?>
<div id="wrapper" class="column cell-padding-y sm-grow">

    <div class="row cell-padding-x sm-shrink sm-wrap md-nowrap">
        <?php echo $header; ?>
    </div>

    <div class="row cell-padding-x sm-shrink hidden-lg">
        <?php echo $topMenu; ?>
    </div>

    <div id="center-area" class="row cell-padding-x sm-grow">
        <div id="menu-area" class="cell lg-3 show-only-lg">
            <?php echo $leftMenu; ?>
        </div>
        <div id="content-area" class="cell sm-12 lg-9">
            <?php echo $content; ?>
        </div>
    </div>

    <div id="footer-area" class="row sm-shrink align-middle align-center cell-padding-x" >
        <div id="footer-cell" class="cell sm-12 align-middle align-center">
            <?php echo $footer; ?>
        </div>
    </div>

</div>