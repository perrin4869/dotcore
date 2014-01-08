<?php

$os = DotCoreOS::GetInstance();
$messages = $os->GetMessages();
$admin_is_logged_in = $os->IsAdminLoggedIn();
$admin = $os->GetAdmin();
$user = DotCoreAdminBLL::GetUser($admin);
if($admin_is_logged_in)
{
    $current_program = $os->GetRequestedProgram();
    if($current_program == NULL)
    {
        $title = $messages['PanelWelcome'] . ' ' . $user->getUsername();
    }
    else
    {
        $current_program->ProcessInput();
        $title = $current_program->GetTitle();
        $header_content .= $current_program->GetHeaderContent();
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:dotcore="http://www.dotcore.co.il/ns">
<head>
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $messages['PanelTitle']; ?></title>

<?php
$header_path = $_SERVER['DOCUMENT_ROOT'].DotCoreConfig::$ADMIN_URL.'header.php';
if(file_exists($header_path))
{
    include($header_path);
}
echo $header_content;
?>

</head>
<body>
<div id="wrapper">
<?php if($admin_is_logged_in) { ?>
<div id="header">
    <div id="logo">
        <h1><a href="index.php"><?php echo $messages['PanelTitle']; ?></a></h1>
        <p><?php echo $messages['PanelSubTitle']; ?></p>
    </div>
</div>
<!-- end #header -->
<div id="menu">
    <ul>
        <li>
            <a href="/" target="_blank">
                <img alt="<?php echo $messages['PanelViewWebsite']; ?>" src="<?php echo DotCoreConfig::$GLOBAL_ADMIN_URL; ?>images/view_website.gif" />
                <span><?php echo $messages['PanelViewWebsite']; ?></span>
            </a>
        </li>
        <?php if($admin_is_logged_in) { ?>
        <li>
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?logoff=true'; ?>">
                <img alt="<?php echo $messages['AdminTitleLogoff']; ?>" src="<?php echo DotCoreConfig::$GLOBAL_ADMIN_URL; ?>images/logoff.png" /><span><?php echo $messages['PanelLogoff']; ?></span>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<!-- end #menu -->
<div id="page">
    <div id="sidebar">
        <?php if($admin_is_logged_in) {
            $menu_path = $_SERVER['DOCUMENT_ROOT'].DotCoreConfig::$ADMIN_URL.'menu.php';
            if(file_exists($menu_path))
            {
                include($menu_path);
            }
        } ?>
        </div>
        <!-- end #sidebar -->
        <div id="content">
                <div class="post">
                    <h1 class="title"><a><?php echo $title; ?></a></h1>
                    <div class="entry">

                    <p class="feedback">
                        <?php
                            $errors = $os->GetErrors();
                            $count_errors = count($errors);
                            for($i = 0; $i < $count_errors; $i++)
                            {
                                if($i > 0)
                                {
                                    echo '<br />';
                                }
                                echo $errors[$i];
                            }
                        ?>
                    </p>
            <?php } ?>

            <?php if(!$admin_is_logged_in) { ?>
            <div id="LoginWrapper">
                <h1 class="title"><?php echo $messages['PanelAdminEntry']; ?></h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div class="feedback">
                        <?php
                            $errors = $os->GetErrors();
                            $count_errors = count($errors);
                            for($i = 0; $i < $count_errors; $i++)
                            {
                                if($i > 0)
                                {
                                    echo '<br />';
                                }
                                echo $errors[$i];
                            }
                        ?>
                    </div>
                    <div class="FormField">
                        <div class="LabelDiv"><label for="login_username"><?php echo $messages['LabelUsername']; ?>:</label></div>
                        <div class="InputDiv"><input type="text" value="<?php echo $_REQUEST['login_username']; ?>" name="login_username" id="login_username" /></div>
                    </div>
                    <div class="FormField">
                        <div class="LabelDiv"><label for="login_password"><?php echo $messages['LabelPassword']; ?>:</label></div>
                        <div class="InputDiv"><input type="password" name="login_password" id="login_password" /></div>
                    </div>

                    <div>
                        <input type="submit" value="Login" name="login_submit" />
                        <img  alt="Lock" src="<?php echo DotCoreConfig::$GLOBAL_ADMIN_URL; ?>images/encrypted-256.png" id="LockImage" />
                    </div>
                </form>
            </div>
            <?php } else {

                if($current_program != NULL)
                {
                    echo $current_program->GetContent();
                }
                else
                {
                    $shortcuts_path = $_SERVER['DOCUMENT_ROOT'].DotCoreConfig::$ADMIN_URL.'shortcuts.php';
                    if(file_exists($shortcuts_path))
                    {
                        include($shortcuts_path);
                    }
                }

             } ?>
            <?php if($admin_is_logged_in) { ?>
                </div>
            </div>
        </div>
    <!-- end #content -->
    <div style="clear: both;">&nbsp;</div>
<?php } ?>
</div>
</div>
<!-- end #page -->
</body>
</html>