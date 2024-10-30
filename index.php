<?php
/*
Plugin Name: Contaqt
Plugin URI: https://login.marketingcrm.nl/site/pluginwordpress.html
Description: Plugin om de contaqt op je website te tonen
Version: 2.4
Author: Mischa Diender
Author URI: https://login.contaqt.net
*/


define('ARTICLE_VERSION', '2.4');
define('ARTICLE__MINIMUM_WP_VERSION', '2.4');
define('ARTICLE__PLUGIN_URL', plugin_dir_url(__FILE__));
define('ARTICLE__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ARTICLE_DELETE_LIMIT', 100000);
/*Define protocol*/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('PROTOCOL', $protocol);

function nieu_article_page()
{
    global $wpdb;
    /*get current site id*/
    $blog_id = get_current_blog_id();
    /*end current site id*/

    global $wpdb;
    /*get current site prefix*/
    $pre_fix_name = $wpdb->prefix;
    $table_name_new = $pre_fix_name . 'article';
    /*end*/
    /*old table name*/
    $table_name_old = 'article_view';
    /*end*/

    $old_api_key = '';
    $old_article_view = '';
    /*check table exist or not*/
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_old'") == $table_name_old) {
        $result_view = $wpdb->get_row("SELECT * FROM " . $table_name_old);
        if(count($result_view)>0) {
            $old_api_key = $result_view->api_key;
            $old_article_view = $result_view->article_view;
        }
    } else {
        /*old table not exist*/
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_new'") != $table_name_new) {
        //table not in database. Create new table
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name_new (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
	         `api_key` VARCHAR(512) NULL DEFAULT NULL,
		      `article_view` VARCHAR(512) NULL DEFAULT NULL,
              PRIMARY KEY (`id`)
          ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        /*insert old table data*/
        $wpdb->query("INSERT INTO " . $table_name_new . " (api_key,article_view) VALUES ('$old_api_key','$old_article_view'); ");
        /*delete old table*/
        $wpdb->query("DROP TABLE " . $table_name_old);

    } else {
        /*table exist*/
    }


    $company_name = '';
    $success = '';
    if (isset($_POST) && !empty($_POST)) {
        $api_key = $_POST['api_key'];

        $article_view = $_POST['article_view'];

        if (!empty($api_key)) {

            $result_view = $wpdb->query("SELECT * FROM ".$table_name_new);
            if ($result_view > 0) {
                $wpdb->query("UPDATE ".$table_name_new." SET api_key='$api_key',article_view='$article_view' ");
            } else {
                $wpdb->query("INSERT INTO ".$table_name_new." (api_key,article_view) VALUES ('$api_key','$article_view'); ");
            }
            $success = "Article api key updated successfully..";
        }
    }
    ?>
    <style>
        form {
            position: relative;
        }

        form div.left-column {
            position: absolute;
            width: 50%;
            /*left: 0;*/
            float: left;
            padding: 10px;
        }

        form div.right-column {
            position: absolute;
            width: 50%;
            left: 225px;
            float: left;
            padding: 10px;
        }

        form input, form label {
            /*float: left;*/
            /*clear: left;*/
        }

        .error_view {
            color: #ff0000;
        }

        .formcolumn {
            margin: 15px;
            border: 1px solid #444444;
            border-radius: 5px;
            min-height: 300px;
            width: 50%;
        }

        .clearfix:after {
            clear: both;
            content: ".";
            display: block;
            height: 0;
            visibility: hidden;
        }

        .clearfix {
            display: inline-block;
        }

        .clearfix {
            display: block;
        }

    </style>
    <?php
    $site_api_key = '';
    $site_article_limit = '';

    $mytestdrafts = $wpdb->get_row("SELECT * FROM ".$table_name_new);
    $site_api_key = $mytestdrafts->api_key;
    $site_article_limit = $mytestdrafts->article_view;

    ?>

    <div class="formcolumn">
        <h2 style="margin-left: 20%"><b><u>Nieuwsartikelen instellingen</u></b></h2>

        <form onsubmit="return nieu_checkValidation();" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <span class="error_view" id="error_view" style="margin-left: 10px;"></span>
            <span class="success_view" id="success_view"
                  style="color:forestgreen; margin-left: 10px; font-size: 15px;"><?php echo $success; ?></span>

            <div class="left-column" style="padding: 10px;">
                <label for="api_key"> Api Key (verplicht) : </label>
                </br>
                <input id="api_key" name="api_key" type="text"
                       value="<?php if (!empty($site_api_key)) echo $site_api_key; else echo ''; ?>"/><br/>
                <br/>


                <label for="font_size"> Aantal artikelen (verplicht) : </label>
                </br>
                <input id="article_view" name="article_view" type="text"
                       value="<?php if (!empty($site_article_limit)) echo $site_article_limit; else echo ''; ?>"/><br/>
                <br/>
                <input type="submit" name="cf-submitted" value="Opslaan">

            </div>

        </form>
        <div class="clearfix"></div>
    </div>
    <h2 style="margin-left: 10px;"><b><u>Nieuwsartikelen tonen</u></b></h2>
    <p>Je kunt de nieuwsartikelen tonen door de volgende code te plakken op de gewenste plek: [article_plugin]</p>
    <p>Je kunt een individueel artikel tonen door de volgende code op de gewenste plek te plakken:
        [article_plugin_single id="artikelnummer"]<br>hiermee
        kun je het artikelnummer vervangen door het ID van het artikel.</p>

    <p>Infschrijf knop code: [subscribe_button]</p>

<?php

}


/*admin tab view*/
function nieu_call_article()
{

    add_object_page('Nieuwsartikelen', 'Nieuwsartikelen', 'manage_options', 'sample-page', 'nieu_article_page');
}

function nieu_call_footer()
{
    ?>
    <script>
        function nieu_checkValidation() {
            document.getElementById('error_view').innerHTML = '';
            var api_key = document.getElementById('api_key').value;
            var article_view = document.getElementById('article_view').value;
            if (api_key == '') {
                document.getElementById('error_view').innerHTML = 'Please enter Api Key..';
                return false;
            }
            else if (article_view == '') {
                document.getElementById('error_view').innerHTML = 'Please enter Article Limit..';

                return false;
            } else {
                return true;
            }

        }
    </script>
<?php
}

/*register setting of plugin*/
add_action('admin_menu', 'nieu_call_article');
add_action('admin_head', 'nieu_call_footer');
/*shortcode create for the all article view site */
include_once 'shortcode_file.php';

/*shortcode create for the single article view site */
include_once 'shortcode_file_single.php';
include_once 'button.php';
?>
