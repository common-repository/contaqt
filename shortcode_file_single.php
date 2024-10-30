<?php
include_once 'ContaqtApi.php';

function nieu_html_form_code_first($id)
{
    global $wpdb;

    $site_api_key = '';

    /*========= check old table and insert in new table==========*/
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
        if (count($result_view) > 0) {
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
    /*========================end=========================*/


    $mytestdrafts = $wpdb->get_row("SELECT * FROM ".$table_name_new);
    $site_api_key = $mytestdrafts->api_key;
    $site_article_limit = $mytestdrafts->article_view;

    $obj = new ContaqtApi;
    $newsArticleData = $obj->plugplayNewsArticle($site_api_key, '1', $id)->response;
    $newsArticleData = json_decode($newsArticleData);
    if ($newsArticleData->status == '0') {
        echo "Api key is invalid..";
    }
    ?>
    <head>
        <meta charset="utf-8"/>
        <!-- Set the viewport width to device width for mobile -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Article Site</title>
        <style>
            @media (max-width: 600px) {
                #article_img_single {
                    display: none;
                }
            }

            #description_id {
                padding-left: 10px;
                padding-right: 10px;
            }
        </style>

    </head>


    <div class="wrapper">
        <div id="main_plugin">
            <?php
            if (isset($newsArticleData->payload)) {
                foreach ($newsArticleData->payload as $article) { ?>
                    <div id="main_parent">
                        <p style="margin-left: 10px; font-size: 18px;"><b><?php echo $article->title; ?></b></p>

                        <div class="parent">

                            <div class="text" id="description_id">
                                <?php echo $article->description; ?>
                            </div>
                            <div class="img" id="article_img_single" style="width: 22%;">
                                <img src="<?php echo $article->afbeelding; ?> "/>
                            </div>
                        </div>
                    </div>
                    <!-- code need to be added-->
                    <?php
                    if (!empty($article->question)): ?>
                        <iframe id='reaction_iframe'
                                src='<?php echo PROTOCOL . "login.contaqt.net/api/getReactionForm/article/" . $article->id; ?>'
                                style='background:none; width: 100%;overflow: auto; border: none; height: 350px;'></iframe>
                    <?php endif ?>

                    <!-- end added code-->


                <?php }
            } else {
                echo "Please check api key.No such data found..";
            }
            ?>

            <div class="clearfix"></div>
        </div>
    </div>
<?php

}

/*add shortcode */
function nieu_cf_shortcode_first($data)
{
    if (!empty($data['id'])) {
        $id = $data['id'];
    } else {
        $id = $data['id'];
    }

    ob_start();
    nieu_html_form_code_first($id);
    return ob_get_clean();
}


/*register shortcode */
add_shortcode('article_plugin_single', 'nieu_cf_shortcode_first');

?>