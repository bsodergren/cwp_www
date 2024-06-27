<?php if(!class_exists('Rain\Tpl')){exit;}?><nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm p-2 fs-4">
    <div class="container-fluid">
        <a href="#" class="logo d-flex align-items-center">
            <span class="navbar-brand h1 fs-3 position-relative ps-4">
                <?php if( defined('TITLE') ){ ?><?php echo TITLE; ?><?php } ?>

            </span>
        </a>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mr-auto " style="width:10%;">
            </ul>

            <ul class="navbar-nav me-auto w-auto">
                <?php if( defined('__DEBUG_STR__') ){ ?> <li class="nav-item "><?php echo __DEBUG_STR__; ?></li>

                <?php } ?>

                <li class="nav-item ">gsd<?php echo $current; ?></li>
                <?php if( $update>$current ){ ?>

                <li class="nav-item auto_refresh"></li>
                <?php } ?>

            </ul>

            <ul class="navbar-nav ml-auto fs-5">
                <?php $counter1=-1;  if( isset($nav_bar_links) && ( is_array($nav_bar_links) || $nav_bar_links instanceof Traversable ) && sizeof($nav_bar_links) ) foreach( $nav_bar_links as $key1 => $value1 ){ $counter1++; ?>

                <li class="nav-item"><a class="nav-link" href="<?php echo __URL_PATH__; ?><?php echo $value1; ?>"><?php echo $key1; ?></a></li>
                <?php } ?>


                <div class="dropdown">
                    <button class="dropdown-toggle nav-link" type="button" id="imgdropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="caret drop-link">Settings</span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-dark" aria-labelledby="imgdropdown">
                        <?php $counter1=-1;  if( isset($nav_bar_dropdown) && ( is_array($nav_bar_dropdown) || $nav_bar_dropdown instanceof Traversable ) && sizeof($nav_bar_dropdown) ) foreach( $nav_bar_dropdown as $key1 => $value1 ){ $counter1++; ?>


                        <?php if( $value1=='divider' ){ ?>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <?php }else{ ?>

                        <li class="nav-item"><a class="dropdown-item " href="<?php echo __URL_PATH__; ?><?php echo $value1; ?>"><i class="fa fa-user pr-2"><?php echo $key1; ?></i></a></li>
                        <?php } ?>

                        <?php } ?>




                        <li class="nav-item">
                            <SPAN class="dropdown-item ">
                                <?php if( $update>$current ){ ?>

                                <i class="fa fa-user text-warning pr-2"> <?php echo $update; ?> available</i>
                                <?php }else{ ?>

                                <?php echo $current; ?>

                                <?php } ?>

                            </SPAN>
                        </li>

                    </div>
                </div>
            </ul>
        </div>
    </div>
</nav>