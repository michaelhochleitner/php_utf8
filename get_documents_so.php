<?php
function number_of_pages(){
    $url = "https://a.wordpress.page/wp-json/wp/v2/posts?page=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $headers = explode("\r\n", $header);
    $xwpTotalPages = $headers[15];
    return explode(":",$xwpTotalPages)[1];
}
function get_pages(int $number_of_pages){
    $pages = array();
    for ($x = 1; $x <= $number_of_pages; $x++) {
        $url = "https://a.wordpress.page/wp-json/wp/v2/posts?page=1" . $x;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $ch = curl_init();
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        echo "pushing page $x to array\n";
        array_push($pages,$body);
    }
    return $pages;
}

function write_to_files($pages){
    foreach ($pages as $page){
        $page = html_entity_decode($page,ENT_COMPAT, 'UTF-8');
        $dict = json_decode($page);
        foreach ($dict as $post){
            $id = $post->id;
            $fp = fopen($id . '.json', 'w');
            fwrite($fp, json_encode($post));
            fclose($fp);
        }
    }
}

write_to_files(get_pages(number_of_pages()));
