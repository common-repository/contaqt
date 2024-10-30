<?php
include('RestClient.php');

class ContaqtApi
{
    public $baseUrl;
    public $headers;

    public function __construct(){


            $this->baseUrl = PROTOCOL."login.contaqt.net/";
    }

    public function plugplayFaq($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayFaq?api_key='.$api_key);
        return $result;
    }

    public function plugplayIndex($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayIndex?api_key='.$api_key);
        return $result;
    }

    public function plugplayAdvieskeuzeReview($api_key,$limit)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayAdvieskeuzeReview?api_key='.$api_key.'&limit='.$limit);
        return $result;
    }

    public function plugplayArticle( $api_key,$limit,$searchQuery = '' )
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        if(!empty($searchQuery)){
            $searchQuery = urlencode($searchQuery);
            $result = $api->get('api/list?api_key='.$api_key.'&limit='.$limit.'&searchQuery='.$searchQuery);
        }
        else{
            $result = $api->get('api/list?api_key='.$api_key.'&limit='.$limit);
        }
        
        return $result;
    }

    public function plugplayNewsArticle($api_key,$limit,$articleId)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/list?api_key='.$api_key.'&limit='.$limit.'&articleId='.$articleId);
        return $result;
    }

    public function plugplayWebsite($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugPlayWebsite?api_key='.$api_key);
        
        return $result;
    }

    public function plugplayWebsitePage($api_key,$websiteId)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayWebsitePage?api_key='.$api_key.'&websiteId='.$websiteId);

        return $result;
    }

    public function plugplayPage($api_key,$pageId,$searchQuery = '')
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));

        if(!empty($searchQuery)){
            $result = $api->get('api/plugplayPage?api_key='.$api_key.'&searchQuery='.$searchQuery);
        }
        else{
            $result = $api->get('api/plugplayPage?api_key='.$api_key.'&pageId='.$pageId);
        }
        return $result;
    }

    public function plugplayNewsletterArticle($api_key,$newsletterId)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayNewsletterArticle?api_key='.$api_key.'&newsletterId='.$newsletterId);
        return $result;
    }
    public function plugplayAdvieskeuzeKantoor($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayAdvieskeuzeKantoor?api_key='.$api_key);
        
        return $result;
    }

    public function plugplayBanner($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayBanner?api_key='.$api_key);

        return $result;
    }

    public function plugplayDownload($api_key)
    {
        $api = new RestClient(array(
            'base_url' => $this->baseUrl,
        ));
        $result = $api->get('api/plugplayDownload?api_key='.$api_key);

        return $result;
    }

}