<?php
ob_start();
function nieu_html_form_code_button()
{
    ob_start();
    global $wpdb;
    $validationJs = PROTOCOL . 'login.contaqt.net/js/subscribe/jquery.validate.min.js';
    $subscribeJs = PROTOCOL . 'login.contaqt.net/js/subscribe/subscribescript-1.js';


    /*========= check old table and insert in new table==========*/
    $pre_fix_name = $wpdb->prefix;
    $table_name_new = $pre_fix_name . 'article';
    /*end*/
    /*old table name*/
    $table_name_old = 'article_view';
    /*end*/

    $site_api_key = '';
    $mytestdrafts = $wpdb->get_results("SELECT * FROM " . $table_name_new);
    foreach ($mytestdrafts as $value) {
        $site_api_key = $value->api_key;
        $site_article_limit = $value->article_view;
    }

    header('Access-Control-Allow-Origin: *');

    /*echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>';*/
    echo '<script src="' . $validationJs . '" type="text/javascript"></script>';
    echo '<script src="' . $subscribeJs . '" type="text/javascript"></script>';


    echo "<script>
    hs([{'api_key':'" . $site_api_key . "'},{'mailinglist_id':''}] , [{'text':'inschrijven'},{'backgroundColor':'green'}])
</script>";
    echo "<span class='subscribe_button_control'>
                           <button class='subscribe_button'>inschrijven</button>
                    </span>";
    echo ob_get_clean();
}

/*add shortcode */
function nieu_cf_shortcode_button()
{

    ob_start();
    nieu_html_form_code_button();
    return ob_get_clean();
}


/*register shortcode */
add_shortcode('subscribe_button', 'nieu_cf_shortcode_button');

?>