<?php
include_once 'ContaqtApi.php';

function nieu_html_form_code()
{
    global $wpdb;
    $site_api_key = '';
    $actual_link = PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $back_link = explode('?', $actual_link);
    if (strpos($actual_link, '?') !== false) {
        $actual_link = $actual_link . "&";
    } else {
        $actual_link = $actual_link . "?";
    }

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
    if (isset($_GET['article_id']) && !empty($_GET['article_id'])) {
        $newsArticleData = $obj->plugplayNewsArticle($site_api_key, '1', $_GET['article_id'])->response;
    }
    else if (isset($_GET['id']) && !empty($_GET['id'])) {
        $newsArticleData = $obj->plugplayNewsArticle($site_api_key, '1', $_GET['id'])->response;
    }
    else {
        $newsArticleData = $obj->plugplayArticle($site_api_key, $site_article_limit)->response;
    }

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

    </head>
    <body>
    <div class="wrapper">
        <div id="main_plugin">
            <?php
            if (isset($_GET['article_id']) || isset($_GET['id'])) {
                if (isset($newsArticleData->payload)) {
                    foreach ($newsArticleData->payload as $article) { ?>

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

                        <div id="main_parent">
                            <a class="nieu_image" href="<?php echo $back_link[0]; ?>"
                               style=" margin: 10px;">
                                Terug naar alle artikelen
                            </a>
                            <hr style="margin: 5px;">
                            <p style="margin-left: 10px; font-size: 18px;"><b><?php echo $article->title; ?></b></p>

                            <div class="parent">
                                <div class="text" id="description_id">
                                    <?php echo $article->description; ?>
                                </div>
                                <div class="img" style="width: 22%;" id="article_img_single">
                                    <img src="<?php echo $article->afbeelding; ?>"/>
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
            } else {
                ?>
                <style>
                    #nieu_items .nieu_item .nieu_item-content {
                        min-height: 230px;
                    }
                </style>
                <div id="nieu_items" class="row-fluid">
                    <?php
                    if (isset($newsArticleData->payload)) {
                        foreach ($newsArticleData->payload as $article) { ?>
                            <div class=" nieu_item masonry-brick">
                                <div class="nieu_picture">
                                    <a class="nieu_image" href="<?php echo $actual_link . "article_id=" . $article->id; ?>">
                                        <img alt="" src="<?php echo $article->afbeelding; ?>">
                                    </a>

                                    <div class="nieu_item-content">
                                        <a class="nieu_image" title="Title"
                                           href="<?php echo $actual_link . "article_id=" . $article->id; ?>"
                                           style="color:#000; font-size: 12px;"><b><?php echo $article->title; ?></b>
                                        </a>

                                        <div class="nieu_description">
                                            <p style="color:#000 !important; text-align: justify;margin: 0px;"> <?php echo substr($article->intro, 0, 200); ?> </p>
                                        </div>
                                        <div class="meta">
								<span style="color:#000;">
									<i class="icon-calendar"></i>
                                    <?php echo $article->pubdate; ?>
								</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo "No such data found!";
                    }
                    ?>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div>
    </div>

    </body>
<?php
}

/*add shortcode */
function nieu_cf_shortcode()
{
    ob_start();
    nieu_html_form_code();
    return ob_get_clean();
}

function nieu_loadCssFile()
{
    wp_enqueue_script('jquery');
    wp_register_style('nieu_stylesheet_css', plugins_url('/css/nieu_stylesheet.css', __FILE__));
    wp_enqueue_style('nieu_stylesheet_css');
}

/*register shortcode */
add_shortcode('article_plugin', 'nieu_cf_shortcode');

add_action('wp_head', 'nieu_loadCssFile');

?>